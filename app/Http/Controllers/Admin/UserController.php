<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cleaner; // ✅ TAMBAHKAN INI
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users and cleaners.
     */
    public function index(Request $request)
    {
        // ===== AMBIL DATA USER =====
        $users = User::when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                if ($status === 'active') {
                    return $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    return $query->where('is_active', false);
                }
            })
            ->withCount('orders') // ✅ Tambah jumlah pesanan
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // ===== AMBIL DATA CLEANER =====
        $cleaners = Cleaner::when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->status, function($query, $status) {
                if ($status === 'active') {
                    return $query->where('status', 'available');
                } elseif ($status === 'inactive') {
                    return $query->whereIn('status', ['offline', 'busy']);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('users', 'cleaners'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['orders' => function($query) {
            $query->with('service')->latest()->limit(10);
        }]);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'city', 'address']));

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'User diaktifkan' : 'User dinonaktifkan'
        ]);
    }
}