<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    public function index(Request $request)
    {
        $cleaners = Cleaner::when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                if ($status !== 'all') {
                    return $query->where('status', $status);
                }
            })
            ->paginate(15);

        return view('admin.cleaners.index', compact('cleaners'));
    }

    public function show(Cleaner $cleaner)
    {
        $cleaner->load(['tasks' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.cleaners.show', compact('cleaner'));
    }

    public function update(Request $request, Cleaner $cleaner)
    {
        // Validasi dan update cleaner
    }

    public function destroy(Cleaner $cleaner)
    {
        $cleaner->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus(Cleaner $cleaner)
    {
        $cleaner->is_active = !$cleaner->is_active;
        $cleaner->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status cleaner berhasil diubah'
        ]);
    }

    public function statistics(Cleaner $cleaner)
    {
        // Ambil statistik cleaner
        $stats = [
            'total_jobs' => $cleaner->tasks()->count(),
            'completed_jobs' => $cleaner->tasks()->where('status', 'completed')->count(),
            'avg_rating' => $cleaner->rating,
            'total_earnings' => 0 // Hitung sesuai kebutuhan
        ];
        
        return response()->json($stats);
    }
}