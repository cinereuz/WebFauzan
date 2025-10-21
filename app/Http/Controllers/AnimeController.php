<?php

namespace App\Http\Controllers;

// Ditambahkan untuk fitur baru
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Bawaan dari kode Anda
use Illuminate\Http\Request;
use App\Models\AnimeModel;
use Illuminate\Support\Facades\Storage;

class AnimeController extends Controller
{
    /**
     * MODIFIKASI: Menampilkan halaman awal dengan data anime yang sudah dibeli.
     */
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

    /**
     * BARU: Method untuk menampilkan dashboard admin.
     */
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

    /**
     * BARU: Method untuk menampilkan halaman "Koleksi Saya".
     */
    public function myLibrary()
    {
        $purchasedOrders = Order::where('user_id', Auth::id())
                                  ->whereIn('transaction_status', ['settlement', 'capture'])
                                  ->with('anime')
                                  ->latest()
                                  ->paginate(10);

        return view('anime.library', compact('purchasedOrders'));
    }

    // ==================================================================
    // == DI BAWAH INI ADALAH SEMUA METHOD LAMA ANDA (TIDAK DIUBAH) ==
    // ==================================================================

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
            $request->file('gambar')->storeAs('public/anime', $gambar); // Disesuaikan path-nya
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
}