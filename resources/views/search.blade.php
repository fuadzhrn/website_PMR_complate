<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian "{{ $q }}" — PMR Wira 242</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/berita.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">

    <style>
        body { background: #f4f1ed; font-family: 'Poppins', sans-serif; }

        .search-page-header {
            background: radial-gradient(circle at top center, #4a2f24 0%, #3b241f 50%, #22140f 100%);
            padding: 122px 24px 40px;
            text-align: center;
            color: #f7f2ec;
        }

        .search-page-header h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .search-page-header h1 em {
            color: #e8721a;
            font-style: normal;
        }

        .search-page-header p {
            font-size: 14px;
            color: rgba(232,211,178,.55);
            margin: 0;
        }

        .search-page-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(232,211,178,.6);
            text-decoration: none;
            margin-bottom: 18px;
            transition: color .2s;
        }

        .search-page-back:hover { color: #f7f2ec; }

        .search-page-body {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        /* Section title */
        .search-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #2c1a14;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid;
        }

        .search-section-title.berita  { border-color: #c0392b; }
        .search-section-title.program { border-color: #1a6eb5; }

        .search-section-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 99px;
            color: #fff;
        }

        .search-section-badge.berita  { background: #c0392b; }
        .search-section-badge.program { background: #1a6eb5; }

        .search-count {
            font-size: 12px;
            font-weight: 500;
            color: #9c8b82;
            margin-left: auto;
        }

        /* Cards grid */
        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 48px;
        }

        .search-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 280px;
        }

        .search-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0,0,0,.07);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform .2s, box-shadow .2s;
        }

        .search-card-link:hover .search-card {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.13);
        }

        .search-card-thumb {
            flex-shrink: 0;
        }

        .search-card-thumb img {
            width: 100%;
            height: 148px;
            object-fit: cover;
        }

        .search-card-body {
            padding: 12px 14px;
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .search-card-title {
            font-size: 14.5px;
            font-weight: 700;
            line-height: 1.35;
            color: #2c1a14;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        .search-card-text {
            font-size: 12px;
            color: #7a6b62;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        .search-card-meta {
            font-size: 11px;
            color: #b09a8e;
            margin-top: auto;
        }

        /* Empty state */
        .search-no-results {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 48px 20px;
            text-align: center;
            color: #8c7b72;
        }

        .search-no-results svg { margin-bottom: 14px; opacity: .4; }
        .search-no-results p   { font-size: 15px; margin: 0; }
        .search-no-results small { font-size: 12.5px; color: #a99080; margin-top: 6px; display: block; }

        @media (max-width: 600px) {
            .search-page-header { padding: 108px 16px 28px; }
            .search-grid { grid-template-columns: 1fr 1fr; }
            .search-card-link { height: 240px; }
            .search-card-thumb img { height: 110px; }
        }
    </style>
</head>
<body>
    @include('partials.topbar')
    @include('partials.sidebar')

    <!-- Page Header -->
    <div class="search-page-header">
        <a href="javascript:history.back()" class="search-page-back">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
            Kembali
        </a>
        <h1>Hasil pencarian <em>{{ $q }}</em></h1>
        <p>{{ $beritaResults->count() + $programResults->count() }} hasil ditemukan dari berita &amp; program kegiatan</p>
    </div>

    <div class="search-page-body">

        {{-- ── BERITA ── --}}
        <div class="search-section-title berita">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="2"><path d="M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18l-4-2-4 2-4-2-4 2Z"/><path d="M8 10h8M8 14h5"/></svg>
            Berita
            <span class="search-section-badge berita">{{ $beritaResults->count() }}</span>
            <span class="search-count">hasil</span>
        </div>

        @if($beritaResults->isEmpty())
            <div class="search-no-results" style="margin-bottom:40px">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <p>Tidak ada berita yang cocok dengan &ldquo;<strong>{{ $q }}</strong>&rdquo;</p>
            </div>
        @else
            <div class="search-grid">
                @foreach($beritaResults as $item)
                <a href="{{ route('berita.detail', ['slug' => $item->slug]) }}" class="search-card-link">
                    <article class="search-card">
                        <div class="search-card-thumb">
                            <img src="{{ asset($item->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $item->title }}">
                        </div>
                        <div class="search-card-body">
                            <h3 class="search-card-title">{{ $item->title }}</h3>
                            <p class="search-card-text">{{ is_array($item->paragraphs) && count($item->paragraphs) ? Str::limit($item->paragraphs[0], 100) : '' }}</p>
                            <span class="search-card-meta">{{ $item->date ?? '' }}</span>
                        </div>
                    </article>
                </a>
                @endforeach
            </div>
        @endif

        {{-- ── PROGRAM KEGIATAN ── --}}
        <div class="search-section-title program">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a6eb5" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            Program Kegiatan
            <span class="search-section-badge program">{{ $programResults->count() }}</span>
            <span class="search-count">hasil</span>
        </div>

        @if($programResults->isEmpty())
            <div class="search-no-results">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#1a6eb5" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <p>Tidak ada program yang cocok dengan &ldquo;<strong>{{ $q }}</strong>&rdquo;</p>
            </div>
        @else
            <div class="search-grid">
                @foreach($programResults as $item)
                <a href="{{ route('program.detail', ['slug' => $item->slug]) }}" class="search-card-link">
                    <article class="search-card">
                        <div class="search-card-thumb">
                            <img src="{{ asset($item->image ?? 'images/program/default.png') }}" alt="{{ $item->title }}">
                        </div>
                        <div class="search-card-body">
                            <h3 class="search-card-title">{{ $item->title }}</h3>
                            <p class="search-card-text">{{ $item->intro ? Str::limit($item->intro, 100) : (is_array($item->paragraphs) && count($item->paragraphs) ? Str::limit($item->paragraphs[0], 100) : '') }}</p>
                            <span class="search-card-meta">{{ $item->date ?? '' }}</span>
                        </div>
                    </article>
                </a>
                @endforeach
            </div>
        @endif

    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>
