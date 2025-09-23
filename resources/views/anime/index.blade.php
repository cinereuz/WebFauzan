<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            padding-top: 50px;
        }
        .anime-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .anime-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-title {
            color: #007bff;
            font-weight: bold;
        }
        .badge {
            font-size: 0.8em;
            font-weight: normal;
        }
        .text-muted-darker {
            color: #6c757d;
        }
        .btn-group-sm > .btn {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Daftar Anime</h1>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('anime.create') }}" class="btn btn-primary">Tambah Anime</a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @forelse($anime as $item)
                <div class="col">
                    <div class="card anime-card h-100">
                        <img src="{{ asset('storage/anime/' . $item->gambar) }}" class="card-img-top" alt="{{ $item->judul }}">
                        <div class="card-body">
                            <div>
                                <h5 class="card-title">{{ $item->judul }}</h5>
                                <p class="card-text">
                                    @php
                                        $genres = explode(', ', $item->genre);
                                    @endphp
                                    @foreach ($genres as $genre)
                                        <span class="badge bg-secondary text-white me-1">{{ $genre }}</span>
                                    @endforeach
                                </p>
                                <p class="card-text mb-3"><small class="text-muted-darker">Episode: {{ number_format($item->episode, 0, ',', '.') }}</small></p>
                                {{-- <p class="card-text">{{ Str::words(strip_tags($item->sinopsis), 15, '...') }}</p> --}}
                            </div>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="{{ route('anime.show', $item->id) }}" class="btn btn-success btn-sm">Detail</a>
                                <div>
                                    <a href="{{ route('anime.edit', $item->id) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                                    <form onsubmit="return confirm('Apakah Anda yakin ingin menghapus anime ini?');"
                                          action="{{ route('anime.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-danger text-center">Data Anime belum Tersedia.</div>
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
        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Sukses', text: '{{ session('success') }}' });
        @elseif (session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}' });
        @endif
    </script>
</body>
</html>