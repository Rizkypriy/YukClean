<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleanerTask;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrderTracking;
use App\Events\OrderLocationUpdated;

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
    
    // geocode alamat pelanggan untuk peta cleaner
    $customerCoords = null;
    if ($currentTask && $currentTask->address) {
        try {
            $resp = \Illuminate\Support\Facades\Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $currentTask->address,
                'format' => 'json',
                'limit' => 1,
            ]);
            if ($resp->ok() && is_array($resp->json()) && count($resp->json()) > 0) {
                $data = $resp->json()[0];
                $customerCoords = [
                    'lat' => floatval($data['lat']),
                    'lng' => floatval($data['lon']),
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Geocode cleaner current error: ' . $e->getMessage());
        }
    }

    return view('cleaner.tasks.current', compact('currentTask', 'customerCoords'));
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

                // 🔔 Broadcast status change so user can be redirected
                if (class_exists('App\Events\OrderStatusUpdated')) {
                    broadcast(new \App\Events\OrderStatusUpdated($task->order_id, 'completed'));
                }
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
            // 1. UPDATE PROGRESS DI CLEANER_TASKS
            $task->update([
                'progress' => $request->progress,
                'status' => 'in_progress'
            ]);
            
            // 2. UPDATE PROGRESS DI ORDERS (Langsung via foreign key)
            if ($task->order_id) {
                $orderUpdates = [
                    'progress' => $request->progress,
                    'status' => 'on_progress'
                ];

                // jika progress sudah 100%, tandai selesai
                if ($request->progress == 100) {
                    $orderUpdates['status'] = 'completed';
                    $orderUpdates['completed_at'] = now();
                }

                Order::where('id', $task->order_id)->update($orderUpdates);

                // jika selesai, broadcast event status
                if ($request->progress == 100 && class_exists('App\Events\OrderStatusUpdated')) {
                    broadcast(new \App\Events\OrderStatusUpdated($task->order_id, 'completed'));
                }
            }

            DB::commit();
            
            Log::info('Progress updated', [
                'task_id' => $task->id,
                'order_id' => $task->order_id,
                'progress' => $request->progress
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Progress diperbarui',
                'progress' => $request->progress
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update progress error: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'trace' => $e->getTraceAsString()
            ]);
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

    /**
     * Update lokasi cleaner untuk task tertentu.
     */
    /**
 * Update lokasi cleaner untuk task tertentu.
 */
public function updateLocation(Request $request, CleanerTask $task)
{
    // Pastikan task milik cleaner yang sedang login
    if ($task->cleaner_id !== Auth::guard('cleaner')->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    DB::beginTransaction();
    try {
        // 1. UPDATE LOKASI DI CLEANER TASK (PENTING!)
        $task->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // 2. CEK DULU APAKAH MODEL OrderTracking ADA
        if (!class_exists('App\Models\OrderTracking')) {
            // Buat tracking tanpa model dulu (simpan ke log)
            Log::info('Location update for task ' . $task->id, [
                'order_id' => $task->order_id,
                'cleaner_id' => Auth::guard('cleaner')->id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);
            
            // OPSI 1: Simpan ke tabel cleaner_locations (alternatif)
            DB::table('cleaner_locations')->updateOrInsert(
                [
                    'cleaner_id' => Auth::guard('cleaner')->id(),
                    'task_id' => $task->id
                ],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'updated_at' => now()
                ]
            );
        } else {
            // Simpan ke database tracking (pakai model)
            $tracking = OrderTracking::updateOrCreate(
                [
                    'order_id'   => $task->order_id,
                    'cleaner_id' => Auth::guard('cleaner')->id(),
                ],
                [
                    'latitude'   => $request->latitude,
                    'longitude'  => $request->longitude,
                ]
            );
        }

        // 3. BROADCAST - cek dulu apakah class event ada
        if (class_exists('App\Events\OrderLocationUpdated')) {
            try {
                broadcast(new OrderLocationUpdated(
                    $task->order_id,
                    $request->latitude,
                    $request->longitude
                ));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed: ' . $e->getMessage());
                // Abaikan error broadcast, jangan sampai rollback transaksi
            }
        }

        DB::commit();

        return response()->json([
            'success' => true, 
            'message' => 'Lokasi terkirim'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Update Lokasi Error: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Gagal mengirim lokasi: ' . $e->getMessage()
        ], 500);
    }
}
}