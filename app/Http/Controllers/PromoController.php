<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;


class PromoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $promos = Promo::active()
            ->orderBy('valid_until', 'asc')  // <-- Sesuai database
            ->get();

        return view('user.promo.index', compact('promos'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $promo = Promo::where('code', $request->code)
            ->active()
            ->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa'
            ]);
        }

        if ($request->subtotal < $promo->min_purchase) {  // <-- Sesuai database
            return response()->json([
                'valid' => false,
                'message' => 'Minimal pembelian ' . $promo->formatted_min_purchase
            ]);
        }

        $discount = $promo->calculateDiscount($request->subtotal);

        return response()->json([
            'valid' => true,
            'promo' => [
                'id' => $promo->id,
                'code' => $promo->code,
                'title' => $promo->title,
                'discount' => $discount,
                'formatted_discount' => $promo->formatted_discount,
            ],
            'total' => $request->subtotal - $discount
        ]);
    }
}