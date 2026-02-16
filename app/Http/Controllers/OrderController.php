<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Bundle;
use App\Models\Promo;
use App\Models\User;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN INI
use Carbon\Carbon;
use App\Models\Payment; // <-- TAMBAHKAN INI

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
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
            
        return view('orders.index', compact('activeOrders', 'historyOrders'));
    }

    public function create(Service $service)
    {
        if (!$service->is_active) {
            return redirect()->route('home')->with('error', 'Layanan tidak tersedia');
        }
        return view('orders.create', compact('service'));
    }

    public function createBundle(Bundle $bundle)
    {
        if (!$bundle->is_active) {
            return redirect()->route('home')->with('error', 'Paket tidak tersedia');
        }
        return view('orders.create-bundle', compact('bundle'));
    }

    public function store(OrderRequest $request)
{
    // Validasi jam
    if ($request->start_time >= $request->end_time) {
        return back()->with('error', 'Jam selesai harus setelah jam mulai.')
            ->withInput();
    }

    // CEK BOOKING DATE
    $orderDate = $request->booking_date ?? date('Y-m-d');

    // Cek ketersediaan jam
    $existingOrder = Order::where('order_date', $orderDate)
        ->where('start_time', '<=', $request->end_time)
        ->where('end_time', '>=', $request->start_time)
        ->whereIn('status', ['pending', 'confirmed', 'on_progress'])
        ->exists();

    if ($existingOrder) {
        return back()->with('error', 'Jam yang dipilih sudah dibooking. Silakan pilih jam lain.')
            ->withInput();
    }

    DB::beginTransaction();
    
    try {
        $user = User::find(Auth::id());
        
        // Calculate subtotal - BISA SERVICE ATAU BUNDLE
        $serviceId = null;
        $bundleId = null;
        $subtotal = 0;
        
        if ($request->service_id) {
            $service = Service::findOrFail($request->service_id);
            $subtotal = $service->price;
            $serviceId = $service->id;
        } elseif ($request->bundle_id) {
            $bundle = Bundle::findOrFail($request->bundle_id);
            $subtotal = $bundle->price;
            $bundleId = $bundle->id;
        } else {
            throw new \Exception('Pilih layanan atau paket');
        }

        // Calculate discount
        $discount = 0;
        $promoId = null;
        
        if ($request->filled('promo_code')) {
            $promo = Promo::where('code', $request->promo_code)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
            
            if ($promo && $subtotal >= $promo->min_purchase) {
                if ($promo->discount_type === 'percentage') {
                    $discount = $subtotal * $promo->discount_value / 100;
                } else {
                    $discount = min($promo->discount_value, $subtotal);
                }
                $promoId = $promo->id;
            }
        }

        $total = $subtotal - $discount;
        if ($total < 0) $total = 0;

        // Generate order number
        $date = date('Ymd');
        $lastOrder = Order::where('order_number', 'like', "ORD-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        $newNumber = $lastOrder ? str_pad(intval(substr($lastOrder->order_number, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        $orderNumber = "ORD-{$date}-{$newNumber}";

        // CREATE ORDER - service_id dan bundle_id bisa null
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'service_id' => $serviceId, // <-- BISA NULL
            'bundle_id' => $bundleId,   // <-- BISA NULL
            'promo_id' => $promoId,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'address' => $request->address,
            'order_date' => $orderDate,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        $user->increment('total_orders');

        DB::commit();

        return redirect()->route('payments.create', $order)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order creation failed: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['service', 'bundle', 'promo']);
        return view('orders.show', compact('order'));
    }

  public function cancel(Request $request, Order $order)
{
    if ($order->user_id !== Auth::id()) {
        abort(403);
    }

    // Cek apakah order bisa dibatalkan (pending atau confirmed)
    if (!in_array($order->status, ['pending', 'confirmed'])) {
        return back()->with('error', 'Pesanan tidak dapat dibatalkan karena statusnya ' . $order->status);
    }

    // Cek apakah sudah melewati tanggal (opsional)
    if ($order->order_date < now()->format('Y-m-d')) {
        return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah melewati tanggal layanan');
    }

    $request->validate([
        'cancellation_reason' => 'required|string|max:500',
    ]);

    DB::beginTransaction();
    
    try {
        // Jika pesanan sudah dibayar (confirmed), lakukan refund
        if ($order->status === 'confirmed') {
            $payment = Payment::where('order_id', $order->id)->where('payment_status', 'paid')->first();
            
            if ($payment) {
                // Proses refund (simulasi)
                // Di sini Anda bisa panggil API payment gateway untuk refund
                // Contoh: PaymentGateway::refund($payment->transaction_id, $payment->total);
                
                $payment->update([
                    'payment_status' => 'refunded', // Tambah status 'refunded' di enum payment_status
                    'refunded_at' => now(),
                ]);
            }
        }

        // Batalkan order
        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        DB::commit();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibatalkan' . ($order->status === 'confirmed' ? ' dan refund akan diproses' : ''));

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Cancel failed: ' . $e->getMessage());
        return back()->with('error', 'Gagal membatalkan pesanan');
    }
}

    public function checkPromo(Request $request)
    {
        $promo = Promo::where('code', $request->code)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo || $request->subtotal < $promo->min_purchase) {
            return response()->json(['valid' => false]);
        }

        $discount = $promo->discount_type === 'percentage' 
            ? $request->subtotal * $promo->discount_value / 100 
            : min($promo->discount_value, $request->subtotal);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'total' => $request->subtotal - $discount
        ]);
    }

    public function checkAvailability(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time'
    ]);

    $exists = Order::where('order_date', $request->date)
        ->where(function($query) use ($request) {
            $query->where(function($q) use ($request) {
                $q->where('start_time', '<=', $request->start_time)
                  ->where('end_time', '>', $request->start_time);
            })->orWhere(function($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>=', $request->end_time);
            })->orWhere(function($q) use ($request) {
                $q->where('start_time', '>=', $request->start_time)
                  ->where('end_time', '<=', $request->end_time);
            });
        })
        ->whereIn('status', ['pending', 'confirmed', 'on_progress'])
        ->exists();

    return response()->json([
        'available' => !$exists,
        'message' => !$exists ? 'Jam tersedia' : 'Jam sudah dibooking'
    ]);
}

/**
 * Track order progress
 */
public function track(Order $order)
{
    // Pastikan user hanya bisa track order miliknya
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
    }
    
    $order->load(['service', 'bundle']);
    
    return view('orders.track', compact('order'));
}
/**
 * Show completed order page
 */
public function completed(Order $order)
{
    // Pastikan user hanya bisa lihat order miliknya
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
    }

    // Pastikan order sudah completed
    if ($order->status !== 'completed') {
        return redirect()->route('orders.show', $order)
            ->with('error', 'Pesanan belum selesai');
    }

    return view('orders.completed', compact('order'));
}
}