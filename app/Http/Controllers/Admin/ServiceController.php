<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
{
    $services = Service::paginate(15); // Hapus orderBy
    return view('admin.services.index', compact('services'));
}

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan service
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        // Validasi dan update service
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus(Service $service)
    {
        $service->is_active = !$service->is_active;
        $service->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status layanan berhasil diubah'
        ]);
    }
}