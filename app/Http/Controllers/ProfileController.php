<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Constructor - ensure user is authenticated
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Get order count
        $orderCount = Order::where('user_id', $user->id)->count();
        
        // Get active orders count
        $activeOrdersCount = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'on_progress'])
            ->count();
        
        // Get completed orders count
        $completedOrdersCount = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Get total spending
        $totalSpending = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total');
        
        // Get voucher count (you can implement this logic based on your business rules)
        $voucherCount = $this->getUserVoucherCount($user);
        
        // Get unread notifications count
        $notificationCount = 3; // This could be dynamic if you implement notifications
        
        return view('profile.index', compact(
            'user', 
            'orderCount', 
            'activeOrdersCount', 
            'completedOrdersCount',
            'totalSpending',
            'voucherCount',
            'notificationCount'
        ));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
            'address.required' => 'Alamat harus diisi',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'avatar.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Show the security settings page.
     */
    public function security()
    {
        /** @var User $user */
        $user = Auth::user();
        
        return view('profile.security', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'current_password.current_password' => 'Password saat ini salah',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var User $user */
        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.security')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Display user's order history.
     */
    public function orders()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $orders = Order::with(['service', 'bundle'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('profile.orders', compact('orders'));
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // You can implement notification settings logic here
        // For example, store in a separate settings table
        
        return back()->with('success', 'Pengaturan notifikasi berhasil diperbarui!');
    }

    /**
     * Delete user account (soft delete or permanent).
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Option 1: Soft delete (if using SoftDeletes trait)
        // $user->delete();
        
        // Option 2: Deactivate account instead of deleting
        $user->update(['is_active' => false]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Akun Anda telah dinonaktifkan.');
    }

    /**
     * Get user's voucher count (example logic).
     */
    private function getUserVoucherCount(User $user): int
    {
        // Implement your voucher logic here
        // For example:
        // - Count based on member level
        // - Count based on order history
        // - Count based on promotions
        
        if ($user->member_level === 'Platinum') {
            return 5;
        } elseif ($user->member_level === 'Gold') {
            return 3;
        } else {
            return 1;
        }
    }
}