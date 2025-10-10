<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $anime->judul }} - Detail Anime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background: linear-gradient(135deg, var(--primary-bg) 0%, #303050 100%); color: var(--text-color); font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .container { padding-top: 50px; padding-bottom: 50px; }
        .detail-card { background-color: var(--secondary-bg); border: none; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.4); padding: 40px; border-bottom: 5px solid var(--accent-color-2); }
        .anime-image { width: 100%; max-width: 350px; height: auto; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); border: 3px solid var(--accent-color-1); }
        .card-title-main { color: var(--accent-color-1); font-weight: 800; font-size: 2.5rem; margin-bottom: 15px; }
        .info-label { font-weight: 600; color: var(--text-color); margin-bottom: 5px; font-size: 1rem; }
        .info-text { margin-bottom: 20px; font-size: 1.05rem; line-height: 1.7; color: rgba(255,255,255,0.8); }
        
        .badge { font-weight: 600; padding: 0.6em 0.9em; border-radius: 20px; font-size: 0.8em; text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.5em; margin-bottom: 0.5em; background-color: #555 !important; color: var(--text-color) !important; }

        .btn-primary { background-color: var(--accent-color-2); border-color: var(--accent-color-2); font-weight: bold; padding: 0.75rem 1.5rem; border-radius: 8px; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #3db2a9; border-color: #3db2a9; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(78, 205, 196, 0.4); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card detail-card">
            <div class="row g-4">
                <div class="col-md-5 d-flex justify-content-center align-items-center">
                    <img src="{{ asset('storage/anime/' . $anime->gambar) }}" class="anime-image" alt="{{ $anime->judul }}">
                </div>
                <div class="col-md-7">
                    <h1 class="card-title-main">{{ $anime->judul }}</h1>
                    <hr class="text-secondary opacity-25 mb-4">
                    
                    <div class="mb-4">
                        <div class="info-label">Genre:</div>
                        @php $genres = explode(', ', $anime->genre); @endphp
                        @foreach ($genres as $genre)
                            <span class="badge">{{ $genre }}</span>
                        @endforeach
                    </div>
                    
                    <div class="mb-4">
                        <div class="info-label">Episode:</div>
                        <div class="info-text">{{ $anime->episode }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="info-label">Sinopsis:</div>
                        <div class="info-text">{!! $anime->sinopsis !!}</div>
                    </div>
                    
                    <a href="{{ route('anime.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Anime
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>