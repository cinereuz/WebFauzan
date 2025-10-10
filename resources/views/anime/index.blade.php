<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-bg: #1c1c2e;
            --secondary-bg: #2a2a4a;
            --text-color: #e0e0f0;
            --accent-color-1: #ff6b6b;
            --accent-color-2: #4ecdc4;
            --card-shadow: rgba(0, 0, 0, 0.4);
            --btn-outline-color-info: #4ecdc4;
            --btn-outline-color-warning: #ffb86b;
            --btn-outline-color-danger: #ff6b6b;
        }
        body {
            background: linear-gradient(135deg, var(--primary-bg) 0%, #303050 100%);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            min-height: 100vh;
        }
        .navbar { background-color: var(--secondary-bg); box-shadow: 0 2px 10px var(--card-shadow); }
        .navbar-brand, .nav-link { font-weight: 700; color: var(--text-color) !important; }
        .nav-link:hover { color: var(--accent-color-1) !important; }
        .hero-section { background: linear-gradient(45deg, var(--accent-color-2) 0%, var(--accent-color-1) 100%); padding: 3rem 2rem; margin-bottom: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); color: #fff; position: relative; overflow: hidden; }
        .hero-section .display-4 { font-weight: 800; font-size: 2.5rem; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4); }

        .anime-card { background-color: var(--secondary-bg); border: none; border-radius: 15px; box-shadow: 0 8px 25px var(--card-shadow); border-bottom: 5px solid var(--accent-color-2); transition: all 0.4s ease; }
        .anime-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 18px 35px rgba(0, 0, 0, 0.6); border-bottom-color: var(--accent-color-1); }
        .card-img-top { height: 250px; object-fit: cover; border-bottom: 3px solid var(--accent-color-1); transition: border-color 0.3s ease; }
        .anime-card:hover .card-img-top { border-bottom-color: var(--accent-color-2); }
        .card-body { padding: 1.5rem; }
        .card-title { font-weight: 700; color: var(--accent-color-1); margin-bottom: 1rem; font-size: 1.5rem; }
        .card-body p small { color: var(--accent-color-2) !important; opacity: 1;}

        .badge { font-weight: 600; padding: 0.6em 0.9em; border-radius: 20px; font-size: 0.8em; text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.5em; margin-bottom: 0.5em; background-color: #555 !important; color: var(--text-color) !important; }
        
        .btn-group-card { display: flex; gap: 10px; margin-top: auto; padding-top: 1rem; border-top: 1px dashed rgba(255,255,255,0.1); }
        .btn-outline-info, .btn-outline-warning, .btn-outline-danger { border-width: 2px; font-weight: 600; transition: all 0.3s ease; padding: 0.5rem 1rem; border-radius: 8px; }
        .btn-outline-info { color: var(--btn-outline-color-info); border-color: var(--btn-outline-color-info); }
        .btn-outline-warning { color: var(--btn-outline-color-warning); border-color: var(--btn-outline-color-warning); }
        .btn-outline-danger { color: var(--btn-outline-color-danger); border-color: var(--btn-outline-color-danger); }
        .btn-outline-info:hover { background-color: var(--btn-outline-color-info); color: var(--secondary-bg); }
        .btn-outline-warning:hover { background-color: var(--btn-outline-color-warning); color: var(--secondary-bg); }
        .btn-outline-danger:hover { background-color: var(--btn-outline-color-danger); color: var(--secondary-bg); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('anime.index') }}">MY ANIME LIST</a>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger ms-2">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout ({{ Auth::user()->name }})
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-info me-2" href="{{ route('register') }}">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-warning" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5 flex-grow-1">
        <div class="hero-section">
            <h1 class="display-4">Daftar Koleksi Anime</h1>
            <p class="lead">Selamat datang, {{ Auth::check() ? Auth::user()->name : 'Tamu' }}! Kelola daftar anime Anda di sini.</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            @if(Auth::check() && Auth::user()->is_admin == 1)
                <div>
                    <a href="{{ route('anime.create') }}" class="btn btn-primary me-2" style="background-color: var(--accent-color-2); border-color: var(--accent-color-2);">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Anime
                    </a>
                    <a href="{{ route('anime.export') }}" class="btn btn-success" style="background-color: var(--accent-color-1); border-color: var(--accent-color-1);">
                        <i class="fas fa-file-excel me-1"></i> Export CSV
                    </a>
                </div>
            @endif
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse ($anime as $item)
            <div class="col">
                <div class="card anime-card h-100">
                    <img src="{{ asset('storage/anime/' . $item->gambar) }}" class="card-img-top" alt="{{ $item->judul }}">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $item->judul }}</h5>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @php $genres = explode(', ', $item->genre); @endphp
                            @foreach ($genres as $genre)
                                <span class="badge">{{ $genre }}</span>
                            @endforeach
                        </div>
                        <p class="card-text text-muted mb-1"><small>Episode: {{ $item->episode }}</small></p>
                        <div class="btn-group-card mt-auto pt-3">
                            <a href="{{ route('anime.show', $item->id) }}" class="btn btn-sm btn-outline-info">Detail</a>
                            
                            @if(Auth::check() && Auth::user()->is_admin == 1)
                                <a href="{{ route('anime.edit', $item->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                <form action="{{ route('anime.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="alert alert-info" role="alert">
                    Belum ada anime di daftar. Silakan tambahkan satu!
                </div>
            </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $anime->links() }}
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Logika SweetAlert2 untuk Notifikasi Sukses/Gagal
        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Sukses', text: '{{ session('success') }}' });
        @elseif (session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}' });
        @endif

        // Logika SweetAlert2 untuk Konfirmasi Delete
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Anime yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    })
                });
            });
        });
    </script>
</body>
</html>