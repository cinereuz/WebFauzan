<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeModel;
use Illuminate\Support\Facades\Storage;

class AnimeController extends Controller
{
    // Untuk menampilkan halaman awal
    public function index()
    {
        $anime = AnimeModel::latest()->paginate(10);
        return view('anime.index', compact('anime'));
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
            $request->file('gambar')->storeAs('anime', $gambar);
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
                Storage::delete('anime/' . $gambar);
            }
            $gambar = $request->file('gambar')->hashName();
            $request->file('gambar')->storeAs('anime', $gambar);
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
            Storage::delete('anime/' . $anime->gambar);
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