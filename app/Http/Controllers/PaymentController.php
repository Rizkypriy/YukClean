<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
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
            abort(403);
        }

        // Cek apakah sudah ada payment untuk order ini
        $payment = Payment::where('order_id', $order->id)->first();
        
        // Hitung biaya admin (misal Rp 2.000)
        $adminFee = 2000;
        
        // Hitung total
        $total = $order->total + $adminFee;

        return view('payments.create', compact('order', 'payment', 'adminFee', 'total'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
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

            // ========== PERBAIKAN: Sesuaikan dengan ENUM di database ==========
            // Tentukan metode pembayaran sesuai ENUM di database
            $paymentMethod = '';
            if ($request->payment_method === 'ewallet') {
                $paymentMethod = 'e-wallet'; // ENUM mengharapkan 'e-wallet' dengan strip
            } elseif ($request->payment_method === 'va') {
                $paymentMethod = 'virtual_account'; // ENUM mengharapkan 'virtual_account'
            } elseif ($request->payment_method === 'qris') {
                $paymentMethod = 'qris'; // ENUM mengharapkan 'qris'
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
                'payment_method' => $paymentMethod, // <-- SEKARANG SESUAI ENUM
                'provider' => $provider,
                'payment_status' => 'pending',
            ]);

            // Update status order
            $order->update(['status' => 'confirmed']);

            DB::commit();

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Silakan lakukan pembayaran');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        if ($payment->order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payments.show', compact('payment'));
    }

    /**
 * Process payment confirmation (simulasi)
 */
public function confirm(Payment $payment)
{
    if ($payment->order->user_id !== Auth::id()) {
        abort(403);
    }

    if ($payment->payment_status !== 'pending') {
        return back()->with('error', 'Pembayaran sudah diproses');
    }

    DB::beginTransaction();
    
    try {
        $payment->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $payment->order->update(['status' => 'on_progress']);

        DB::commit();

        return redirect()->route('orders.show', $payment->order)
            ->with('success', 'Pembayaran berhasil! Pesanan sedang diproses.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal memproses pembayaran');
    }
}

/**
 * Mark order as completed (ini bisa dipanggil oleh admin atau sistem)
 */
public function complete(Order $order)
{
    if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
        abort(403);
    }

    DB::beginTransaction();
    
    try {
        $order->update(['status' => 'completed']);
        
        // Update payment status jika ada
        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment) {
            $payment->update(['payment_status' => 'paid']);
        }

        DB::commit();

        // Redirect ke halaman completed
        return redirect()->route('orders.completed', $order)
            ->with('success', 'Pesanan telah selesai. Terima kasih!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyelesaikan pesanan');
    }
}

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($payment->payment_status !== 'pending') {
            return back()->with('error', 'Pembayaran sudah diproses');
        }

        $payment->update(['payment_status' => 'failed']);

        return redirect()->route('orders.show', $payment->order)
            ->with('error', 'Pembayaran dibatalkan');
    }
}