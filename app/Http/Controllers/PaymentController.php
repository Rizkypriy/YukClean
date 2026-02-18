<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\CleanerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show payment page for an order
     */
    public function create(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::id()) {
            Log::warning('Payment create: Unauthorized access attempt', [
                'order_user_id' => $order->user_id,
                'auth_user_id' => Auth::id()
            ]);
            abort(403);
        }

        // Cek apakah sudah ada payment untuk order ini
        $payment = Payment::where('order_id', $order->id)->first();
        
        // Hitung biaya admin (misal Rp 2.000)
        $adminFee = 2000;
        
        // Hitung total
        $total = $order->total + $adminFee;

        Log::info('Payment create page accessed', [
            'order_id' => $order->id,
            'total' => $total
        ]);

        return view('user.payments.create', compact('order', 'payment', 'adminFee', 'total'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            Log::warning('Payment store: Unauthorized access attempt', [
                'order_user_id' => $order->user_id,
                'auth_user_id' => Auth::id()
            ]);
            abort(403);
        }

        $rules = [
            'payment_method' => 'required|in:ewallet,va,qris',
        ];

        // Validasi provider untuk ewallet dan va
        if ($request->payment_method === 'ewallet') {
            $rules['provider'] = 'required|in:gopay,ovo,dana,shopeepay';
        } elseif ($request->payment_method === 'va') {
            $rules['provider'] = 'required|in:bca,mandiri,bni,bri';
        }

        $request->validate($rules);

        DB::beginTransaction();
        
        try {
            // Hitung biaya admin
            $adminFee = 2000;
            $total = $order->total + $adminFee;

            // Generate payment number
            $paymentNumber = 'PAY-' . date('Ymd') . '-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT);

            // Tentukan metode pembayaran sesuai ENUM di database
            $paymentMethod = '';
            if ($request->payment_method === 'ewallet') {
                $paymentMethod = 'e-wallet';
            } elseif ($request->payment_method === 'va') {
                $paymentMethod = 'virtual_account';
            } elseif ($request->payment_method === 'qris') {
                $paymentMethod = 'qris';
            }
            
            $provider = $request->provider ?? null;

            // Buat payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_number' => $paymentNumber,
                'amount' => $order->total,
                'admin_fee' => $adminFee,
                'discount' => $order->discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'provider' => $provider,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Update status order
            $order->update(['status' => 'confirmed']);

            

            DB::commit();

            Log::info('Payment created successfully', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'payment_number' => $paymentNumber
            ]);

            return redirect()->route('user.payments.processing', $order)
                ->with('success', 'Silakan lakukan pembayaran');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     // ===== TAMBAHKAN METHOD INI =====
    /**
     * Show processing page after payment
     */
    public function processing(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        Log::info('Payment processing page accessed', [
            'order_id' => $order->id,
            'order_status' => $order->status
        ]);

        return view('user.payments.processing', compact('order'));
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        if ($payment->order->user_id !== Auth::id()) {
            Log::warning('Payment show: Unauthorized access attempt', [
                'payment_id' => $payment->id,
                'order_user_id' => $payment->order->user_id,
                'auth_user_id' => Auth::id()
            ]);
            abort(403);
        }

        Log::info('Payment show page accessed', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id
        ]);

        return view('user.payments.show', compact('payment'));
    }

    /**
     * Process payment confirmation
     * Setelah pembayaran dikonfirmasi, buat tugas untuk cleaner
     */
    public function confirm(Payment $payment)
    {
        // Log untuk debugging
        Log::info('========== PAYMENT CONFIRMATION ==========');
        Log::info('Payment ID: ' . $payment->id);
        Log::info('Order ID: ' . $payment->order_id);
        Log::info('User ID: ' . Auth::id());
        Log::info('Payment Status: ' . $payment->payment_status);

        if ($payment->order->user_id !== Auth::id()) {
            Log::error('Unauthorized: User ID mismatch', [
                'expected' => $payment->order->user_id,
                'actual' => Auth::id()
            ]);
            abort(403);
        }

        if ($payment->payment_status !== 'pending') {
            Log::error('Invalid payment status: ' . $payment->payment_status);
            return back()->with('error', 'Pembayaran sudah diproses');
        }

        DB::beginTransaction();
        
        try {
            // Update payment status
            $payment->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
            Log::info('Payment status updated to paid');

            // Update order status menjadi on_progress
            $order = $payment->order;
            
            // ===== TAMBAHKAN PENGECEKAN ORDER =====
            if (!$order) {
                Log::error('Order not found for payment ID: ' . $payment->id);
                throw new \Exception('Order tidak ditemukan');
            }

            $order->update(['status' => 'on_progress']);
            Log::info('Order status updated to on_progress');

            // ===== BUAT TUGAS UNTUK CLEANER =====
            // Tentukan service type dan name
            $serviceType = 'regular';
            $serviceName = '';
            
            if ($order->service_id) {
                $service = $order->service;
                // ===== TAMBAHKAN PENGECEKAN SERVICE =====
                if (!$service) {
                    Log::error('Service not found for ID: ' . $order->service_id);
                    throw new \Exception('Layanan tidak ditemukan');
                }
                $serviceName = $service->name;
                $serviceType = $service->type ?? 'regular';
                Log::info('Service: ' . $serviceName);
            } elseif ($order->bundle_id) {
                $bundle = $order->bundle;
                // ===== TAMBAHKAN PENGECEKAN BUNDLE =====
                if (!$bundle) {
                    Log::error('Bundle not found for ID: ' . $order->bundle_id);
                    throw new \Exception('Paket tidak ditemukan');
                }
                $serviceName = $bundle->name;
                $serviceType = 'bundle';
                Log::info('Bundle: ' . $serviceName);
            } else {
                Log::warning('No service or bundle found for order ID: ' . $order->id);
            }

            // Buat cleaner task dengan status 'available'
            $task = CleanerTask::create([
                'order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'address' => $order->address,
                'service_name' => $serviceName,
                'service_type' => $serviceType,
                'task_date' => $order->order_date,
                'start_time' => $order->start_time,
                'end_time' => $order->end_time,
                'status' => 'available', // Tersedia untuk diambil cleaner
            ]);
            Log::info('Cleaner task created with ID: ' . $task->id);

            DB::commit();
            Log::info('Transaction committed successfully');

            return redirect()->route('user.orders.index')
                ->with('success', 'Pembayaran berhasil! Tugas akan segera diproses oleh petugas.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment confirmation error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as completed (bisa dipanggil oleh admin atau sistem)
     */
    public function complete(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            Log::warning('Order complete: Unauthorized access attempt', [
                'order_user_id' => $order->user_id,
                'auth_user_id' => Auth::id(),
                'is_admin' => Auth::user()->isAdmin() ?? false
            ]);
            abort(403);
        }

        DB::beginTransaction();
        
        try {
            $order->update(['status' => 'completed']);
            Log::info('Order status updated to completed', ['order_id' => $order->id]);
            
            // Update payment status jika ada
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update(['payment_status' => 'paid']);
                Log::info('Payment status updated to paid', ['payment_id' => $payment->id]);
            }

            DB::commit();

            return redirect()->route('user.orders.completed', $order)
                ->with('success', 'Pesanan telah selesai. Terima kasih!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order complete error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menyelesaikan pesanan');
        }
    }

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->order->user_id !== Auth::id()) {
            Log::warning('Payment cancel: Unauthorized access attempt', [
                'payment_id' => $payment->id,
                'order_user_id' => $payment->order->user_id,
                'auth_user_id' => Auth::id()
            ]);
            abort(403);
        }

        if ($payment->payment_status !== 'pending') {
            Log::warning('Payment cancel: Invalid status', [
                'payment_id' => $payment->id,
                'status' => $payment->payment_status
            ]);
            return back()->with('error', 'Pembayaran sudah diproses');
        }

        $payment->update(['payment_status' => 'failed']);
        Log::info('Payment cancelled', ['payment_id' => $payment->id]);

        return redirect()->route('user.orders.show', $payment->order)
            ->with('error', 'Pembayaran dibatalkan');
    }
}