<?php
// app/Http/Controllers/Cleaner/ProfileController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyPerformance = $cleaner->performance()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
        
        $recentTasks = $cleaner->tasks()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('cleaner.profile.index', compact(
            'cleaner',
            'monthlyPerformance',
            'recentTasks'
        ));
    }

    public function edit()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        return view('cleaner.profile.edit', compact('cleaner'));
    }

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

        return redirect()->route('cleaner.profile')
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password:cleaner',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $cleaner->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('cleaner.profile')
            ->with('success', 'Password berhasil diubah');
    }

    public function statistics()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $monthlyStats = $cleaner->performance()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        $taskStats = [
            'total' => $cleaner->total_tasks,
            'this_month' => $cleaner->tasks()
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'avg_rating' => $cleaner->rating,
            'satisfaction' => $cleaner->satisfaction_rate,
        ];
        
        return view('cleaner.profile.statistics', compact('cleaner', 'monthlyStats', 'taskStats'));
    }
}