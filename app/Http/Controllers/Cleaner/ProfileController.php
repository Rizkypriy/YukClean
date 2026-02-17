<?php
// app/Http/Controllers/Cleaner/ProfileController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display cleaner profile.
     */
    public function index()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        // Statistik umum
        $totalTasks = CleanerTask::where('cleaner_id', $cleaner->id)->count();
        $completedTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->count();
        
        // Rating dan kepuasan (dari model cleaner)
        $rating = $cleaner->rating ?? 5.0;
        $satisfaction = $cleaner->satisfaction_rate ?? 98;
        
        // Aktivitas terakhir
        $recentTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        // Performa bulan ini
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyPerformance = $cleaner->performance()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
        
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
        
        return view('cleaner.profile.index', compact(
            'cleaner',
            'totalTasks',
            'completedTasks',
            'rating',
            'satisfaction',
            'recentTasks',
            'monthlyPerformance',
            'monthlyCompleted',
            'activeDays'
        ));
    }

    /**
     * Show edit profile form.
     */
    public function edit()
    {
        /** @var Cleaner $cleaner */
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

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($cleaner->avatar) {
                Storage::disk('public')->delete($cleaner->avatar);
            }
            
            // Store new avatar
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
            'current_password.required' => 'Password saat ini harus diisi',
            'current_password.current_password' => 'Password saat ini salah',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
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
        $cleaner = Auth::guard('cleaner')->user();
        
        // Monthly statistics for chart
        $monthlyStats = $cleaner->performance()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        // Task statistics
        $taskStats = [
            'total' => $cleaner->total_tasks,
            'this_month' => CleanerTask::where('cleaner_id', $cleaner->id)
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'avg_rating' => $cleaner->rating,
            'satisfaction' => $cleaner->satisfaction_rate,
        ];
        
        return view('cleaner.profile.statistics', compact('cleaner', 'monthlyStats', 'taskStats'));
    }
}