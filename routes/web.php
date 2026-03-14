<?php

use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\HomeContentController as AdminHomeContentController;
use App\Http\Controllers\Admin\OrgMemberController as AdminOrgMemberController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\ProfileController;
use App\Models\Berita;
use App\Models\Comment;
use App\Models\GalleryItem;
use App\Models\HomeContent;
use App\Models\OrgMember;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// ============================================================
//  PUBLIC ROUTES
// ============================================================

// Home
Route::get('/', function (Request $request) {
    $selayang = HomeContent::where('section', 'selayang-pandang')->first();
    $tentang  = HomeContent::where('section', 'tentang-kami')->first();

    // Gambar slider hero — ambil semua dari DB, fallback ke default jika kosong
    $heroSlideRecords = HomeContent::where('section', 'like', 'hero-slide-%')
        ->orderBy('section')->get();

    if ($heroSlideRecords->isEmpty()) {
        $heroSlides = [
            'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=1600&h=900&fit=crop',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1600&h=900&fit=crop',
            'https://plus.unsplash.com/premium_photo-1661605653366-b1a6a6831cd4?q=80&w=869&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1600&h=900&fit=crop',
        ];
    } else {
        $heroSlides = $heroSlideRecords->map(fn($r) => asset($r->image))->values()->all();
    }

    // Ambil semua periode, urutkan terbaru dulu
    $orgPeriods     = OrgMember::select('period')->distinct()->orderByDesc('period')->pluck('period');
    $latestPeriod   = $orgPeriods->first() ?? '2025-2026';
    $selectedPeriod = $request->query('org_period', $latestPeriod);

    // Pastikan period yang dipilih valid
    if (!$orgPeriods->contains($selectedPeriod)) {
        $selectedPeriod = $latestPeriod;
    }

    $members = OrgMember::where('period', $selectedPeriod)
                   ->orderBy('sort_order')->get()->groupBy('role_group');

    return view('home', compact('selayang', 'tentang', 'members', 'orgPeriods', 'selectedPeriod', 'heroSlides'));
});

// AJAX endpoint: returns org members for a given period as JSON
Route::get('/org-members', function (Request $request) {
    $orgPeriods   = OrgMember::select('period')->distinct()->orderByDesc('period')->pluck('period');
    $latestPeriod = $orgPeriods->first() ?? '2025-2026';
    $period       = $request->query('period', $latestPeriod);

    if (!$orgPeriods->contains($period)) {
        $period = $latestPeriod;
    }

    $members = OrgMember::where('period', $period)->orderBy('sort_order')->get();

    return response()->json(
        $members->map(fn($m) => [
            'position_key' => $m->position_key,
            'title'        => $m->title,
            'name'         => $m->name,
            'photo'        => $m->photo ? asset($m->photo) : null,
            'parent_key'   => $m->parent_key,
        ])->values()
    );
});

Route::get('/welcome', function () {
    return view('welcome');
});

// Gallery
Route::get('/gallery', function (Request $request) {
    $years = GalleryItem::selectRaw('year')->distinct()->orderByDesc('year')->pluck('year')->all();
    $currentYear  = !empty($years) ? $years[0] : (int) date('Y');
    $selectedYear = (int) $request->query('year', $currentYear);

    // Only months that actually have photos for the selected year, oldest first
    $monthsForYear = GalleryItem::where('year', $selectedYear)
        ->distinct()->orderBy('month')->pluck('month')->all();

    $selectedMonth = $request->query('month', 'all');

    $query = GalleryItem::where('year', $selectedYear)->orderBy('month')->orderBy('uploaded_at');
    if ($selectedMonth !== 'all') {
        $query->where('month', (int) $selectedMonth);
    }

    // Group by month: [ 1 => Collection[...], 2 => Collection[...], ... ]
    $photosByMonth = $query->get()->groupBy('month');

    return view('gallery', compact('photosByMonth', 'years', 'selectedYear', 'monthsForYear', 'selectedMonth'));
})->name('gallery.index');

