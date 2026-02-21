<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        
        // Validasi data sesuai dengan form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'icon_name' => 'nullable|string|in:ruangan,kamar,ruang tamu,toilet,dapur',
            'color' => 'nullable|string|max:7',
            'duration' => 'required|integer|min:1',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Generate slug dari name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Handle checkboxes (pastikan nilainya boolean)
        $validated['is_popular'] = $request->has('is_popular') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Simpan ke database
        $service = Service::create($validated);

        if ($service) {
            return redirect()->route('admin.services.index')
                ->with('success', 'Layanan berhasil ditambahkan.');
        } else {
            return back()->with('error', 'Gagal menambahkan layanan.')
                ->withInput();
        }
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        // Validasi data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'icon_name' => 'nullable|string|in:ruangan,kamar,ruang tamu,toilet,dapur',
            'color' => 'nullable|string|max:7',
            'duration' => 'required|integer|min:1',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Update slug jika nama berubah
        if ($service->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Handle checkboxes
        $validated['is_popular'] = $request->has('is_popular') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Update ke database
        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
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