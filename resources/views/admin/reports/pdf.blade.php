{{-- resources/views/admin/reports/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan Yuk Clean</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #0d9488;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .period {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
            color: #0d9488;
        }
        .summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .summary-item {
            width: 23%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 10px;
        }
        .summary-item .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 20px;
            font-weight: bold;
            color: #0d9488;
        }
        .summary-item .sub-value {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th {
            background-color: #0d9488;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #0d9488;
            margin: 20px 0 10px;
            border-bottom: 1px solid #0d9488;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            background-color: #e9ecef;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .progress {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }
        .progress-bar {
            height: 8px;
            background-color: #0d9488;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Yuk Clean</h1>
        <p>Laporan Kinerja Mingguan</p>
    </div>

    <div class="period">
        Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
    </div>

    {{-- Ringkasan --}}
    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Pesanan</div>
            <div class="value">{{ $summary['total_orders'] }}</div>
            <div class="sub-value">{{ $summary['completed_orders'] }} selesai | {{ $summary['cancelled_orders'] }} batal</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            <div class="sub-value">Rata-rata: Rp {{ number_format($summary['avg_order_value'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">User Baru</div>
            <div class="value">{{ $summary['new_users'] }}</div>
            <div class="sub-value">Pelanggan baru</div>
        </div>
        <div class="summary-item">
            <div class="label">Cleaner Baru</div>
            <div class="value">{{ $summary['new_cleaners'] }}</div>
            <div class="sub-value">Petugas baru</div>
        </div>
    </div>

    {{-- Pesanan per Hari --}}
    <div class="section-title">Detail Pesanan per Hari</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Pesanan</th>
                <th>Selesai</th>
                <th>Dibatalkan</th>
                <th>Persentase Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyOrders as $order)
            @php
                $completionRate = $order->total > 0 ? round(($order->completed / $order->total) * 100) : 0;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</td>
                <td class="text-right">{{ $order->total }}</td>
                <td class="text-right text-success">{{ $order->completed }}</td>
                <td class="text-right text-danger">{{ $order->cancelled }}</td>
                <td style="width: 200px;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="progress" style="width: 150px;">
                            <div class="progress-bar" style="width: {{ $completionRate }}%"></div>
                        </div>
                        <span>{{ $completionRate }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Layanan Terpopuler --}}
    <div class="section-title">Layanan Terpopuler</div>
    <table>
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Jumlah Pesanan</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxCount = $popularServices->max('total');
            @endphp
            @forelse($popularServices as $service)
            @php
                $percentage = $maxCount > 0 ? round(($service->total / $maxCount) * 100) : 0;
            @endphp
            <tr>
                <td>{{ $service->service->name ?? 'Unknown' }}</td>
                <td class="text-right">{{ $service->total }}</td>
                <td style="width: 200px;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="progress" style="width: 150px;">
                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span>{{ $percentage }}%</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">Tidak ada data layanan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Cleaner Terbaik --}}
    <div class="section-title">Cleaner Terbaik Minggu Ini</div>
    <table>
        <thead>
            <tr>
                <th>Nama Cleaner</th>
                <th>Tugas Selesai</th>
                <th>Rating</th>
                <th>Kepuasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topCleaners as $cleaner)
            <tr>
                <td>{{ $cleaner->name }}</td>
                <td class="text-right">{{ $cleaner->tasks_count }}</td>
                <td class="text-right">{{ number_format($cleaner->rating, 1) }} ⭐</td>
                <td class="text-right">{{ $cleaner->satisfaction_rate ?? 0 }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada data cleaner</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>&copy; {{ date('Y') }} Yuk Clean. All rights reserved.</p>
    </div>
</body>
</html>