<?php
// app/Http/Controllers/Cleaner/TaskController.php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // TAMBAHKAN INI

class TaskController extends Controller
{
    /**
     * Display a listing of all tasks for the cleaner.
     */
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

    /**
     * Display the specified task.
     */
    public function show(CleanerTask $task)
    {
        // Pastikan task milik cleaner yang sedang login
        if ($task->cleaner_id !== Auth::guard('cleaner')->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini');
        }
        
        return view('cleaner.tasks.show', compact('task'));
    }

    /**
     * Accept an available task.
     */
    public function accept(CleanerTask $task)
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();

        // Validasi: task harus berstatus available
        if ($task->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak tersedia untuk diambil'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Hitung jarak jika ada koordinat
            $distance = null;
            if ($cleaner->latitude && $cleaner->longitude && $task->latitude && $task->longitude) {
                $distance = $this->calculateDistance(
                    $cleaner->latitude, $cleaner->longitude,
                    $task->latitude, $task->longitude
                );
            }

            // Update task
            $task->update([
                'cleaner_id' => $cleaner->id,
                'status' => 'assigned',
                'distance_km' => $distance,
            ]);

            // Update cleaner status
            $cleaner->update(['status' => 'on_task']);

            // Update order jika ada relasi
            if ($task->order) {
                $task->order->update(['cleaner_id' => $cleaner->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil diambil',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display current active task.
     */
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

    /**
     * Update task status (on_the_way, in_progress, completed).
     */
    public function updateStatus(Request $request, CleanerTask $task)
    {
        // Pastikan task milik cleaner yang sedang login
        if ($task->cleaner_id !== Auth::guard('cleaner')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke tugas ini'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:on_the_way,in_progress,completed',
        ]);

        DB::beginTransaction();
        try {
            $data = ['status' => $request->status];
            
           // Catat waktu berdasarkan status
        if ($request->status === 'on_the_way') {
            $data['started_at'] = now();
        } elseif ($request->status === 'in_progress') {
            $data['started_at'] = now(); // Jika sebelumnya belum dicatat
        } elseif ($request->status === 'completed') {
            $data['completed_at'] = now();
                
                // Update order status
                if ($task->order) {
                    $task->order->update(['status' => 'completed']);
                }
                
                // Update cleaner stats
                $task->cleaner->increment('total_tasks');
                $task->cleaner->update(['status' => 'available']);
            }
            
            $task->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'status' => $task->status
            ]);

       } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Update status error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal update status: ' . $e->getMessage()
        ], 500);
    }
    }

    /**
     * Display task history (completed tasks).
     */
    public function history()
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();
        
        $tasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);
        
        return view('cleaner.tasks.history', compact('tasks'));
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
}