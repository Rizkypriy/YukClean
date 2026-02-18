<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Bundle;
use App\Models\Promo;
use App\Models\User;
use App\Models\Payment;
use App\Models\CleanerTask; // <-- TAMBAHKAN UNTUK INTEGRASI CLEANER
// use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $activeOrders = Order::with(['service', 'bundle'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'on_progress'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $historyOrders = Order::with(['service', 'bundle'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.orders.index', compact('activeOrders', 'historyOrders'));
    }

    /**
     * Show form for creating a service order.
     */
    public function create(Service $service)
    {
        if (!$service->is_active) {
            return redirect()->route('user.dashboard')->with('error', 'Layanan tidak tersedia');
        }
        return view('user.orders.create', compact('service'));
    }

    /**
     * Show form for creating a bundle order.
     */
    public function createBundle(Bundle $bundle)
    {
        if (!$bundle->is_active) {
            return redirect()->route('user.dashboard')->with('error', 'Paket tidak tersedia');
        }
        return view('user.orders.create-bundle', compact('bundle'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        // 1. Standarisasi Format Waktu
        $startTime = Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = Carbon::parse($request->end_time)->format('H:i:s');
        $orderDate = $request->booking_date ?? date('Y-m-d');

        // // Validasi jam
        // if ($startTime >= $endTime) {
        //     return back()->with('error', 'Jam selesai harus setelah jam mulai.')->withInput();
        // }

        // 2. Cek Overlap Jam
        $existingOrder = Order::where('order_date', $orderDate)
            ->whereIn('status', ['pending', 'confirmed', 'on_progress'])
            ->where(function($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($existingOrder) {
            return back()->with('error', 'Jam tersebut sudah dipesan. Silakan pilih jam lain.')->withInput();
        }

        DB::beginTransaction();
        try {
            /** @var User $user */
            $user = Auth::user();
            
            // 3. Hitung Subtotal
            $serviceId = null;
            $bundleId = null;
            $subtotal = 0;
            $serviceName = '';
            $serviceType = 'regular';
            
            if ($request->service_id) {
                $service = Service::findOrFail($request->service_id);
                $subtotal = $service->price;
                $serviceId = $service->id;
                $serviceName = $service->name;
                $serviceType = $service->type ?? 'regular';
            } elseif ($request->bundle_id) {
                $bundle = Bundle::findOrFail($request->bundle_id);
                $subtotal = $bundle->price;
                $bundleId = $bundle->id;
                $serviceName = $bundle->name;
                $serviceType = 'bundle';
            } else {
                throw new \Exception('Pilih layanan atau paket terlebih dahulu');
            }

            // 4. Hitung Diskon
            $discount = 0;
            $promoId = null;
            if ($request->filled('promo_code')) {
                $promo = Promo::where('code', $request->promo_code)
                    ->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();
                
                if ($promo && $subtotal >= $promo->min_purchase) {
                    $discount = ($promo->discount_type === 'percentage') 
                        ? ($subtotal * $promo->discount_value / 100) 
                        : min($promo->discount_value, $subtotal);
                    $promoId = $promo->id;
                }
            }

            $total = max(0, $subtotal - $discount);

            // 5. Generate Order Number
            $datePrefix = date('Ymd');
            $lastOrder = Order::where('order_number', 'like', "ORD-{$datePrefix}-%")->latest('id')->first();
            $sequence = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;
            $orderNumber = "ORD-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // 6. Simpan Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'service_id' => $serviceId,
                'bundle_id' => $bundleId,
                'promo_id' => $promoId,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'address' => $request->address,
                'order_date' => $orderDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'special_conditions' => $request->special_conditions,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'notes' => $request->notes,
                'status' => 'pending', // Status awal pending
            ]);

            // 7. Update total orders user
            User::where('id', $user->id)->increment('total_orders');

            DB::commit();

            return redirect()->route('user.payments.create', $order)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Store Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Check availability for a specific date and time.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            // 'end_time' => 'required'
        ]);

        $startTime = Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = Carbon::parse($request->start_time)->addHours(2)->format('H:i:s');

        $exists = Order::where('order_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed', 'on_progress'])
            ->where(function($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => !$exists ? 'Jam tersedia' : 'Jam sudah dibooking'
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['service', 'bundle', 'promo']);
        return view('user.orders.show', compact('order'));
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena statusnya ' . $order->status);
        }

        $request->validate(['cancellation_reason' => 'required|string|max:500']);

        DB::beginTransaction();
        try {
            // Jika pesanan sudah dibayar (confirmed), lakukan refund
            if ($order->status === 'confirmed') {
                $payment = Payment::where('order_id', $order->id)->where('payment_status', 'paid')->first();
                if ($payment) {
                    $payment->update([
                        'payment_status' => 'refunded',
                        'refunded_at' => now(),
                    ]);
                }
            }

            // Batalkan order
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Jika ada cleaner task yang terkait, batalkan juga
            $cleanerTask = \App\Models\CleanerTask::where('order_id', $order->id)->first();
            if ($cleanerTask && $cleanerTask->status === 'assigned') {
                $cleanerTask->update(['status' => 'cancelled']);
            }

            DB::commit();

            return redirect()->route('user.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Cancel Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membatalkan pesanan.');
        }
    }

    /**
     * Track order progress.
     */
   public function track(Order $order)
{
    if ($order->user_id !== Auth::id()) {
        abort(403);
    }
    
    // Ambil data cleaner task jika ada
    $cleanerTask = CleanerTask::where('order_id', $order->id)->with('cleaner')->first();
    
    return view('user.orders.track', compact('order', 'cleanerTask'));
}

    /**
     * Mark order as completed (after payment confirmation).
     */
    public function complete(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($order->status !== 'confirmed') {
            return back()->with('error', 'Hanya pesanan dengan status confirmed yang dapat diselesaikan');
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'completed']);

            // Update payment jika ada
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update(['payment_status' => 'paid']);
            }

            DB::commit();

            return redirect()->route('user.orders.completed', $order)
                ->with('success', 'Pesanan selesai! Terima kasih.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Complete Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyelesaikan pesanan');
        }
    }

    /**
     * Show completed order page.
     */
    public function completed(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'completed') {
            return redirect()->route('user.orders.show', $order)
                ->with('error', 'Pesanan belum selesai');
        }

        return view('user.orders.completed', compact('order'));
    }

    /**
     * Check promo code (AJAX)
     */
    public function checkPromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $promo = Promo::where('code', $request->code)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa'
            ]);
        }

        if ($request->subtotal < $promo->min_purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal pembelian Rp ' . number_format($promo->min_purchase, 0, ',', '.')
            ]);
        }

        if ($promo->discount_type === 'percentage') {
            $discount = $request->subtotal * $promo->discount_value / 100;
        } else {
            $discount = min($promo->discount_value, $request->subtotal);
        }

        return response()->json([
            'valid' => true,
            'promo' => [
                'id' => $promo->id,
                'code' => $promo->code,
                'title' => $promo->title,
                'discount' => $discount,
                'formatted_discount' => 'Rp ' . number_format($discount, 0, ',', '.'),
            ],
            'total' => $request->subtotal - $discount
        ]);
    }
}