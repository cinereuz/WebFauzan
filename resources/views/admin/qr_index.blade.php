<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background-color: var(--primary-bg); color: var(--text-color); }
        .card { background-color: var(--secondary-bg); border: none; border-radius: 10px; }
        .table-dark-custom { --bs-table-bg: var(--secondary-bg); --bs-table-color: var(--text-color); --bs-table-border-color: #404060; }
        .qr-image { max-width: 100px; height: auto; background-color: white; padding: 5px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--accent-color-2);">Manajemen QR Code</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light">Kembali ke Dashboard</a>
        </div>

        <div class="card p-4 mb-4">
            <h3 style="color: var(--accent-color-1);">Tambah QR Code Baru</h3>
            <form action="{{ route('admin.qr.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="code_id" class="form-label" style="color: white;">Masukkan ID Unik (Contoh: 12345)</label>
                    <input type="text" class="form-control" id="code_id" name="code_id" value="{{ old('code_id') }}" required>
                    @error('code_id')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn" style="background-color: var(--accent-color-1); color: white;">
                    <i class="fas fa-plus-circle me-1"></i> Generate & Simpan
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card p-4">
            <h3 class="mb-3" style="color: var(--accent-color-1);">Daftar QR Code</h3>
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover">
                    <thead>
                        <tr>
                            <th>ID (Kode Teks)</th>
                            <th>Gambar QR</th>
                            <th>Nama File</th>
                            <th>Dibuat Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($qrCodes as $qr)
                            <tr>
                                <td>
                                    <a href="{{ asset('storage/qrcodes/' . $qr->filename) }}" target="_blank" title="Lihat gambar QR">
                                        {{ $qr->code_id }}
                                    </a>
                                </td>
                                <td>
                                    <img src="{{ asset('storage/qrcodes/' . $qr->filename) }}" alt="QR Code {{ $qr->code_id }}" class="qr-image">
                                </td>
                                <td>{{ $qr->filename }}</td>
                                <td>{{ $qr->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.qr.download', $qr->code_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada QR Code yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $qrCodes->links() }}
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>