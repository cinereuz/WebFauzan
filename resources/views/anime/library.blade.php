<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Anime Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background-color: var(--primary-bg); color: var(--text-color); }
        .card { transition: transform .2s ease-in-out, box-shadow .2s ease-in-out; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.4); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--accent-color-1);">Koleksi Saya</h1>
            <a href="{{ route('anime.index') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Anime
            </a>
        </div>

        <div class="row">
            @forelse ($purchasedOrders as $order)
                <div class="col-md-4 mb-4">
                    <div class="card h-100" style="background-color: var(--secondary-bg); border-color: #404060;">
                        <img src="{{ asset('storage/anime/' . $order->anime->gambar) }}" class="card-img-top" alt="{{ $order->anime->judul }}" style="height: 300px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-white">{{ $order->anime->judul }}</h5>
                            <p class="card-text small text-white-50">Studio: {{ $order->anime->studio }}</p>
                            <div class="mt-auto d-grid">
                                <a href="{{ route('anime.show', $order->anime->id) }}" class="btn" style="background-color: var(--accent-color-2);">Tonton Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center" style="background-color: var(--secondary-bg); border-color: #404060; color: var(--text-color);">
                        Anda belum memiliki anime apapun. <a href="{{ route('anime.index') }}">Jelajahi sekarang!</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $purchasedOrders->links() }}
        </div>
    </div>
</body>
</html>