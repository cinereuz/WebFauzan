<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function index()
    {
        // Ambil semua lokasi untuk ditampilkan di peta
        $locations = Location::latest()->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->hashName();
            // PERBAIKAN: Gunakan disk 'public' dan simpan langsung di folder 'locations'
            // Hasilnya akan ada di: storage/app/public/locations/namafile.jpg
            $request->file('image')->storeAs('locations', $image, 'public');
        }

        Location::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
            'image' => $image,
        ]);

        return back()->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        
        // Hapus file gambar dari storage jika ada
        if ($location->image) {
            // PERBAIKAN: Hapus menggunakan disk 'public' path 'locations/'
            Storage::disk('public')->delete('locations/' . $location->image);
        }

        $location->delete();
        
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}