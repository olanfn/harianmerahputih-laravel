<?php

namespace App\Http\Controllers;

use App\Models\EventPhoto;
use App\Models\TvVideo;
use Illuminate\View\View;

class ShowcaseController extends Controller
{
    public function photos(): View { return view('showcase.index', ['title' => 'Foto Peristiwa', 'items' => EventPhoto::published()->with('media')->orderBy('sort_order')->latest('published_at')->paginate(12), 'kind' => 'Foto Peristiwa']); }
    public function tv(): View { return view('showcase.index', ['title' => 'Merah Putih TV', 'items' => TvVideo::published()->with('media')->orderBy('sort_order')->latest('published_at')->paginate(12), 'kind' => 'Merah Putih TV']); }
}
