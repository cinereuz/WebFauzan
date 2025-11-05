<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Anime List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #1c1c2e;
            --secondary-bg: #2a2a4a;
            --text-color: #e0e0f0;
            --accent-color-1: #ff6b6b;
            --accent-color-2: #4ecdc4;
        }
        body { 
            background: linear-gradient(135deg, var(--primary-bg) 0%, #303050 100%);
            color: var(--text-color);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .auth-card { 
            background-color: var(--secondary-bg);
            border-radius: 15px; 
            padding: 40px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            max-width: 450px; 
            width: 90%;
            border-bottom: 5px solid var(--accent-color-2);
        }
        .card-title { 
            color: var(--accent-color-1); 
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 25px;
        }
        .form-control {
            background-color: #38385e; 
            border: 1px solid #4a4a70; 
            color: var(--text-color) !important;
            padding: 10px 15px;
        }
        .form-control:focus {
            background-color: #38385e;
            border-color: var(--accent-color-2);
            box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.3);
        }
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-color);
        }
        .text-muted, .text-muted a {
            color: var(--text-color) !important;
        }
        .text-muted a {
            color: var(--accent-color-1) !important;
            font-weight: 600;
            text-decoration: none;
        }
        .text-muted a {
            color: var(--accent-color-1) !important;
            font-weight: 600;
            text-decoration: none;
        }
        .text-muted a:hover {
            color: white !important;
        }
        .form-control::placeholder {
            /* Target browser modern */
            color: var(--text-color) !important;
            opacity: 0.5;
        }
        .form-control::-webkit-input-placeholder {
            color: var(--text-color) !important;
            opacity: 0.5;
        }
        .form-control::-moz-placeholder {
            color: var(--text-color) !important;
            opacity: 0.5;
        }
        .form-control:-ms-input-placeholder {
            color: var(--text-color) !important;
            opacity: 0.5;
        }
        .btn-primary {
            background-color: var(--accent-color-2);
            border-color: var(--accent-color-2);
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #38385e; 
            color: var(--accent-color-1); 
            border-color: #4a4a70;
        }
        .alert-success {
            background-color: #38385e; 
            color: var(--accent-color-2); 
            border-color: #4a4a70;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1 class="text-center card-title">Login Akun</h1>
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 mb-3">Email atau Password salah.</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email terdaftar" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password Anda" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">LOGIN</button>
            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" style="color: var(--accent-color-2);">Lupa Password?</a>
            </div>
            <p class="text-center mt-3 small text-muted">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
        </form>
    </div>
</body>
</html>