<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Comment;
use App\Models\GalleryItem;
use App\Models\Program;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalBerita'   => Berita::count(),
            'totalProgram'  => Program::count(),
            'totalGallery'  => GalleryItem::count(),
            'totalKomentar' => Comment::count(),
            'latestBerita'  => Berita::latest()->take(5)->get(),
            'latestProgram' => Program::latest()->take(5)->get(),
        ]);
    }
}
