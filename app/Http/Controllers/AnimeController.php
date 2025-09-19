<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeModel;

class AnimeController extends Controller
{
    public function index()
    {
        $anime = AnimeModel::latest()->paginate(10);
        return view('anime.index', compact('anime'));
    }
}
