<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'cleaner', 'service'])
            ->when($request->status, function($query, $status) {
                if ($status !== 'all') {
                    return $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(15);

        $orderStats = [
            'waiting' => Order::where('status', 'waiting')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'orderStats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'cleaner', 'service']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // Validasi dan update order
    }

    public function assignCleaner(Request $request, Order $order)
    {
        $request->validate([
            'cleaner_id' => 'required|exists:cleaners,id'
        ]);

        $order->cleaner_id = $request->cleaner_id;
        $order->status = 'in_progress';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Petugas berhasil ditugaskan'
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:waiting,in_progress,completed,cancelled'
        ]);

        $order->status = $request->status;
        
        if ($request->status == 'completed') {
            $order->completed_at = now();
        }
        
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diupdate'
        ]);
    }

    public function export($format)
    {
        // Export orders ke CSV/Excel
    }

     /**
     * Display monitoring page.
     */
    public function monitoring(Request $request)
    {
        // Statistik status
        $statusStats = [
            'waiting' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::whereIn('status', ['on_progress', 'in_progress'])->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        $totalOrders = array_sum($statusStats);

        // Persentase untuk chart
        $percentages = [
            'waiting' => $totalOrders > 0 ? round(($statusStats['waiting'] / $totalOrders) * 100, 1) : 0,
            'in_progress' => $totalOrders > 0 ? round(($statusStats['in_progress'] / $totalOrders) * 100, 1) : 0,
            'completed' => $totalOrders > 0 ? round(($statusStats['completed'] / $totalOrders) * 100, 1) : 0,
            'cancelled' => $totalOrders > 0 ? round(($statusStats['cancelled'] / $totalOrders) * 100, 1) : 0,
        ];

        // Pekerjaan aktif (sedang berjalan)
        $activeJobs = Order::with(['user', 'cleaner', 'service'])
            ->whereIn('status', ['on_progress', 'in_progress'])
            ->latest()
            ->take(5)
            ->get();

        // Riwayat pekerjaan selesai
        $completedJobs = Order::with(['user', 'cleaner', 'service'])
            ->where('status', 'completed')
            ->latest()
            ->paginate(10);

        return view('admin.orders.monitoring', compact(
            'statusStats',
            'totalOrders',
            'percentages',
            'activeJobs',
            'completedJobs'
        ));
    }

}