<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #00bca4;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 22px;
            color: #00bca4;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
        
        .meta-info {
            text-align: right;
            font-size: 9px;
            color: #888;
            margin-bottom: 20px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #00bca4;
            border-left: 4px solid #00bca4;
            padding-left: 10px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        
        .section-content {
            padding-left: 14px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #00bca4;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .two-column {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }
        
        .column:first-child {
            padding-left: 0;
        }
        
        .column:last-child {
            padding-right: 0;
        }
        
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        
        ul li {
            padding: 4px 0;
            padding-left: 15px;
            position: relative;
        }
        
        ul li:before {
            content: "•";
            color: #00bca4;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        .tech-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #eee;
        }
        
        .tech-item strong {
            color: #00bca4;
            min-width: 120px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-primary {
            background-color: #00bca4;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #888;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .highlight-box {
            background-color: #f0fdf4;
            border: 1px solid #00bca4;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .box-title {
            font-weight: bold;
            color: #00bca4;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>{{ $subtitle }}</h2>
    </div>
    
    <div class="meta-info">
        <p>Tanggal: {{ $date }}</p>
        <p>Dibuat: {{ $generated_at }}</p>
    </div>
    
    <!-- Tech Stack Section -->
    <div class="section">
        <div class="section-title">1. Teknologi yang Digunakan</div>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th>Teknologi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tech_stack as $component => $technology)
                    <tr>
                        <td><strong>{{ $component }}</strong></td>
                        <td>{{ $technology }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- User Types Section -->
    <div class="section">
        <div class="section-title">2. Jenis Pengguna Sistem</div>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Peran</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user_types as $user)
                    <tr>
                        <td><span class="badge badge-primary">{{ $user['role'] }}</span></td>
                        <td>{{ $user['description'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Models Section -->
    <div class="section">
        <div class="section-title">3. Struktur Model (Eloquent)</div>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($models as $model => $description)
                    <tr>
                        <td><strong>{{ $model }}</strong></td>
                        <td>{{ $description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="section">
        <div class="section-title">4. Fitur Utama</div>
        <div class="section-content">
            <div class="two-column">
                <div class="column">
                    <div class="highlight-box">
                        <div class="box-title">Fitur User (Pelanggan)</div>
                        <ul>
                            @foreach($user_features as $feature)
                            <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="column">
                    <div class="highlight-box">
                        <div class="box-title">Fitur Admin</div>
                        <ul>
                            @foreach($admin_features as $feature)
                            <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Methods -->
    <div class="section">
        <div class="section-title">5. Metode Pembayaran</div>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Metode</th>
                        <th>Provider</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payment_methods as $payment)
                    <tr>
                        <td><strong>{{ $payment['method'] }}</strong></td>
                        <td>{{ $payment['providers'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Database Tables -->
    <div class="section">
        <div class="section-title">6. Struktur Database</div>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Nama Tabel</th>
                        <th>Kolom</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table => $columns)
                    <tr>
                        <td><strong>{{ $table }}</strong></td>
                        <td>{{ implode(', ', $columns) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Status Values -->
    <div class="section">
        <div class="section-title">7. Status Sistem</div>
        <div class="section-content">
            <div class="two-column">
                <div class="column">
                    <strong>Status Pesanan:</strong>
                    <ul>
                        @foreach($order_status as $status)
                        <li>{{ $status }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="column">
                    <strong>Level Member:</strong>
                    <ul>
                        @foreach($member_levels as $level)
                        <li>{{ $level }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis dari analisis codebase proyek YukClean</p>
        <p>&copy; {{ date('Y') }} YukClean - Aplikasi Layanan Cleaning Berbasis Web</p>
    </div>
</body>
</html>
