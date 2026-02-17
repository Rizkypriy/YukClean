<?php
// app/Http/Controllers/Cleaner/DashboardController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the cleaner dashboard.
     */
    public function index()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        // Jika cleaner tidak ditemukan
        if (!$cleaner) {
            return redirect()->route('cleaner.login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // ===== STATISTIK UMUM =====
        $totalTasks = CleanerTask::where('cleaner_id', $cleaner->id)->count();
        $completedTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->count();
        
        // Rating dan kepuasan (dari model cleaner)
        $rating = $cleaner->rating ?? 5.0;
        $satisfaction = $cleaner->satisfaction_rate ?? 98;
        
        // ===== TUGAS TERSEDIA (dari database) =====
        $availableTasks = CleanerTask::where('status', 'available')
            ->whereDate('task_date', '>=', now())
            ->orderBy('task_date')
            ->orderBy('start_time')
            ->get();
        
        // Filter berdasarkan radius cleaner
        if ($cleaner->latitude && $cleaner->longitude) {
            $availableTasks = $availableTasks->filter(function ($task) use ($cleaner) {
                // Hitung jarak jika task memiliki koordinat
                if ($task->latitude && $task->longitude) {
                    $distance = $this->calculateDistance(
                        $cleaner->latitude, $cleaner->longitude,
                        $task->latitude, $task->longitude
                    );
                    return $distance <= $cleaner->radius_km;
                }
                return true; // Tampilkan semua jika tidak ada koordinat
            });
        }
        
        // ===== TUGAS AKTIF =====
        $currentTask = $cleaner->currentTask;
        
        // ===== JADWAL HARI INI =====
        $todayTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereDate('task_date', today())
            ->orderBy('start_time')
            ->get();
        
        // ===== AKTIVITAS TERAKHIR =====
        $recentTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        // ===== PERFORMANCE DATA =====
        $performance = null;
        if (method_exists($cleaner, 'performance')) {
            $performance = $cleaner->performance()
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->first();
        }
        
        // ===== PERFORMA BULAN INI (manual) =====
        $monthlyTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereMonth('task_date', now()->month)
            ->whereYear('task_date', now()->year)
            ->count();
        
        $monthlyCompleted = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereMonth('task_date', now()->month)
            ->whereYear('task_date', now()->year)
            ->where('status', 'completed')
            ->count();
        
        $activeDays = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereMonth('task_date', now()->month)
            ->whereYear('task_date', now()->year)
            ->distinct('task_date')
            ->count('task_date');
        
        return view('cleaner.dashboard.index', compact(
            'cleaner',
            'totalTasks',
            'completedTasks',
            'rating',
            'satisfaction',
            'availableTasks',
            'currentTask',
            'todayTasks',
            'recentTasks',
            'performance',
            'monthlyTasks',
            'monthlyCompleted',
            'activeDays'
        ));
    }

    /**
     * Get available tasks within cleaner's radius (metode alternatif).
     */
    private function getAvailableTasks(Cleaner $cleaner)
    {
        // Query real dari database
        return CleanerTask::where('status', 'available')
            ->whereDate('task_date', '>=', now())
            ->orderBy('task_date')
            ->orderBy('start_time')
            ->get()
            ->filter(function ($task) use ($cleaner) {
                // Filter berdasarkan radius jika ada koordinat
                if ($cleaner->latitude && $cleaner->longitude && $task->latitude && $task->longitude) {
                    $distance = $this->calculateDistance(
                        $cleaner->latitude, $cleaner->longitude,
                        $task->latitude, $task->longitude
                    );
                    return $distance <= $cleaner->radius_km;
                }
                return true; // Jika tidak ada koordinat, tampilkan semua
            })
            ->take(10);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    /**
     * Update cleaner's current location.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $cleaner->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update cleaner's status.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:available,on_task,offline',
        ]);

        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $cleaner->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status' => $cleaner->status_badge,
        ]);
    }
}