<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background: linear-gradient(135deg, var(--primary-bg) 0%, #303050 100%); color: var(--text-color); }
    </style>
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card p-4" style="background-color: var(--secondary-bg); width: 100%; max-width: 400px; border-radius: 15px;">
            <h3 class="text-center mb-4" style="color: var(--accent-color-1);">Lupa Password</h3>
            <p class="text-center text-white-50 mb-3">Kami akan mengirimkan link reset password ke nomor WhatsApp Anda.</p>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.phone') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="phone_number" class="form-label text-white">Nomor WhatsApp (Contoh: 0812...)</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                </div>
                <button type="submit" class="btn w-100" style="background-color: var(--accent-color-2);">Kirim Link Reset</button>
            </form>
        </div>
    </div>
</body>
</html>