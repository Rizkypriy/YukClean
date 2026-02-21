<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Cleaner;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman laporan mingguan
     */
    public function weekly(Request $request)
    {
        // Tentukan tanggal mulai dan akhir minggu
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfWeek();

        // Data Ringkasan
        $summary = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'cancelled_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'cancelled')->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total'),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_cleaners' => Cleaner::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->avg('total') ?? 0,
        ];

        // Grafik Pesanan per Hari
        $dailyOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Layanan Terpopuler
        $popularServices = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with('service')
            ->select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Cleaner Terbaik (berdasarkan rating)
        $topCleaners = Cleaner::withCount(['tasks' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
            }])
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        // Data untuk chart
        $chartLabels = $dailyOrders->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('d M');
        });

        $chartData = [
            'total' => $dailyOrders->pluck('total'),
            'completed' => $dailyOrders->pluck('completed'),
            'cancelled' => $dailyOrders->pluck('cancelled'),
        ];

        return view('admin.reports.weekly', compact(
            'summary',
            'dailyOrders',
            'popularServices',
            'topCleaners',
            'startDate',
            'endDate',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfWeek();

        $data = $this->getReportData($startDate, $endDate);

        $pdf = \PDF::loadView('admin.reports.pdf', $data);
        
        return $pdf->download('laporan-mingguan-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.pdf');
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfWeek();

        $data = $this->getReportData($startDate, $endDate);

        // Implementasi Excel export
        // Bisa menggunakan Laravel Excel atau manual CSV
        return $this->exportCsv($data, $startDate, $endDate);
    }

    /**
     * Export ke CSV (alternatif sederhana)
     */
    private function exportCsv($data, $startDate, $endDate)
    {
        $filename = 'laporan-mingguan-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['LAPORAN MINGGUAN YUK CLEAN']);
            fputcsv($file, ['Periode: ' . $data['startDate']->format('d M Y') . ' - ' . $data['endDate']->format('d M Y')]);
            fputcsv($file, []);
            
            // Ringkasan
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Pesanan', $data['summary']['total_orders']]);
            fputcsv($file, ['Pesanan Selesai', $data['summary']['completed_orders']]);
            fputcsv($file, ['Pesanan Dibatalkan', $data['summary']['cancelled_orders']]);
            fputcsv($file, ['Total Pendapatan', 'Rp ' . number_format($data['summary']['total_revenue'], 0, ',', '.')]);
            fputcsv($file, ['User Baru', $data['summary']['new_users']]);
            fputcsv($file, ['Cleaner Baru', $data['summary']['new_cleaners']]);
            fputcsv($file, ['Rata-rata Transaksi', 'Rp ' . number_format($data['summary']['avg_order_value'], 0, ',', '.')]);
            fputcsv($file, []);
            
            // Pesanan per Hari
            fputcsv($file, ['PESANAN PER HARI']);
            fputcsv($file, ['Tanggal', 'Total', 'Selesai', 'Dibatalkan']);
            foreach ($data['dailyOrders'] as $order) {
                fputcsv($file, [
                    Carbon::parse($order->date)->format('d/m/Y'),
                    $order->total,
                    $order->completed,
                    $order->cancelled
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ambil data laporan (reusable)
     */
    private function getReportData($startDate, $endDate)
    {
        $summary = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'cancelled_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'cancelled')->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total'),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_cleaners' => Cleaner::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->avg('total') ?? 0,
        ];

        $dailyOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $popularServices = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with('service')
            ->select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $topCleaners = Cleaner::withCount(['tasks' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
            }])
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        return compact('summary', 'dailyOrders', 'popularServices', 'topCleaners', 'startDate', 'endDate');
    }
}