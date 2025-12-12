<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Lokasi & Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color: #4ecdc4; }
        body { background-color: var(--primary-bg); color: var(--text-color); }
        
        .card-custom { background-color: var(--secondary-bg); border: none; border-radius: 10px; }
        .card-custom h5, .card-custom label { color: #ffffff !important; }

        #map { height: 500px; width: 100%; border-radius: 10px; cursor: crosshair; }

        .form-control { 
            background-color: #1c1c2e; 
            border-color: #404060; 
            color: #ffffff !important; 
        }
        
        .form-control:focus { 
            background-color: #151525; 
            border-color: var(--accent-color); 
            color: #ffffff !important;
            box-shadow: none;
        }

        .form-control::placeholder { color: #bbbbbb; opacity: 1; }
        .table-dark { --bs-table-bg: var(--secondary-bg); --bs-table-color: var(--text-color); }

        /* Styling Rute & Popup agar Teks Hitam */
        .leaflet-routing-container, 
        .leaflet-routing-alt, 
        .leaflet-popup-content-wrapper {
            color: #000000 !important;
            background-color: #ffffff !important;
        }
        .leaflet-routing-container td, 
        .leaflet-routing-container h2, 
        .leaflet-routing-container h3,
        .leaflet-routing-container span {
            color: #000000 !important;
        }
        .leaflet-routing-container a { color: #007bff !important; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--accent-color);"><i class="fas fa-map-marked-alt"></i> Manajemen Lokasi & Tracking</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light">Kembali ke Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card card-custom p-4">
                    <h5 class="mb-3">Tambah Lokasi Baru</h5>
                    
                    <form action="{{ route('admin.locations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" name="name" class="form-control" required placeholder="Contoh: Cabang Jakarta">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" id="lat" class="form-control" required readonly placeholder="Klik Peta / Geser Marker">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" id="lng" class="form-control" required readonly placeholder="Klik Peta / Geser Marker">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto (Opsional)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat lokasi..."></textarea>
                        </div>
                        <button type="button" onclick="getLocation()" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-crosshairs"></i> Deteksi Lokasi Saya
                        </button>
                        <button type="submit" class="btn btn-success w-100">Simpan Lokasi</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div id="map"></div>
                <small class="text-muted mt-2 d-block">* Tips: Jika lokasi tidak akurat, <b>klik</b> peta atau <b>geser</b> marker merah ke posisi yang benar.</small>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3">Daftar Lokasi Tersimpan</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover rounded">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Koordinat</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $loc)
                            <tr>
                                <td>{{ $loc->name }}</td>
                                <td>{{ $loc->latitude }}, {{ $loc->longitude }}</td>
                                <td>
                                    @if($loc->image)
                                        <img src="{{ asset('storage/locations/'.$loc->image) }}" width="50" class="rounded">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="routeTo({{ $loc->latitude }}, {{ $loc->longitude }})">
                                        <i class="fas fa-route"></i> Rute ke sini
                                    </button>
                                    <form action="{{ route('admin.locations.destroy', $loc->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus lokasi ini?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

    <script>
        // 1. Inisialisasi Peta
        var map = L.map('map').setView([-6.200000, 106.816666], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker;
        var currentLat, currentLng;
        var routingControl;

        // --- FUNGSI UPDATE INPUT SAAT MARKER BERGERAK ---
        function updateInput(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            currentLat = lat; // Update variabel global user position
            currentLng = lng;
        }

        // --- FUNGSI MENAMBAHKAN MARKER DRAGGABLE ---
        function setMarker(lat, lng, message = "Lokasi Dipilih (Geser jika perlu)") {
            if (marker) map.removeLayer(marker);
            
            // Tambahkan opsi draggable: true
            marker = L.marker([lat, lng], {draggable: true}).addTo(map)
                .bindPopup(message).openPopup();

            // Event saat marker selesai digeser (dragend)
            marker.on('dragend', function(e) {
                var position = marker.getLatLng();
                updateInput(position.lat, position.lng);
                marker.bindPopup("Lokasi Baru").openPopup();
            });

            // Update input form langsung
            updateInput(lat, lng);
            map.setView([lat, lng], 16);
        }

        // 2. Fungsi Deteksi Lokasi Saya
        function getLocation() {
            if (navigator.geolocation) {
                // Saya naikkan timeout jadi 20 detik agar lebih sabar mencari sinyal
                var options = {
                    enableHighAccuracy: true,
                    timeout: 20000, 
                    maximumAge: 0
                };
                navigator.geolocation.getCurrentPosition(showPosition, showError, options);
            } else {
                alert("Geolocation tidak didukung oleh browser ini.");
            }
        }

        function showPosition(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            setMarker(lat, lng, "Lokasi Anda (Geser jika kurang pas)");
        }

        function showError(error) {
            // Fallback: Jika error/timeout, jangan diam saja, beri alert
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User menolak permintaan Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Informasi lokasi tidak tersedia.");
                    break;
                case error.TIMEOUT:
                    alert("Waktu permintaan lokasi habis (Sinyal lemah). Silakan pilih lokasi manual di peta.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("Terjadi kesalahan yang tidak diketahui.");
                    break;
            }
        }

        // 3. Klik Peta untuk pilih lokasi manual
        map.on('click', function(e) {
            setMarker(e.latlng.lat, e.latlng.lng);
        });

        // 4. Menampilkan Marker untuk lokasi tersimpan (Database)
        var locations = @json($locations);
        locations.forEach(function(loc) {
            // Marker database TIDAK draggable (statis)
            L.marker([loc.latitude, loc.longitude]).addTo(map)
                .bindPopup(`<b>${loc.name}</b><br>${loc.description || ''}<br><button onclick="routeTo(${loc.latitude}, ${loc.longitude})" class="btn btn-xs btn-primary mt-2">Rute</button>`);
        });

        // 5. Fungsi Tracking Rute
        function routeTo(destLat, destLng) {
            // Pastikan user sudah punya titik awal (bisa dari autodetect atau klik manual)
            // Jika input lat/lng kosong, berarti belum ada titik awal
            var startLat = document.getElementById('lat').value;
            var startLng = document.getElementById('lng').value;

            if (!startLat || !startLng) {
                alert("Tentukan titik awal Anda dulu! (Klik 'Deteksi Lokasi Saya' atau Klik Peta)");
                return;
            }

            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(startLat, startLng), 
                    L.latLng(destLat, destLng)
                ],
                routeWhileDragging: true,
                geocoder: L.Control.Geocoder ? L.Control.Geocoder.nominatim() : null
            }).addTo(map);
        }
    </script>
</body>
</html>