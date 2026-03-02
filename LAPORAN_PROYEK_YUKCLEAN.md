# LAPORAN PROYEK YUKCLEAN

## Aplikasi Layanan Cleaning Berbasis Web

---

## DAFTAR ISI

1. [Pendahuluan](#pendahuluan)
2. [Struktur Proyek](#struktur-proyek)
3. [Backend (Laravel)](#backend-laravel)
4. [Frontend](#frontend)
5. [Database](#database)
6. [Fitur Utama](#fitur-utama)
7. [Kesimpulan](#kesimpulan)

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang

YukClean adalah aplikasi layanan cleaning (pembersihan) berbasis web yang dikembangkan menggunakan framework Laravel. Aplikasi ini memungkinkan pengguna untuk memesan layanan pembersihan rumah dengan mudah, memilih cleaner profesional, dan melakukan pembayaran secara online.

### 1.2 Teknologi yang Digunakan

| Komponen       | Teknologi                      |
| -------------- | ------------------------------ |
| Backend        | Laravel 12.x                   |
| PHP            | PHP 8.2                        |
| Database       | MySQL/SQLite                   |
| Frontend       | Blade Templates + Tailwind CSS |
| JavaScript     | Vanilla JS + Alpine.js         |
| PDF Generation | Barryvdh Laravel DomPDF        |
| Real-time      | Pusher                         |
| Build Tool     | Vite                           |

### 1.3 Jenis Pengguna

Sistem ini memiliki 3 jenis pengguna:

1. **User (Pelanggan)** - Memesan layanan cleaning
2. **Cleaner (Petugas)** - Menjalankan tugas cleaning
3. **Admin** - Mengelola sistem

---

## 2. STRUKTUR PROYEK

### 2.1 Direktori Utama

```
YukClean/
├── app/                    # Aplikasi inti Laravel
│   ├── Console/           # Command definitions
│   ├── Events/            # Event classes
│   ├── Http/              # HTTP layer (Controllers, Middleware)
│   ├── Models/           # Eloquent models
│   ├── Providers/         # Service providers
│   └── Policies/         # Authorization policies
├── bootstrap/             # Bootstrap files
├── config/                # Configuration files
├── database/              # Database (migrations, seeders)
│   ├── migrations/       # Database migrations
│   ├── seeders/          # Database seeders
│   └── factories/        # Model factories
├── public/               # Public assets
├── resources/            # Resources (views, assets)
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── views/           # Blade templates
├── routes/               # Route definitions
├── storage/              # Storage files
└── tests/                # Test files
```

### 2.2 Struktur Model (app/Models/)

| Model              | Deskripsi               |
| ------------------ | ----------------------- |
| User               | Data pengguna/pelanggan |
| Admin              | Data administrator      |
| Cleaner            | Data petugas cleaning   |
| Order              | Data pesanan layanan    |
| Service            | Jenis layanan cleaning  |
| Bundle             | Paket layanan           |
| Promo              | Kode promo/diskon       |
| Payment            | Data pembayaran         |
| Review             | Ulasan dan rating       |
| CleanerTask        | Tugas untuk cleaner     |
| CleanerPerformance | Performansi cleaner     |
| OrderTracking      | Pelacakan pesanan       |
| Category           | Kategori layanan        |

---

## 3. BACKEND (LARAVEL)

### 3.1 Controller

#### A. HomeController

Bertanggung jawab untuk halaman utama dan pencarian layanan.

```php
// app/Http/Controllers/HomeController.php

public function index()
{
    $services = Service::where('is_active', true)->get();
    $promos = Promo::where('is_active', true)
        ->where('valid_from', '<=', now())
        ->where('valid_until', '>=', now())
        ->limit(2)
        ->get();
    $bundles = Bundle::where('is_active', true)->get();

    return view('user.home.index', compact('services', 'promos', 'bundles'));
}
```

**Fitur:**

- Menampilkan layanan aktif
- Menampilkan promo yang berlaku
- Menampilkan paket bundle
- Pencarian layanan (AJAX)

---

#### B. OrderController

Mengelola seluruh proses pesanan.

**Method Utama:**

| Method              | Deskripsi                       |
| ------------------- | ------------------------------- |
| index()             | Menampilkan daftar pesanan user |
| create()            | Form pemesanan layanan          |
| createBundle()      | Form pemesanan paket            |
| store()             | Menyimpan pesanan baru          |
| show()              | Detail pesanan                  |
| cancel()            | Membatalkan pesanan             |
| track()             | Melacak pesanan                 |
| checkAvailability() | Cek ketersediaan jadwal         |
| checkPromo()        | Validasi kode promo             |
| rate()              | Rating dan review               |

**Fitur Utama:**

1. Pengecekan overlap jadwal
2. Generated nomor pesanan otomatis (ORD-YYYYMMDD-XXXX)
3. Integrasi promo/diskon
4. Pembuatan CleanerTask otomatis
5. Rating dan review untuk cleaner

```php
// Contoh nomor pesanan otomatis
$datePrefix = date('Ymd');
$lastOrder = Order::where('order_number', 'like', "ORD-{$datePrefix}-%")->latest('id')->first();
$sequence = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;
$orderNumber = "ORD-{$datePrefix}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
```

---

#### C. PaymentController

Mengelola proses pembayaran.

**Metode Pembayaran:**

- E-Wallet (GoPay, OVO, DANA, ShopeePay)
- Virtual Account (BCA, Mandiri, BNI, BRI)
- QRIS

**Fitur:**

- Biaya admin Rp 2.000 per transaksi
- Generate nomor pembayaran otomatis
- Konfirmasi pembayaran
- Pembatalan dan refund

```
php
// app/Http/Controllers/PaymentController.php

public function store(Request $request, Order $order)
{
    $adminFee = 2000;
    $total = $order->total + $adminFee;
    $paymentNumber = 'PAY-' . date('Ymd') . '-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT);

    $payment = Payment::create([
        'order_id' => $order->id,
        'payment_number' => $paymentNumber,
        'amount' => $order->total,
        'admin_fee' => $adminFee,
        'total' => $total,
        'payment_method' => $paymentMethod,
        'provider' => $provider,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $order->update(['status' => 'confirmed']);
}
```

---

#### D. Admin Dashboard Controller

Dashboard administrator dengan statistik lengkap.

**Statistik yang Ditampilkan:**

- Total users, cleaners, orders
- Pesanan pending dan completed
- Pendapatan total
- Pesanan hari ini
- Cleaner aktif
- Pendapatan bulanan dengan growth percentage

**Fitur Chart:**

- Chart pesanan 7 hari terakhir
- Chart layanan populer
- Data mingguan, bulanan, tahunan

```
php
// app/Http/Controllers/Admin/DashboardController.php

$stats = [
    'total_users' => User::count(),
    'total_cleaners' => Cleaner::count(),
    'total_orders' => Order::count(),
    'pending_orders' => Order::where('status', 'pending')->count(),
    'completed_orders' => Order::where('status', 'completed')->count(),
    'revenue' => Order::where('status', 'completed')->sum('total'),
    'monthly_revenue' => Order::where('status', 'completed')
        ->whereMonth('created_at', Carbon::now()->month)
        ->sum('total'),
    'revenue_growth' => $this->calculateRevenueGrowth(),
];
```

---

### 3.2 Model (Eloquent)

#### A. User Model

```
php
// app/Models/User.php

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'avatar',
        'member_level', 'total_orders', 'password', 'role', 'is_active'
    ];

    // Relasi
    public function orders() { return $this->hasMany(Order::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function payments() { return $this->hasManyThrough(Payment::class, Order::class); }

    // Accessors
    public function getAvatarUrlAttribute() { ... }
    public function getMemberLevelBadgeAttribute(): array { ... }
    public function getTotalSpendingAttribute() { ... }
}
```

**Fitur User:**

- Level member (Regular, Gold, Platinum)
- Role (admin, user)
- Status aktif/nonaktif
- Total pesanan

---

#### B. Order Model

```
php
// app/Models/Order.php

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'cleaner_id', 'service_id', 'bundle_id',
        'promo_id', 'customer_name', 'customer_phone', 'address',
        'special_conditions', 'order_date', 'start_time', 'end_time',
        'subtotal', 'discount', 'total', 'status', 'notes',
        'cancellation_reason', 'rating', 'review', 'completed_at'
    ];

    // Status Pesanan
    // 'pending', 'confirmed', 'on_progress', 'completed', 'cancelled'
}
```

---

#### C. Service Model

```
php
// app/Models/Service.php

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price',
        'icon_name', 'color', 'duration', 'is_popular', 'is_active'
    ];

    // Jenis Layanan
    // - Ruangan (M全面清洁)
    // - Kamar (卧室清洁)
    // - Ruang Tamu (客厅清洁)
    // - Toilet (卫生间清洁)
    // - Dapur (厨房清洁)
}
```

---

### 3.3 Middleware

| Middleware            | Fungsi                     |
| --------------------- | -------------------------- |
| Authenticate          | Memeriksa autentikasi user |
| AdminMiddleware       | Memeriksa role admin       |
| CleanerMiddleware     | Memeriksa role cleaner     |
| MemberLevelMiddleware | Memeriksa level member     |
| UserMiddleware        | Memeriksa role user        |

---

## 4. FRONTEND

### 4.1 Struktur View

```
resources/views/
├── admin/                    # View untuk admin
│   ├── auth/                 # Login admin
│   ├── cleaners/            # Kelola cleaner
│   ├── dashboard/           # Dashboard admin
│   ├── layouts/             # Layout admin
│   ├── orders/              # Kelola pesanan
│   ├── reports/             # Laporan
│   ├── services/            # Kelola layanan
│   └── users/               # Kelola user
├── auth/                    # View autentikasi
│   ├── landing.blade.php
│   ├── login.blade.php
│   └── register.blade.php
├── cleaner/                 # View untuk cleaner
│   ├── auth/
│   ├── dashboard/
│   ├── layouts/
│   ├── profile/
│   └── tasks/
├── layouts/                 # Layout utama
│   ├── app.blade.php
│   └── user.blade.php
└── user/                    # View untuk user
    ├── home/
    ├── orders/
    ├── payments/
    ├── profile/
    └── promo/
```

### 4.2 Teknologi Frontend

- **Template Engine**: Blade Laravel
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Vanilla JS dengan Alpine.js
- **Build Tool**: Vite
- **Icons**: Heroicons / Custom SVG

### 4.3 Contoh Struktur Layout

```
blade
<!-- resources/views/layouts/user.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'YukClean')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')
</body>
</html>
```

### 4.4 Halaman Utama User

```
blade
<!-- resources/views/user/home/index.blade.php -->
@extends('layouts.user')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <h1>Layanan Cleaning Profesional</h1>
    <p>Bersihkan rumah Anda dengan mudah</p>
</section>

<!-- Services Section -->
<section class="services">
    @foreach($services as $service)
        <div class="service-card">
            <img src="{{ $service->icon_path }}" alt="{{ $service->name }}">
            <h3>{{ $service->name }}</h3>
            <p>{{ $service->description }}</p>
            <span class="price">{{ $service->formatted_price }}</span>
            <a href="{{ route('user.orders.create', $service) }}">Pesan</a>
        </div>
    @endforeach
</section>

<!-- Promos Section -->
<section class="promos">
    @foreach($promos as $promo)
        <div class="promo-card" style="background-color: {{ $promo->background_color }}">
            <h3>{{ $promo->title }}</h3>
            <p>{{ $promo->description }}</p>
        </div>
    @endforeach
</section>
@endsection
```

---

## 5. DATABASE

### 5.1 Schema Database

#### Tabel: users

```
php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->string('avatar')->nullable();
    $table->enum('member_level', ['Regular', 'Gold', 'Platinum'])->default('Regular');
    $table->integer('total_orders')->default(0);
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

#### Tabel: orders

```
php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('cleaner_id')->nullable()->constrained('cleaners');
    $table->foreignId('service_id')->nullable()->constrained('services');
    $table->foreignId('bundle_id')->nullable()->constrained('bundles');
    $table->foreignId('promo_id')->nullable()->constrained('promos');
    $table->string('customer_name');
    $table->string('customer_phone');
    $table->text('address');
    $table->text('special_conditions')->nullable();
    $table->date('order_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->enum('status', ['pending', 'confirmed', 'on_progress', 'completed', 'cancelled']);
    $table->text('notes')->nullable();
    $table->text('cancellation_reason')->nullable();
    $table->integer('rating')->nullable();
    $table->text('review')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});
```

#### Tabel: payments

```
php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained();
    $table->string('payment_number')->unique();
    $table->decimal('amount', 10, 2);
    $table->decimal('admin_fee', 10, 2)->default(0);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->enum('payment_method', ['e-wallet', 'virtual_account', 'qris']);
    $table->string('provider')->nullable(); // gopay, ovo, dana, bca, mandiri, etc.
    $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded']);
    $table->string('provider_payment_id')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->timestamp('refunded_at')->nullable();
    $table->timestamps();
});
```

#### Tabel: cleaners

```
php
Schema::create('cleaners', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('name');
    $table->string('phone');
    $table->text('address')->nullable();
    $table->string('photo')->nullable();
    $table->enum('status', ['available', 'on_task', 'unavailable'])->default('available');
    $table->decimal('rating', 3, 1)->default(0);
    $table->integer('total_reviews')->default(0);
    $table->integer('total_tasks')->default(0);
    $table->timestamps();
});
```

#### Tabel: cleaner_tasks

```
php
Schema::create('cleaner_tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained();
    $table->foreignId('cleaner_id')->nullable()->constrained('cleaners');
    $table->string('customer_name');
    $table->string('customer_phone');
    $table->text('address');
    $table->string('service_name');
    $table->date('task_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->enum('status', ['available', 'assigned', 'on_the_way', 'in_progress', 'completed', 'cancelled']);
    $table->text('notes')->nullable();
    $table->integer('progress')->default(0);
    $table->timestamps();
});
```

#### Tabel: services

```
php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->string('icon_name')->nullable();
    $table->string('color')->nullable();
    $table->integer('duration'); // dalam jam
    $table->boolean('is_popular')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### Tabel: promos

```
php
Schema::create('promos', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('discount_type', ['percentage', 'fixed']);
    $table->decimal('discount_value', 10, 2);
    $table->decimal('min_purchase', 10, 2)->default(0);
    $table->date('valid_from');
    $table->date('valid_until');
    $table->integer('max_uses')->nullable();
    $table->integer('current_uses')->default(0);
    $table->boolean('is_active')->default(true);
    $table->string('background_color')->nullable();
    $table->string('icon')->nullable();
    $table->timestamps();
});
```

---

### 5.2 Relasi Antar Tabel

```
users (1) ──────< (N) orders
orders (N) ──────< (1) services
orders (N) ──────< (1) bundles
orders (N) ──────< (1) promos
orders (N) ──────< (1) cleaners
orders (N) ──────< (1) payments
orders (N) ──────< (1) cleaner_tasks
cleaners (1) ────< (N) cleaner_tasks
cleaners (1) ────< (N) reviews
users (1) ───────< (N) reviews
```

---

## 6. FITUR UTAMA

### 6.1 Fitur untuk User (Pelanggan)

| Fitur               | Deskripsi                                                 |
| ------------------- | --------------------------------------------------------- |
| Pendaftaran & Login | Registrasi dan autentikasi user                           |
| Lihat Layanan       | Melihat daftar layanan cleaning                           |
| Pemesanan           | Memesan layanan dengan memilih tanggal, waktu, dan alamat |
| Promo Code          | Menggunakan kode promo untuk diskon                       |
| Pembayaran          | Melakukan pembayaran via E-Wallet, VA, atau QRIS          |
| Pelacakan           | Melacak status pesanan secara real-time                   |
| Riwayat Pesanan     | Melihat riwayat pesanan (aktif dan selesai)               |
| Rating & Review     | Memberikan rating dan ulasan setelah selesai              |
| Profil              | Mengelola profil dan level member                         |

### 6.2 Fitur untuk Admin

| Fitur          | Deskripsi                          |
| -------------- | ---------------------------------- |
| Dashboard      | Statistik lengkap dengan chart     |
| Kelola User    | Melihat dan mengelola data user    |
| Kelola Cleaner | Melihat dan mengelola data cleaner |
| Kelola Pesanan | Melihat dan memanage semua pesanan |
| Kelola Layanan | Menambah, edit, hapus layanan      |
| Kelola Promo   | Mengelola kode promo               |
| Laporan        | Melihat laporan bisnis             |

### 6.3 Fitur untuk Cleaner

| Fitur     | Deskripsi                        |
| --------- | -------------------------------- |
| Dashboard | Melihat tugas harian             |
| Tugas     | Menerima dan menyelesaikan tugas |
| Profil    | Mengelola profil dan rating      |

---

## 7. KESIMPULAN

### 7.1 Keberhasilan Proyek

Proyek YukClean berhasil dikembangkan dengan fitur-fitur lengkap:

1. ✅ Sistem multi-user (Admin, User, Cleaner)
2. ✅ Pemesanan layanan cleaning online
3. ✅ Sistem pembayaran terintegrasi
4. ✅ Promo dan diskon
5. ✅ Pelacakan pesanan real-time
6. ✅ Rating dan review
7. ✅ Dashboard admin dengan analytics
8. ✅ Sistem tugas untuk cleaner

### 7.2 Rekomendasi Pengembangan

1. **Integrasi Payment Gateway** - Mengintegrasikan Midtrans atau Xendit untuk pembayaran yang lebih lengkap
2. **Notifikasi Real-time** - Menggunakan Pusher untuk notifikasi real-time
3. **Mobile App** - Mengembangkan aplikasi mobile (Flutter/React Native)
4. **Chat System** - Fitur chat antara user dan cleaner
5. **Sistem Absensi** - GPS tracking untuk cleaner

---

## LAMPIRAN

### A. Daftar Migration

| File                                              | Deskripsi            |
| ------------------------------------------------- | -------------------- |
| 0001_01_01_000000_create_users_table.php          | Tabel users          |
| 2026_02_16_030108_create_services_table.php       | Tabel services       |
| 2026_02_16_030109_create_promos_table.php         | Tabel promos         |
| 2026_02_16_094933_create_bundles_table.php        | Tabel bundles        |
| 2026_02_16_094949_create_categories_table.php     | Tabel categories     |
| 2026_02_16_154039_create_payments_table.php       | Tabel payments       |
| 2026_02_16_174701_create_reviews_table.php        | Tabel reviews        |
| 2026_02_16_183034_create_cleaners_table.php       | Tabel cleaners       |
| 2026_02_16_183125_create_cleaner_tasks_table.php  | Tabel cleaner_tasks  |
| 2026_02_17_033232_create_admins_table.php         | Tabel admins         |
| 2026_02_23_151828_create_order_tracking_table.php | Tabel order_tracking |

### B. Package yang Digunakan

```
json
{
    "require": {
        "php": "^8.2",
        "barryvdh/laravel-dompdf": "^3.1",
        "laravel/framework": "^12.0",
        "laravel/tinker": "^2.10.1",
        "pusher/pusher-php-server": "^7.2"
    }
}
```

---

**Dibuat pada:** 2026
**Framework:** Laravel 12.x
**Developer:** [Nama Developer]

---

_Laporan ini dibuat secara otomatis dari analisis codebase proyek YukClean._
