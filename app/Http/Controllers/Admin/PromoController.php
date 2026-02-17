<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::latest()->paginate(15);
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promos.create');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan promo
    }

    public function edit(Promo $promo)
    {
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, Promo $promo)
    {
        // Validasi dan update promo
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus(Promo $promo)
    {
        $promo->is_active = !$promo->is_active;
        $promo->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status promo berhasil diubah'
        ]);
    }
}