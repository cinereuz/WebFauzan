<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function index()
    {
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
        
        if ($location->image) {
            Storage::disk('public')->delete('locations/' . $location->image);
        }

        $location->delete();
        
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}