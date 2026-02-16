<?php

namespace App\Http\Controllers;

use App\Models\Bundle;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::where('is_active', true)->get();
        return view('bundles.index', compact('bundles'));
    }

    public function show(Bundle $bundle)
    {
        return view('bundles.show', compact('bundle'));
    }

    public function createBundle(Bundle $bundle)
{
    if (!$bundle->is_active) {
        return redirect()->route('home')->with('error', 'Paket tidak tersedia');
    }
    
    return view('orders.create-bundle', compact('bundle'));
}
}