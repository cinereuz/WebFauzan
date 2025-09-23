<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeModel;

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
}
