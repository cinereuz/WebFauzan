<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $anime->judul }} - Detail Anime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            padding-top: 50px;
        }
        .detail-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .anime-image {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 10px;
        }
        .genre-badge {
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card detail-card p-4">
            <div class="row g-4">
                <div class="col-md-5 d-flex justify-content-center">
                    <img src="{{ asset('storage/anime/' . $anime->gambar) }}" class="anime-image" alt="{{ $anime->judul }}">
                </div>
                <div class="col-md-7">
                    <h1 class="card-title text-primary mb-3">{{ $anime->judul }}</h1>
                    <hr class="text-secondary">
                    <div class="mb-3">
                        <h5 class="fw-bold">Genre:</h5>
                        @php
                            $genres = explode(', ', $anime->genre);
                        @endphp
                        @foreach ($genres as $genre)
                            <span class="badge bg-secondary text-white genre-badge me-1">{{ $genre }}</span>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <h5 class="fw-bold">Episode:</h5>
                        <p>{{ $anime->episode }}</p>
                    </div>
                    <div class="mb-3">
                        <h5 class="fw-bold">Sinopsis:</h5>
                        <div class="text-muted">{!! $anime->sinopsis !!}</div>
                    </div>
                    <a href="{{ route('anime.index') }}" class="btn btn-primary mt-3">Kembali ke Daftar Anime</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>