Route::post('/gallery/upload', function (Request $request) {
    $validated = $request->validate([
        'title'         => 'nullable|string|max:150',
        'activity_date' => 'required|date|before_or_equal:today',
        'photos'        => 'required|array',
        'photos.*'      => 'image|max:4096',
    ]);

    $activityDate = \Carbon\Carbon::parse($validated['activity_date']);
    $year  = (int) $activityDate->year;
    $month = (int) $activityDate->month;
    $uploadedAt = $activityDate->startOfDay();

    $uploadDir = public_path('images/gallery/uploads/' . $year . '/' . str_pad((string) $month, 2, '0', STR_PAD_LEFT));
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    foreach ($request->file('photos', []) as $file) {
        if (!$file || !$file->isValid()) continue;

        $filename     = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);
        $relativePath = 'images/gallery/uploads/' . $year . '/' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '/' . $filename;

        GalleryItem::create([
            'title'       => $validated['title'] ?? null,
            'path'        => $relativePath,
            'year'        => $year,
            'month'       => $month,
            'uploaded_at' => $uploadedAt,
        ]);
    }

    return redirect()->route('gallery.index')->with('status', 'Foto berhasil diupload.');
})->name('gallery.upload');

Route::get('/gallery/data', function (Request $request) {
    $year = (int) $request->query('year', date('Y'));
    $monthsForYear = GalleryItem::where('year', $year)->distinct()->orderBy('month')->pluck('month')->all();
    $selectedMonth = $request->query('month', 'all');

    $query = GalleryItem::where('year', $year)->orderBy('month')->orderBy('uploaded_at');
    if ($selectedMonth !== 'all') {
        $query->where('month', (int) $selectedMonth);
    }

    $grouped = $query->get()->groupBy('month');
    $photosByMonth = [];
    foreach ($grouped as $m => $photos) {
        $photosByMonth[(int)$m] = $photos->map(function ($p) {
            return [
                'src'           => asset($p->path),
                'title'         => $p->title,
                'date'          => \Carbon\Carbon::parse($p->uploaded_at)->format('d/m/Y'),
                'dateFormatted' => \Carbon\Carbon::parse($p->uploaded_at)->translatedFormat('d F Y'),
            ];
        })->values();
    }

    return response()->json([
        'selectedYear'  => $year,
        'selectedMonth' => $selectedMonth,
        'monthsForYear' => $monthsForYear,
        'photosByMonth' => $photosByMonth,
    ]);
})->name('gallery.data');

// Redirect lama
Route::get('/tentang',  fn () => redirect('/#tentang'));
Route::get('/selayang', fn () => redirect('/#selayang'));

// Program Kegiatan
Route::get('/program', function (Request $request) {
    $query = Program::query();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('month') && is_numeric($request->month)) {
        $query->where('month', (int) $request->month);
    }
    if ($request->filled('year') && is_numeric($request->year)) {
        $query->where('year', (int) $request->year);
    }

    $programs = $query->orderByDesc('year')
                      ->orderByDesc('month')
                      ->orderByDesc('date')
                      ->get();

    $years          = Program::select('year')->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');
    $selectedStatus = $request->status ?? '';
    $selectedMonth  = $request->month  ?? '';
    $selectedYear   = $request->year   ?? '';

    return view('program', compact('programs', 'years', 'selectedStatus', 'selectedMonth', 'selectedYear'));
})->name('program.index');

Route::get('/program/{slug}', function (string $slug) {
    $program = Program::where('slug', $slug)->firstOrFail();

    // Hitung view sekali per sesi agar refresh tidak menambah angka terus
    $viewKey = 'viewed_program_' . $slug;
    if (!session()->has($viewKey)) {
        $program->increment('views');
        session()->put($viewKey, true);
    }

    $hasLiked = session()->has('liked_program_' . $slug);

    $uploadDir   = public_path('images/program/uploads/' . $slug);
    $uploadedDocs = [];
    if (is_dir($uploadDir)) {
        foreach (scandir($uploadDir) as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $uploadedDocs[] = 'images/program/uploads/' . $slug . '/' . $file;
            }
        }
    }

    return view('program-detail', compact('program', 'uploadedDocs', 'hasLiked'));
})->name('program.detail');

Route::post('/program/{slug}/like', function (Request $request, string $slug) {
    $program = Program::where('slug', $slug)->firstOrFail();
    $likeKey = 'liked_program_' . $slug;

    // Tambah like hanya jika belum pernah like dalam sesi ini
    if (!session()->has($likeKey)) {
        $program->increment('likes');
        session()->put($likeKey, true);
        $program->refresh();
    }

    if ($request->wantsJson()) {
        return response()->json([
            'views'        => $program->views,
            'likes'        => $program->likes,
            'alreadyLiked' => true,
        ]);
    }
    return back();
})->name('program.like');

