<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori (Livewire)</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color: #4ecdc4; }
        body { background-color: var(--primary-bg); color: var(--text-color); }
        .card-custom { background-color: var(--secondary-bg); border: none; border-radius: 10px; }
    </style>

    @livewireStyles
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--accent-color);">Manajemen Kategori</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light">Kembali ke Dashboard</a>
        </div>

        @livewire('admin.category-crud')
        
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts

    <script>
        window.addEventListener('close-modal', event => {
            $('#categoryModal').modal('hide');
        });

        window.addEventListener('open-modal', event => {
            $('#categoryModal').modal('show');
        });
    </script>
</body>
</html>