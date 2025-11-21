<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AnimeModel;
use Illuminate\Support\Facades\Storage;
use App\Models\QrCode;
use Illuminate\Support\Facades\Http;

class AnimeController extends Controller
{
    public function index()
    {
        $anime = AnimeModel::latest()->paginate(10);

        // Menambahkan logika untuk cek anime yang sudah lunas
        $purchasedAnimeIds = [];
        if (Auth::check()) {
            $purchasedAnimeIds = Order::where('user_id', Auth::id())
                                      ->whereIn('transaction_status', ['settlement', 'capture'])
                                      ->pluck('anime_id')
                                      ->toArray();
        }
        
        // Mengirim data 'purchasedAnimeIds' ke view
        return view('anime.index', compact('anime', 'purchasedAnimeIds'));
    }

    // Method untuk menampilkan dashboard admin.
    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalAnime = AnimeModel::count();
        $totalRevenue = Order::whereIn('transaction_status', ['settlement', 'capture'])->sum('gross_amount');
        $recentTransactions = Order::with('user', 'anime')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAnime',
            'totalRevenue',
            'recentTransactions'
        ));
    }

    // Method untuk menampilkan halaman "Koleksi Saya".
    public function myLibrary()
    {
        $purchasedOrders = Order::where('user_id', Auth::id())
                                  ->whereIn('transaction_status', ['settlement', 'capture'])
                                  ->with('anime')
                                  ->latest()
                                  ->paginate(10);

        return view('anime.library', compact('purchasedOrders'));
    }

    // Untuk menampilkan form tambah anime
    public function create()
    {
        $genres = [
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror',
            'Mecha', 'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural'
        ];
        return view('anime.create', compact('genres'));
    }

    // Untuk menyimpan data anime baru ke database
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'judul' => 'required|string|max:255',
            'genres' => 'required|array',
            'episode' => 'required|integer',
            'sinopsis' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $genreString = implode(', ', $request->genres);

        // Upload gambar
        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->hashName();
            $request->file('gambar')->storeAs('public/anime', $gambar);
        }

        // Simpan data ke database
        AnimeModel::create([
            'judul' => $request->judul,
            'genre' => $genreString,
            'episode' => $request->episode,
            'sinopsis' => $request->sinopsis,
            'gambar' => $gambar,
        ]);

        return redirect()->route('anime.index')->with('success', 'Anime berhasil ditambahkan!');
    }

    // Untuk menampilkan detail anime
    public function show($id)
    {
        $anime = AnimeModel::findOrFail($id);
        return view('anime.show', compact('anime'));
    }

    // Fungsi untuk menampilkan form edit anime
    public function edit($id)
    {
        $anime = AnimeModel::findOrFail($id);
        
        $genres = [
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror',
            'Mecha', 'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural'
        ];

        $selectedGenres = explode(', ', $anime->genre);

        return view('anime.edit', compact('anime', 'genres', 'selectedGenres'));
    }

     // Fungsi untuk menyimpan hasil edit ke database
    public function update(Request $request, $id)
    {
        $anime = AnimeModel::findOrFail($id);

        // 1. Validasi data
        $request->validate([
            'judul' => 'required|string|max:255',
            'genres' => 'required|array',
            'episode' => 'required|integer',
            'sinopsis' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Menggabungkan array genre menjadi string
        $genreString = implode(', ', $request->genres);

        // 3. Menangangani upload/perubahan gambar
        $gambar = $anime->gambar;
        if ($request->hasFile('gambar')) {
            if ($gambar) {
                Storage::delete('public/anime/' . $gambar);
            }
            $gambar = $request->file('gambar')->hashName();
            $request->file('gambar')->storeAs('public/anime', $gambar);
        }

        // 4. Update data ke database
        $anime->update([
            'judul' => $request->judul,
            'genre' => $genreString,
            'episode' => $request->episode,
            'sinopsis' => $request->sinopsis,
            'gambar' => $gambar,
        ]);

        return redirect()->route('anime.index')->with('success', 'Anime berhasil diupdate!');
    }

    // Fungsi untuk menghapus data anime
    public function destroy($id)
    {
        $anime = AnimeModel::findOrFail($id);

        // Hapus file gambar dari storage
        if ($anime->gambar) {
            Storage::delete('public/anime/' . $anime->gambar);
        }

        // Hapus data anime dari database
        $anime->delete();

        return redirect()->route('anime.index')->with('success', 'Anime berhasil dihapus!');
    }
    
     // Export Data ke CSV/Excel
    public function export()
    {
        $animes = AnimeModel::select('judul', 'genre', 'episode', 'sinopsis')->get();
        
        $fileName = 'data_anime_list_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($animes) {
            $file = fopen('php://output', 'w');
            $columns = ['Judul Anime', 'Genre', 'Jumlah Episode', 'Sinopsis'];
            fputcsv($file, $columns, ';');

            foreach ($animes as $anime) {
                $sinopsis_bersih = strip_tags($anime->sinopsis);

                fputcsv($file, [
                    $anime->judul,
                    $anime->genre,
                    $anime->episode,
                    $sinopsis_bersih
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    // Menampilkan halaman daftar QR Code dan form tambah.
    public function qrIndex()
    {
        $qrCodes = QrCode::latest()->paginate(10);
                            
        return view('admin.qr_index', compact('qrCodes'));
    }

    // Membuat, menyimpan, dan mencatat QR Code baru dari GoQR.
    public function qrStore(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'code_id' => 'required|string|max:100|unique:qr_codes,code_id',
        ], [
            'code_id.unique' => 'ID tersebut sudah pernah dibuat. Masukkan ID lain.'
        ]);

        $codeId = $request->code_id;

        // 2. Panggil API GoQR
        $response = Http::get('https://api.qrserver.com/v1/create-qr-code/', [
            'data' => $codeId,
            'size' => '300x300',
            'format' => 'png'
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal menghubungi server QR Code. Coba lagi nanti.');
        }

        // 3. Tentukan Nama File dan Folder
        $filename = $codeId . '.png';
        
        $folderPath = 'qrcodes/' . $filename; 

        // 4. Simpan gambar menggunakan disk 'public' secara eksplisit
        Storage::disk('public')->put($folderPath, $response->body());

        // 5. Dapatkan URL publik yang benar
        $publicUrl = Storage::url($folderPath); 

        // 6. Simpan record ke database
        QrCode::create([
            'code_id' => $codeId,
            'user_id' => Auth::id(),
            'filename' => $filename,
            'public_url' => $publicUrl,
        ]);

        return back()->with('success', 'QR Code untuk ID: ' . $codeId . ' berhasil dibuat!');
    }

    // Download gambar QR Code.
   public function qrDownload($code_id)
    {
        // Cari record QR berdasarkan ID
        $qr = QrCode::findOrFail($code_id);

        // Nama file dan path relatif
        $filePath = 'qrcodes/' . $qr->filename;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $fullPath = storage_path('app/public/' . $filePath);

        return response()->download($fullPath);
    }
}