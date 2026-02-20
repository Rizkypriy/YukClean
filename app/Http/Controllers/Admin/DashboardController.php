<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cleaner;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $today = Carbon::today();
        
        // Statistik untuk dashboard
        $stats = [
            // Statistik umum
            'total_users' => User::count(),
            'total_cleaners' => Cleaner::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'revenue' => Order::where('status', 'completed')->sum('total'),
            
            // Statistik harian
            'total_orders_today' => Order::whereDate('created_at', $today)->count(),
            'active_orders' => Order::whereIn('status', ['waiting', 'in_progress'])->count(),
            'completed_orders_today' => Order::whereDate('created_at', $today)->count(),
            'active_cleaners' => Cleaner::where('status', 'available')->count(),
        ];

        // 10 pesanan terbaru
        $recentOrders = Order::with(['user', 'service', 'cleaner'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 5 cleaner terbaik (berdasarkan rating)
        $topCleaners = Cleaner::whereIn('status', ['available', 'on_task'])
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'admin', 
            'stats', 
            'recentOrders',
            'topCleaners'
        ));
    }

    /**
     * Get dashboard data for AJAX requests (optional)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $today = Carbon::today();
        
        $stats = [
            'total_users' => User::count(),
            'total_cleaners' => Cleaner::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'revenue' => Order::where('status', 'completed')->sum('total'),
            'orders_today' => Order::whereDate('created_at', $today)->count(),
            'active_orders' => Order::whereIn('status', ['waiting', 'in_progress'])->count(),
            'active_cleaners' => Cleaner::where('status', 'available')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for dashboard
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'week');
        
        switch ($period) {
            case 'week':
                $data = $this->getWeeklyData();
                break;
            case 'month':
                $data = $this->getMonthlyData();
                break;
            case 'year':
                $data = $this->getYearlyData();
                break;
            default:
                $data = $this->getWeeklyData();
        }

        return response()->json($data);
    }

    /**
     * Get weekly orders data
     *
     * @return array
     */
    private function getWeeklyData()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $data[] = Order::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $days,
            'data' => $data
        ];
    }

    /**
     * Get monthly orders data
     *
     * @return array
     */
    private function getMonthlyData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                   'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = Order::whereMonth('created_at', $i)->count();
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get yearly orders data
     *
     * @return array
     */
    private function getYearlyData()
    {
        $currentYear = Carbon::now()->year;
        $lastFiveYears = range($currentYear - 4, $currentYear);
        $data = [];

        foreach ($lastFiveYears as $year) {
            $data[] = Order::whereYear('created_at', $year)->count();
        }

        return [
            'labels' => $lastFiveYears,
            'data' => $data
        ];
    }

    public function orders()
{
    $orders = Order::with(['user', 'cleaner', 'service'])->latest()->paginate(15);
    return view('admin.orders.index', compact('orders'));
}
}