<?php
// app/Http/Controllers/Cleaner/TaskController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $tasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->orderBy('task_date', 'desc')
            ->orderBy('start_time')
            ->paginate(10);
        
        return view('cleaner.tasks.index', compact('tasks'));
    }

    public function show(CleanerTask $task)
    {
        $this->authorize('view', $task);
        
        return view('cleaner.tasks.show', compact('task'));
    }

    public function accept(Request $request, $taskId)
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        // In real app, you'd find the order and create cleaner_task
        // This is simplified
        
        DB::transaction(function () use ($cleaner) {
            $task = CleanerTask::create([
                'cleaner_id' => $cleaner->id,
                'order_id' => 1, // dummy
                'customer_name' => 'Ibu Siti Aminah',
                'address' => 'Jl. Merpati No. 45',
                'service_type' => 'Pembersihan Rumah Regular',
                'task_type' => 'regular',
                'task_date' => now()->addDays(2),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'distance_km' => 0.8,
                'status' => 'assigned',
            ]);
            
            $cleaner->update(['status' => 'on_task']);
        });

        return redirect()->route('cleaner.tasks.current')
            ->with('success', 'Tugas berhasil diambil');
    }

    public function current()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $currentTask = $cleaner->currentTask;
        
        if (!$currentTask) {
            return redirect()->route('cleaner.dashboard')
                ->with('info', 'Tidak ada tugas aktif');
        }
        
        return view('cleaner.tasks.current', compact('currentTask'));
    }

    public function updateStatus(Request $request, CleanerTask $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => 'required|in:on_the_way,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $task) {
            $data = ['status' => $request->status];
            
            if ($request->status === 'on_the_way') {
                $data['started_at'] = now();
            } elseif ($request->status === 'completed') {
                $data['completed_at'] = now();
                
                // Update cleaner stats
                $task->cleaner->increment('total_tasks');
                $task->cleaner->updatePerformance();
            }
            
            $task->update($data);
            
            // Update cleaner status
            if ($request->status === 'completed') {
                $task->cleaner->update(['status' => 'available']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diperbarui',
        ]);
    }

    public function history()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $completedTasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);
        
        return view('cleaner.tasks.history', compact('completedTasks'));
    }
}