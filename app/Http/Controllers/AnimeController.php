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
use Intervention\Image\Laravel\Facades\Image; 

class AnimeController extends Controller
{
    public function index()
    {
        $anime = AnimeModel::latest()->paginate(10);

        $purchasedAnimeIds = [];
        if (Auth::check()) {
            $purchasedAnimeIds = Order::where('user_id', Auth::id())
                                      ->whereIn('transaction_status', ['settlement', 'capture'])
                                      ->pluck('anime_id')
                                      ->toArray();
        }
        
        return view('anime.index', compact('anime', 'purchasedAnimeIds'));
    }

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

    public function myLibrary()
    {
        $purchasedOrders = Order::where('user_id', Auth::id())
                                  ->whereIn('transaction_status', ['settlement', 'capture'])
                                  ->with('anime')
                                  ->latest()
                                  ->paginate(10);

        return view('anime.library', compact('purchasedOrders'));
    }

    public function create()
    {
        $genres = [
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror',
            'Mecha', 'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural'
        ];
        return view('anime.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'genres' => 'required|array',
            'episode' => 'required|integer',
            'sinopsis' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $genreString = implode(', ', $request->genres);

        $gambarName = null;
        
        if ($request->hasFile('gambar')) {
            // 1. Mengambil file asli
            $file = $request->file('gambar');
            
            // 2. Membuat nama file unik dengan ekstensi .webp
            $gambarName = md5(uniqid()) . '.webp';
            
            // 3. Memastikan folder penyimpanan ada
            $path = storage_path('app/public/anime');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // 4. Memproses Gambar menggunakan Intervention Image
            $image = Image::read($file);
            
            // Resize & Crop (Cover) agar ukuran sama persis
            $image->cover(300, 450); 
            
            // Encode ke WebP dengan kualitas 75%
            $image->toWebp(75)->save($path . '/' . $gambarName);
        }

        AnimeModel::create([
            'judul' => $request->judul,
            'genre' => $genreString,
            'episode' => $request->episode,
            'sinopsis' => $request->sinopsis,
            'gambar' => $gambarName,
        ]);

        return redirect()->route('anime.index')->with('success', 'Anime berhasil ditambahkan!');
    }

    public function show($id)
    {
        $anime = AnimeModel::findOrFail($id);
        return view('anime.show', compact('anime'));
    }

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

    public function update(Request $request, $id)
    {
        $anime = AnimeModel::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'genres' => 'required|array',
            'episode' => 'required|integer',
            'sinopsis' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $genreString = implode(', ', $request->genres);

        $gambarName = $anime->gambar;

        if ($request->hasFile('gambar')) {
            if ($gambarName) {
                Storage::delete('public/anime/' . $gambarName);
            }

            $file = $request->file('gambar');
            $gambarName = md5(uniqid()) . '.webp';
            $path = storage_path('app/public/anime');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $image = Image::read($file);
            $image->cover(300, 450);
            $image->toWebp(75)->save($path . '/' . $gambarName);
        }

        $anime->update([
            'judul' => $request->judul,
            'genre' => $genreString,
            'episode' => $request->episode,
            'sinopsis' => $request->sinopsis,
            'gambar' => $gambarName,
        ]);

        return redirect()->route('anime.index')->with('success', 'Anime berhasil diupdate!');
    }

    public function destroy($id)
    {
        $anime = AnimeModel::findOrFail($id);

        if ($anime->gambar) {
            Storage::delete('public/anime/' . $anime->gambar);
        }

        $anime->delete();

        return redirect()->route('anime.index')->with('success', 'Anime berhasil dihapus!');
    }
    
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

    public function qrIndex()
    {
        $qrCodes = QrCode::latest()->paginate(10);             
        return view('admin.qr_index', compact('qrCodes'));
    }

    public function qrStore(Request $request)
    {
        $request->validate([
            'code_id' => 'required|string|max:100|unique:qr_codes,code_id',
        ], [
            'code_id.unique' => 'ID tersebut sudah pernah dibuat. Masukkan ID lain.'
        ]);

        $codeId = $request->code_id;

        $response = Http::get('https://api.qrserver.com/v1/create-qr-code/', [
            'data' => $codeId,
            'size' => '300x300',
            'format' => 'png'
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal menghubungi server QR Code. Coba lagi nanti.');
        }

        $filename = $codeId . '.png';
        $folderPath = 'qrcodes/' . $filename; 

        Storage::disk('public')->put($folderPath, $response->body());
        $publicUrl = Storage::url($folderPath); 

        QrCode::create([
            'code_id' => $codeId,
            'user_id' => Auth::id(),
            'filename' => $filename,
            'public_url' => $publicUrl,
        ]);

        return back()->with('success', 'QR Code untuk ID: ' . $codeId . ' berhasil dibuat!');
    }

   public function qrDownload($code_id)
    {
        $qr = QrCode::findOrFail($code_id);
        $filePath = 'qrcodes/' . $qr->filename;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $fullPath = storage_path('app/public/' . $filePath);

        return response()->download($fullPath);
    }
}