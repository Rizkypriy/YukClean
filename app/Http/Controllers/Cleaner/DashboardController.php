<?php
// app/Http/Controllers/Cleaner/DashboardController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use Illuminate\Http\Request; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        // Get available tasks within radius
        $availableTasks = $this->getAvailableTasks($cleaner);
        
        // Get current active task
        $currentTask = $cleaner->currentTask;
        
        // Get today's tasks
        $todayTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->whereDate('task_date', today())
            ->orderBy('start_time')
            ->get();
        
        // Get performance data
        $performance = $cleaner->performance()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();
        
        // Get recent activities
        $recentTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('cleaner.dashboard.index', compact(
            'cleaner',
            'availableTasks',
            'currentTask',
            'todayTasks',
            'performance',
            'recentTasks'
        ));
    }

    private function getAvailableTasks(Cleaner $cleaner)
    {
        // This is a simplified example - in real app, you'd query orders
        // that are within cleaner's radius and not yet assigned
        
        return collect([
            (object)[
                'customer_name' => 'Ibu Siti Aminah',
                'service_type' => 'Pembersihan Rumah Regular',
                'address' => 'Jl. Merpati No. 45, Jakarta Selatan',
                'distance_km' => 0.8,
                'task_date' => now()->addDays(2),
                'start_time' => '10:00',
                'task_type' => 'regular',
            ],
            (object)[
                'customer_name' => 'Bapak Ahmad Rizki',
                'service_type' => 'Pembersihan Deep Cleaning',
                'address' => 'Jl. Kenari No. 12, Jakarta Selatan',
                'distance_km' => 1.2,
                'task_date' => now()->addDays(2),
                'start_time' => '14:00',
                'task_type' => 'deep_cleaning',
            ],
            (object)[
                'customer_name' => 'Ibu Dewi Lestari',
                'service_type' => 'Pembersihan Kamar Mandi',
                'address' => 'Jl. Cendrawasih No. 8, Jakarta Selatan',
                'distance_km' => 2.1,
                'task_date' => now()->addDays(3),
                'start_time' => '09:00',
                'task_type' => 'bathroom',
            ],
        ])->filter(function ($task) use ($cleaner) {
            return $task->distance_km <= $cleaner->radius_km;
        })->take(5);
    }

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