Route::post('/program/{slug}/dokumentasi-upload', function (Request $request, string $slug) {
    $request->validate([
        'photos'   => 'required|array|min:1',
        'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:8192',
    ]);

    $program = Program::where('slug', $slug)->firstOrFail();
    $now     = now();

    // Gunakan tanggal kegiatan program untuk menentukan bulan/tahun di galeri.
    // Jika program punya field date (Y-m-d), pakai itu; fallback ke now().
    $programDate = null;
    if (!empty($program->date)) {
        try {
            $d = $program->date;
            if (str_contains($d, '/')) {
                $clean = preg_replace('/\s*\/\s*/', '/', trim($d));
                $programDate = \Carbon\Carbon::createFromFormat('d/m/Y', $clean);
            } else {
                $programDate = \Carbon\Carbon::createFromFormat('Y-m-d', $d);
            }
        } catch (\Exception $e) {
            $programDate = null;
        }
    }
    $galleryDate = $programDate ?? $now;

    $uploaded = 0;
    foreach ($request->file('photos', []) as $key => $photo) {
        if (!$photo || !$photo->isValid()) continue;

        $filename     = $now->format('YmdHis') . '_' . $key . '_' . Str::random(6) . '.' . $photo->getClientOriginalExtension();
        $uploadDir    = public_path('images/program/uploads/' . $slug);
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $photo->move($uploadDir, $filename);

        $relativePath = 'images/program/uploads/' . $slug . '/' . $filename;

        // Simpan ke galeri dengan bulan/tahun sesuai tanggal kegiatan (bukan tanggal upload)
        GalleryItem::create([
            'title'       => $program->title,
            'path'        => $relativePath,
            'year'        => $galleryDate->year,
            'month'       => $galleryDate->month,
            'uploaded_at' => $now,
        ]);

        $uploaded++;
    }

    return back()->with('upload_success', $uploaded . ' foto berhasil diupload dan disimpan ke galeri.');
})->name('program.upload');

// Berita
Route::get('/berita', function (Request $request) {
    $q = trim($request->query('q', ''));

    // ── MODE PENCARIAN ──────────────────────────────────────────────
    if ($q !== '') {
        $qLower = mb_strtolower($q);
        $searchResults = Berita::latest()->get()->filter(function ($b) use ($qLower) {
            if (mb_strpos(mb_strtolower($b->title), $qLower) !== false) return true;
            if (is_array($b->paragraphs)) {
                foreach ($b->paragraphs as $p) {
                    if (mb_strpos(mb_strtolower((string) $p), $qLower) !== false) return true;
                }
            }
            return false;
        })->values();

        return view('berita', [
            'q'              => $q,
            'searchResults'  => $searchResults,
            'featured'       => null,
            'todayBerita'    => collect(),
            'olderBerita'    => collect(),
            'olderBeritaSisa'=> 0,
        ]);
    }

    // ── MODE NORMAL ─────────────────────────────────────────────────
    $featured    = Berita::where('is_featured', true)->latest()->first();
    $allBerita   = Berita::latest()->get();

    // Normalisasi date ke format Y-m-d untuk perbandingan konsisten
    $todayStr = now()->format('Y-m-d');

    // Fungsi normalisasi: ubah semua format date ke Y-m-d
    $normalizeDate = function (string $date): string {
        $d = trim($date);
        if (!$d) return '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return $d;
        $clean = preg_replace('/\s*\/\s*/', '/', $d);
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $clean)->format('Y-m-d');
        } catch (\Exception $e) {
            return $d;
        }
    };

    // Berita yang tanggalnya hari ini → tampil di grid utama
    $todayBerita = $allBerita->filter(fn($b) =>
        $normalizeDate($b->date ?? '') === $todayStr
    )->values();

    // Berita hari-hari sebelumnya (max 30 hari) → panel tersembunyi, dibatasi 20 terbaru
    $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d');

    $allOlder = $allBerita->filter(function ($b) use ($normalizeDate, $todayStr, $thirtyDaysAgo) {
        $nd = $normalizeDate($b->date ?? '');
        return $nd !== '' && $nd !== $todayStr && $nd >= $thirtyDaysAgo;
    })->sortByDesc(fn($b) => $normalizeDate($b->date ?? ''))->values();

    // Berita lebih dari 30 hari → masuk arsip
    $allArchive = $allBerita->filter(function ($b) use ($normalizeDate, $thirtyDaysAgo) {
        $nd = $normalizeDate($b->date ?? '');
        return $nd !== '' && $nd < $thirtyDaysAgo;
    })->values();

    $olderBerita     = $allOlder->take(20);
    $olderBeritaSisa = $allArchive->count() + max(0, $allOlder->count() - 20);

    return view('berita', compact('featured', 'todayBerita', 'olderBerita', 'olderBeritaSisa', 'q'));
})->name('berita.index');

