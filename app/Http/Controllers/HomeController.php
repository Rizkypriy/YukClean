<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Promo;
use App\Models\Bundle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with data from database
     */
    public function index()
    {
        // Ambil data services dari database
        $services = Service::where('is_active', true)->get();
        
        // Ambil data promo aktif (max 2 untuk ditampilkan di home)
        $promos = Promo::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->limit(2)
            ->get();
        
        // Ambil data bundles aktif
        $bundles = Bundle::where('is_active', true)->get();

        // Jika tidak ada promo di database, gunakan data default
        if ($promos->isEmpty()) {
            $promos = collect([
                (object)[
                    'title' => 'Diskon 20% Pengguna Baru!',
                    'description' => 'Untuk pemesanan pertama Anda',
                    'icon' => '🏷️',
                    'background_color' => 'linear-gradient(135deg, #ff861d 0%, #f73798 100%)'
                ],
                (object)[
                    'title' => 'Promo Bundling Rumah!',
                    'description' => 'Hemat hingga 30% untuk paket lengkap',
                    'icon' => '🎁',
                    'background_color' => 'linear-gradient(135deg, #be79ff 0%, #645fff 100%)'
                ]
            ]);
        }

        return view('home.index', compact('services', 'promos', 'bundles'));
    }

    /**
     * Search services (AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $services = Service::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();

        return response()->json($services);
    }
}