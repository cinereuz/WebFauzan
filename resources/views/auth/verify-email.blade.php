<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background: linear-gradient(135deg, var(--primary-bg) 0%, #303050 100%); color: var(--text-color); }
    </style>
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card p-4 text-center" style="background-color: var(--secondary-bg); width: 100%; max-width: 500px; border-radius: 15px;">
            <h3 class="mb-4" style="color: var(--accent-color-1);">Verifikasi Alamat Email Anda</h3>
            
            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success" role="alert">
                    Link verifikasi baru telah dikirim ke email Anda!
                </div>
            @endif

            <p class="text-white-50 mb-3">Terima kasih telah mendaftar! Sebelum melanjutkan, silakan periksa email Anda untuk link verifikasi.</p>
            <p class="text-white-50 mb-4">Jika Anda tidak menerima email, kami akan mengirimkannya kembali.</p>

            <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn w-100 mb-2" style="background-color: var(--accent-color-2);">Kirim Ulang Email Verifikasi</button>
            </form>

            <form class="d-inline" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-white-50">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>