// Arsip berita lama >30 hari (dengan filter per bulan)
Route::get('/berita/arsip', function () {
    $normalizeDate = function (string $date): string {
        $d = trim($date);
        if (!$d) return '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return $d;
        $clean = preg_replace('/\s*\/\s*/', '/', $d);
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $clean)->format('Y-m-d');
        } catch (\Exception $e) { return $d; }
    };

    $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d');

    // Ambil hanya berita >30 hari lalu
    $allArchive = Berita::latest()->get()
        ->filter(function ($b) use ($normalizeDate, $thirtyDaysAgo) {
            $nd = $normalizeDate($b->date ?? '');
            return $nd !== '' && $nd < $thirtyDaysAgo;
        })
        ->sortByDesc(fn($b) => $normalizeDate($b->date ?? ''))
        ->values();

    // Daftar bulan yang tersedia (format Y-m, diurutkan terbaru dulu)
    $availableMonths = $allArchive->map(function ($b) use ($normalizeDate) {
        $nd = $normalizeDate($b->date ?? '');
        return $nd ? substr($nd, 0, 7) : null;
    })->filter()->unique()->sort()->reverse()->values()->all();

    // Filter berdasarkan bulan yang dipilih (opsional)
    $selectedMonth = request('bulan'); // format: '2025-08'
    if ($selectedMonth && in_array($selectedMonth, $availableMonths)) {
        $filtered = $allArchive->filter(
            fn($b) => substr($normalizeDate($b->date ?? ''), 0, 7) === $selectedMonth
        )->values();
    } else {
        $selectedMonth = null;
        $filtered = $allArchive;
    }

    // Manual pagination
    $perPage     = 20;
    $currentPage = (int) request('page', 1);
    $total       = $filtered->count();
    $items       = $filtered->forPage($currentPage, $perPage);
    $paginator   = new \Illuminate\Pagination\LengthAwarePaginator(
        $items, $total, $perPage, $currentPage,
        ['path' => route('berita.arsip'), 'query' => array_filter(['bulan' => $selectedMonth])]
    );

    return view('berita-arsip', [
        'berita'          => $paginator,
        'normalizeDate'   => $normalizeDate,
        'availableMonths' => $availableMonths,
        'selectedMonth'   => $selectedMonth,
        'totalArsip'      => $allArchive->count(),
    ]);
})->name('berita.arsip');

// Unified search suggestions (berita + program)
Route::get('/search/suggestions', function (Request $request) {
    $q = trim($request->query('q', ''));
    if (mb_strlen($q) < 2) return response()->json([]);
    $qLower = mb_strtolower($q);

    $beritaResults = Berita::latest()->get()
        ->filter(fn($b) => mb_strpos(mb_strtolower($b->title), $qLower) !== false)
        ->take(4)
        ->map(fn($b) => [
            'type'  => 'berita',
            'title' => $b->title,
            'slug'  => $b->slug,
            'url'   => route('berita.detail', ['slug' => $b->slug]),
            'image' => asset($b->image ?? 'images/news/latihansingkatpembalutanringan.png'),
            'meta'  => $b->date ?? '',
        ])->values();

    $programResults = Program::latest()->get()
        ->filter(fn($p) => mb_strpos(mb_strtolower($p->title), $qLower) !== false)
        ->take(3)
        ->map(fn($p) => [
            'type'  => 'program',
            'title' => $p->title,
            'slug'  => $p->slug,
            'url'   => route('program.detail', ['slug' => $p->slug]),
            'image' => asset($p->image ?? 'images/program/default.png'),
            'meta'  => $p->date ?? '',
        ])->values();

    return response()->json($beritaResults->merge($programResults)->values());
})->name('search.suggestions');

