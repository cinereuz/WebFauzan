<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background-color: var(--primary-bg); color: var(--text-color); }
        .stat-card { background-color: var(--secondary-bg); border: none; border-radius: 10px; }
        .table-dark-custom { --bs-table-bg: var(--secondary-bg); --bs-table-color: var(--text-color); --bs-table-border-color: #404060; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-4" style="color: var(--accent-color-2);">Admin Dashboard</h1>
            <div>
                <a href="{{ route('admin.qr.index') }}" class="btn btn-info me-2" style="background-color: var(--accent-color-2); border-color: var(--accent-color-2);">
                    <i class="fas fa-qrcode me-1"></i> Manajemen QR
                </a>
                <a href="{{ route('anime.index') }}" class="btn btn-outline-light">Lihat Halaman Utama</a>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-3x me-4" style="color: var(--accent-color-2);"></i>
                        <div>
                            <h5>Total Pengguna</h5>
                            <h2 class="fw-bold">{{ $totalUsers }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-film fa-3x me-4" style="color: var(--accent-color-1);"></i>
                        <div>
                            <h5>Total Anime</h5>
                            <h2 class="fw-bold">{{ $totalAnime }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-dollar-sign fa-3x me-4" style="color: #6a994e;"></i>
                        <div>
                            <h5>Total Pendapatan</h5>
                            <h2 class="fw-bold">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <h3 class="mb-3" style="color: var(--accent-color-1);">5 Transaksi Terbaru</h3>
        <div class="table-responsive">
            <table class="table table-dark-custom table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Pengguna</th>
                        <th>Anime</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->anime->judul }}</td>
                            <td>Rp{{ number_format($order->gross_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($order->transaction_status == 'settlement' || $order->transaction_status == 'capture')
                                    <span class="badge bg-success">Success</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($order->transaction_status) }}</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>