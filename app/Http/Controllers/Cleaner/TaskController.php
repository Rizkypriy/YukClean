<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        if ($task->cleaner_id !== Auth::guard('cleaner')->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini');
        }
        
        return view('cleaner.tasks.show', compact('task'));
    }

    /**
     * FIX: Menerima tugas tanpa error
     */
    public function accept(CleanerTask $task)
    {
        /** @var Cleaner $cleaner */
        $cleaner = Auth::guard('cleaner')->user();

        if ($task->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak tersedia untuk diambil'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Hitung jarak dengan proteksi nilai null
            $distance = null;
            if (!empty($cleaner->latitude) && !empty($cleaner->longitude) && 
                !empty($task->latitude) && !empty($task->longitude)) {
                $distance = $this->calculateDistance(
                    $cleaner->latitude, $cleaner->longitude,
                    $task->latitude, $task->longitude
                );
            }

            // 1. Update task ke status assigned
            $task->update([
                'cleaner_id' => $cleaner->id,
                'status' => 'assigned',
                'distance_km' => $distance,
            ]);

            // 2. FIX: Update cleaner status (Gunakan satu status yang valid, misal: 'on_task')
            $cleaner->update(['status' => 'on_task']);

            // 3. Update order jika ada relasi
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
            Log::error('Accept Task Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tugas: ' . $e->getMessage()
            ], 500);
        }
    }

   public function current()
{
    /** @var Cleaner $cleaner */
    $cleaner = Auth::guard('cleaner')->user();
    $currentTask = $cleaner->currentTask;
    
    // HAPUS redirect, tetap kirim variabel meskipun null
    return view('cleaner.tasks.current', compact('currentTask'));
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:on_the_way,in_progress,completed'
        ]);

        $cleanerId = Auth::guard('cleaner')->id();

        try {
            $task = CleanerTask::with('order')->findOrFail($id);

            if ($task->cleaner_id !== $cleanerId) {
                return response()->json(['success'=>false,'message'=>'Unauthorized'],403);
            }

            $task->status = $request->status;

            if ($request->status === 'on_the_way') {
                optional($task->order)->update(['status' => 'on_progress']);
            }

            if ($request->status === 'in_progress') {
                $task->started_at = now();
                optional($task->order)->update(['status' => 'on_progress']);
            }

            if ($request->status === 'completed') {
                $task->completed_at = now();
                optional($task->order)->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'progress' => 100
                ]);

                // Reset status cleaner ke available
                $cleaner = Auth::guard('cleaner')->user();
                $cleaner->update(['status' => 'available']);
            }

            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated'
            ]);

        } catch (\Exception $e) {
            Log::error('Update status error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function updateProgress(Request $request, CleanerTask $task)
    {
        if ($task->cleaner_id !== Auth::guard('cleaner')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['progress' => 'required|integer|min:0|max:100']);

        DB::beginTransaction();
        try {
            $task->update([
                'progress' => $request->progress,
                'status' => 'in_progress'
            ]);
            
            if ($task->order) {
                $task->order->update(['progress' => $request->progress]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Progress diperbarui',
                'progress' => $task->progress
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update progress error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal update progress'], 500);
        }
    }

    public function history()
    {
        $cleaner = Auth::guard('cleaner')->user();
        $tasks = CleanerTask::where('cleaner_id', $cleaner->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);
        
        return view('cleaner.tasks.history', compact('tasks'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
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