// Unified search results page
Route::get('/search', function (Request $request) {
    $q = trim($request->query('q', ''));
    if ($q === '') return redirect()->route('berita.index');

    $qLower = mb_strtolower($q);

    $beritaResults = Berita::latest()->get()->filter(function ($b) use ($qLower) {
        if (mb_strpos(mb_strtolower($b->title), $qLower) !== false) return true;
        if (is_array($b->paragraphs)) {
            foreach ($b->paragraphs as $p) {
                if (mb_strpos(mb_strtolower((string)$p), $qLower) !== false) return true;
            }
        }
        return false;
    })->values();

    $programResults = Program::latest()->get()->filter(function ($p) use ($qLower) {
        if (mb_strpos(mb_strtolower($p->title), $qLower) !== false) return true;
        if (mb_strpos(mb_strtolower($p->intro ?? ''), $qLower) !== false) return true;
        if (is_array($p->paragraphs)) {
            foreach ($p->paragraphs as $par) {
                if (mb_strpos(mb_strtolower((string)$par), $qLower) !== false) return true;
            }
        }
        return false;
    })->values();

    return view('search', compact('q', 'beritaResults', 'programResults'));
})->name('search.index');

// Berita search suggestions (JSON) — kept for compatibility
Route::get('/berita/suggestions', function (Request $request) {
    return redirect()->route('search.suggestions', ['q' => $request->query('q', '')]);
})->name('berita.suggestions');

// Berita detail
Route::get('/berita/{slug}', function (string $slug) {
    $article = Berita::where('slug', $slug)->firstOrFail();

    // Hitung view sekali per sesi agar refresh tidak menambah angka terus
    $viewKey = 'viewed_berita_' . $slug;
    if (!session()->has($viewKey)) {
        $article->increment('views');
        session()->put($viewKey, true);
    }

    $hasLiked     = session()->has('liked_berita_' . $slug);
    $comments     = $article->comments()->latest()->get();
    $otherBerita  = Berita::where('id', '!=', $article->id)->latest()->limit(8)->get();
    return view('berita-detail', compact('article', 'comments', 'otherBerita', 'hasLiked'));
})->name('berita.detail');

Route::post('/berita/{slug}/like', function (Request $request, string $slug) {
    $article = Berita::where('slug', $slug)->firstOrFail();
    $likeKey = 'liked_berita_' . $slug;

    // Tambah like hanya jika belum pernah like dalam sesi ini
    if (!session()->has($likeKey)) {
        $article->increment('likes');
        session()->put($likeKey, true);
        $article->refresh();
    }

    if ($request->wantsJson()) {
        return response()->json([
            'views'        => $article->views,
            'likes'        => $article->likes,
            'alreadyLiked' => true,
        ]);
    }
    return back();
})->name('berita.like');

Route::post('/berita/{slug}/comment', function (Request $request, string $slug) {
    $validated = $request->validate([
        'name'    => 'required|string|max:100',
        'message' => 'required|string|max:1000',
    ]);
    $article = Berita::where('slug', $slug)->firstOrFail();
    Comment::create([
        'berita_id' => $article->id,
        'name'      => $validated['name'],
        'message'   => $validated['message'],
    ]);
    return back();
})->name('berita.comment');

// ============================================================
//  ADMIN ROUTES (auth + admin role required)
// ============================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Berita CRUD
    Route::resource('berita', AdminBeritaController::class)->except(['show']);

    // Program CRUD
    Route::resource('program', AdminProgramController::class)->except(['show']);

    // Konten Home
    Route::get('home-content',    [AdminHomeContentController::class, 'index'])->name('home-content.index');
    Route::post('home-content',   [AdminHomeContentController::class, 'update'])->name('home-content.update');

    // Struktur Organisasi
    Route::resource('org', AdminOrgMemberController::class)->except(['show']);

    // Galeri
    Route::get('gallery',               [AdminGalleryController::class, 'index'])->name('gallery.index');
    Route::delete('gallery/{id}',       [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::post('gallery/bulk-destroy', [AdminGalleryController::class, 'bulkDestroy'])->name('gallery.bulkDestroy');

    // Dokumentasi Program (hapus foto yang diupload pengunjung)
    Route::post('program/{slug}/dokumentasi-delete', function (\Illuminate\Http\Request $request, string $slug) {
        $request->validate(['path' => 'required|string']);
        $relativePath   = $request->input('path');
        $expectedPrefix = 'images/program/uploads/' . $slug . '/';
        if (strpos($relativePath, $expectedPrefix) !== 0) return back();
        $fullPath = public_path($relativePath);
        if (is_file($fullPath)) @unlink($fullPath);
        \App\Models\GalleryItem::where('path', $relativePath)->delete();
        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    })->name('program.deleteDoc');

    // Komentar
    Route::get('comment',          [AdminCommentController::class, 'index'])->name('comment.index');
    Route::delete('comment/{id}',  [AdminCommentController::class, 'destroy'])->name('comment.destroy');

    // Profile Breeze (opsional)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

