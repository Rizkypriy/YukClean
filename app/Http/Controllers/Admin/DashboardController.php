<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
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
            'active_orders' => Order::whereIn('status', ['waiting', 'in_progress', 'on_progress'])->count(),
            'completed_orders_today' => Order::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count(),
            'active_cleaners' => Cleaner::where('status', 'available')->count(),
            
            // 🔥 TAMBAHKAN: Pendapatan bulanan dan growth
            'monthly_revenue' => Order::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('total'),
            'revenue_growth' => $this->calculateRevenueGrowth(),
        ];

        // 🔥 TAMBAHKAN: Data untuk Chart Pesanan Per Hari (7 hari terakhir)
        $chartLabels = [];
        $chartData = [
            'total' => [],
            'completed' => [],
            'cancelled' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D');
            
            // Total pesanan per hari
            $chartData['total'][] = Order::whereDate('created_at', $date)->count();
            
            // Pesanan selesai per hari
            $chartData['completed'][] = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();
            
            // Pesanan dibatalkan per hari
            $chartData['cancelled'][] = Order::whereDate('created_at', $date)
                ->where('status', 'cancelled')
                ->count();
        }

        // 🔥 TAMBAHKAN: Data untuk Chart Layanan Populer
        $popularServices = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->with('service')
            ->select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $serviceLabels = [];
        $serviceData = [];

        foreach ($popularServices as $item) {
            if ($item->service) {
                $serviceLabels[] = $item->service->name;
                $serviceData[] = $item->total;
            }
        }

        // Jika data kosong, gunakan fallback
        if (empty($serviceLabels)) {
            $serviceLabels = ['Rumah', 'Kantor', 'Kaca', 'Karpet', 'AC'];
            $serviceData = [45, 32, 28, 22, 18];
        }

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
            'topCleaners',
            'chartLabels',      // 🔥 TAMBAHKAN
            'chartData',        // 🔥 TAMBAHKAN
            'serviceLabels',    // 🔥 TAMBAHKAN
            'serviceData'       // 🔥 TAMBAHKAN
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
            'active_orders' => Order::whereIn('status', ['waiting', 'in_progress', 'on_progress'])->count(),
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

    /**
     * Calculate revenue growth compared to previous month
     *
     * @return string
     */
    private function calculateRevenueGrowth()
    {
        $thisMonth = Order::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total');
        
        $lastMonth = Order::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('total');
        
        if ($lastMonth == 0) return '+100%';
        
        $growth = (($thisMonth - $lastMonth) / $lastMonth) * 100;
        
        return ($growth >= 0 ? '+' : '') . round($growth) . '%';
    }

    /**
     * Get weekly orders with completion status for chart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeeklyChartData()
    {
        $labels = [];
        $total = [];
        $completed = [];
        $cancelled = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D');
            
            $total[] = Order::whereDate('created_at', $date)->count();
            $completed[] = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();
            $cancelled[] = Order::whereDate('created_at', $date)
                ->where('status', 'cancelled')
                ->count();
        }

        return response()->json([
            'labels' => $labels,
            'data' => [
                'total' => $total,
                'completed' => $completed,
                'cancelled' => $cancelled
            ]
        ]);
    }

    /**
     * Get popular services data for chart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopularServicesChart()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $popularServices = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->with('service')
            ->select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($popularServices as $item) {
            if ($item->service) {
                $labels[] = $item->service->name;
                $data[] = $item->total;
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function orders()
    {
        $orders = Order::with(['user', 'cleaner', 'service'])->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }
}