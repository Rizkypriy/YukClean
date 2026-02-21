<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use App\Models\Review; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display cleaner profile.
     */
    public function index()
    {
        /** @var Cleaner $cleaner */
        // 1. Ambil data Cleaner langsung dari DB agar fresh
        $cleanerId = Auth::guard('cleaner')->id();
        $cleaner = Cleaner::findOrFail($cleanerId);

        // 2. 🔥 HITUNG RATING (Otomatis & Real-time)
        // Mengambil rata-rata dari tabel ulasan berdasarkan cleaner_id
        $rating = Review::where('cleaner_id', $cleaner->id)->avg('rating') ?: 0;
        $rating = round($rating, 1); // Membulatkan ke 1 desimal (contoh: 4.5)
        
        // 3. Hitung kepuasan secara dinamis
        $totalReviews = Review::where('cleaner_id', $cleaner->id)->count();
        if ($totalReviews > 0) {
            $positiveReviews = Review::where('cleaner_id', $cleaner->id)
                ->where('rating', '>=', 4)
                ->count();
            $satisfaction = round(($positiveReviews / $totalReviews) * 100);
        } else {
            $satisfaction = 0;
        }

        // --- UPDATE SYNC (Opsional tapi disarankan) ---
        // Sinkronisasi data hitungan real-time ke kolom tabel cleaners agar cache database tetap akurat
        $cleaner->update([
            'rating' => $rating,
            'satisfaction_rate' => $satisfaction
        ]);
        // ----------------------------------------------
        
        // 4. Statistik umum
        $totalTasks = CleanerTask::where('cleaner_id', $cleaner->id)->count();
        $completedTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->count();
        
        // 5. Ulasan terbaru dengan relasi user
       $recentReviews = Review::where('cleaner_id', $cleaner->getKey()) // Mengambil primary key apapun namanya
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
        // 6. Aktivitas terakhir
        $recentTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        // 7. Performa bulan ini
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyCompleted = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereMonth('task_date', $currentMonth)
            ->whereYear('task_date', $currentYear)
            ->where('status', 'completed')
            ->count();
        
        $activeDays = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereMonth('task_date', $currentMonth)
            ->whereYear('task_date', $currentYear)
            ->distinct('task_date')
            ->count('task_date');
        
        // 8. Kirim semua data ke view
        return view('cleaner.profile.index', compact(
            'cleaner',
            'totalTasks',
            'completedTasks',
            'rating',
            'satisfaction',
            'recentReviews',
            'recentTasks',
            'monthlyCompleted',
            'activeDays'
        ));
    }

    /**
     * Show edit profile form.
     */
    public function edit()
    {
        $cleaner = Auth::guard('cleaner')->user();
        return view('cleaner.profile.edit', compact('cleaner'));
    }

    /**
     * Update cleaner profile.
     */
    public function update(Request $request)
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string',
            'radius_km' => 'nullable|integer|min:1|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'radius_km' => $request->radius_km ?? 5,
        ];

        if ($request->hasFile('avatar')) {
            if ($cleaner->avatar) {
                Storage::disk('public')->delete($cleaner->avatar);
            }
            $path = $request->file('avatar')->store('cleaner-avatars', 'public');
            $data['avatar'] = $path;
        }

        $cleaner->update($data);

        return redirect()->route('cleaner.profile.index')
            ->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Update cleaner password.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password:cleaner',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'Password saat ini salah',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $cleaner->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('cleaner.profile.index')
            ->with('success', 'Password berhasil diubah');
    }

    /**
     * Display cleaner statistics.
     */
    public function statistics()
    {
        /** @var Cleaner $cleaner */
        $cleanerId = Auth::guard('cleaner')->id();
        $cleaner = Cleaner::findOrFail($cleanerId);
        
        // Monthly stats dari performance relation
        $monthlyStats = $cleaner->performance()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        // Hitung real-time rating untuk statistik agar sinkron
        $currentRating = Review::where('cleaner_id', $cleaner->id)->avg('rating') ?: 0;
        
        $taskStats = [
            'total' => CleanerTask::where('cleaner_id', $cleaner->id)->count(),
            'this_month' => CleanerTask::where('cleaner_id', $cleaner->id)
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->where('status', 'completed')
                ->count(),
            'avg_rating' => round($currentRating, 1),
            'satisfaction' => $cleaner->satisfaction_rate,
        ];
        
        return view('cleaner.profile.statistics', compact('cleaner', 'monthlyStats', 'taskStats'));
    }
}