<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran untuk {{ $anime->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background: linear-gradient(135deg, var(--primary-bg) 0%, #303050 100%); color: var(--text-color); }
    </style>
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        
        <div class="card p-5 text-center text-white" style="background-color: var(--secondary-bg); border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.4);">
            
            <h1 class="mb-3" style="color: var(--accent-color-1);">Konfirmasi Pembayaran</h1>
            <p>Anda akan melakukan pembayaran untuk:</p>
            <h4 class="mb-3">{{ $anime->judul }}</h4>
            
            <img src="{{ asset('storage/anime/' . $anime->gambar) }}" class="img-fluid rounded mb-4" style="max-height: 200px; object-fit: cover;">
            
            <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
            <p class="h5"><strong>Total:</strong> Rp{{ number_format($order->gross_amount, 0, ',', '.') }}</p>
            
            {{-- Grup Tombol --}}
            <div class="d-grid gap-2 mt-4">
                <button id="pay-button" class="btn btn-lg" style="background-color: var(--accent-color-2); border-color: var(--accent-color-2);">Lanjutkan Pembayaran</button>
                
                <a href="{{ route('anime.index') }}" class="btn btn-outline-light">Batal</a>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    alert("Pembayaran berhasil!"); 
                    console.log(result);
                    window.location.href = '/anime?status=sukses';
                },
                onPending: function(result){
                    alert("Menunggu pembayaran Anda!"); 
                    console.log(result);
                    window.location.href = '/anime?status=pending';
                },
                onError: function(result){
                    alert("Pembayaran gagal!"); 
                    console.log(result);
                },
                onClose: function(){
                    alert('Anda menutup pop-up tanpa menyelesaikan pembayaran.');
                }
            });
        };
    </script>
</body>
</html>