<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Arsip Berita</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/berita.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">

    <style>
        body { padding-top: 80px; }

        .arsip-wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 32px 16px 60px;
        }

        .arsip-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .arsip-back {
            font-size: 13px;
            color: #7a3b2e;
            text-decoration: none;
        }

        .arsip-back:hover { text-decoration: underline; }

        .arsip-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .arsip-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .arsip-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 12px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .arsip-link {
            display: flex;
            gap: 12px;
            text-decoration: none;
            color: inherit;
            flex: 1;
        }

        .arsip-link:hover .arsip-item-title { text-decoration: underline; }

        .arsip-thumb {
            width: 80px;
            height: 56px;
            border-radius: 6px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .arsip-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .arsip-body {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .arsip-item-title {
            font-size: 14px;
            font-weight: 600;
        }

        .arsip-item-desc {
            font-size: 12px;
            color: #666;
        }

        .arsip-item-meta {
            font-size: 11px;
            color: #9b8f86;
            white-space: nowrap;
            margin-left: 12px;
        }

        .arsip-pagination {
            margin-top: 28px;
            display: flex;
            justify-content: center;
        }

        .arsip-pagination .page-link {
            color: #7a3b2e;
            border-color: #d0cbc7;
        }

        .arsip-pagination .page-item.active .page-link {
            background-color: #7a3b2e;
            border-color: #7a3b2e;
            color: #fff;
        }

        /* ── Month filter chips ── */
        .arsip-month-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .arsip-month-chip {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid #d4c5bf;
            color: #7a3b2e;
            background: #fff;
            transition: background .15s, color .15s;
        }

        .arsip-month-chip:hover,
        .arsip-month-chip.active {
            background: #7a3b2e;
            color: #fff;
            border-color: #7a3b2e;
        }

        .arsip-filter-label {
            font-size: 12px;
            color: #999;
            align-self: center;
            margin-right: 4px;
        }
    </style>
</head>
<body>
    @include('partials.topbar')
    @include('partials.sidebar')

    <div class="arsip-wrap">
        <div class="arsip-header">
            <a href="{{ route('berita.index') }}" class="arsip-back">&larr; Kembali ke Berita</a>
            <span style="color:#ccc">|</span>
            <h1 class="arsip-title">Arsip Berita</h1>
            <span style="font-size:13px;color:#999;margin-left:auto;">{{ number_format($totalArsip, 0, ',', '.') }} berita</span>
        </div>

        {{-- Filter per bulan --}}
        @if(count($availableMonths) > 0)
        @php
            $bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        @endphp
        <div class="arsip-month-filter">
            <span class="arsip-filter-label">Filter:</span>
            <a href="{{ route('berita.arsip') }}"
               class="arsip-month-chip {{ is_null($selectedMonth) ? 'active' : '' }}">
               Semua
            </a>
            @foreach($availableMonths as $ym)
            @php
                [$y, $m] = explode('-', $ym);
                $label = $bulanId[(int)$m] . ' ' . $y;
            @endphp
            <a href="{{ route('berita.arsip', ['bulan' => $ym]) }}"
               class="arsip-month-chip {{ $selectedMonth === $ym ? 'active' : '' }}">
               {{ $label }}
            </a>
            @endforeach
        </div>
        @endif

        <div class="arsip-list">
            @forelse($berita as $item)
            @php
                $rawD = trim($item->date ?? '');
                $carbonD = null;
                if ($rawD) {
                    try {
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawD)) {
                            $carbonD = \Carbon\Carbon::createFromFormat('Y-m-d', $rawD)->startOfDay();
                        } else {
                            $clean = preg_replace('/\s*\/\s*/', '/', $rawD);
                            $carbonD = \Carbon\Carbon::createFromFormat('d/m/Y', $clean)->startOfDay();
                        }
                    } catch (\Exception $e) {}
                }
                if ($carbonD) {
                    $today    = now()->startOfDay();
                    $diffDays = (int) $carbonD->diffInDays($today);
                    $isPast   = $carbonD->lt($today);
                    if (!$isPast) {
                        $dateLabel = 'hari ini';
                    } elseif ($diffDays === 1) {
                        $dateLabel = 'kemarin';
                    } elseif ($diffDays <= 7) {
                        $dateLabel = $diffDays . ' hari lalu';
                    } else {
                        $bulanId   = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        $dateLabel = $carbonD->day . ' ' . $bulanId[(int)$carbonD->month] . ' ' . $carbonD->year;
                    }
                } else {
                    $dateLabel = $rawD ?: '-';
                }
            @endphp
            <div class="arsip-item">
                <a href="{{ route('berita.detail', ['slug' => $item->slug]) }}" class="arsip-link">
                    <div class="arsip-thumb">
                        <img src="{{ asset($item->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $item->title }}">
                    </div>
                    <div class="arsip-body">
                        <span class="arsip-item-title">{{ $item->title }}</span>
                        @if(is_array($item->paragraphs) && count($item->paragraphs))
                        <span class="arsip-item-desc">{{ Str::limit($item->paragraphs[0], 120) }}</span>
                        @endif
                    </div>
                </a>
                <span class="arsip-item-meta">{{ $dateLabel }}</span>
            </div>
            @empty
            <p class="text-muted text-center py-5">Tidak ada arsip berita.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($berita->hasPages())
        <div class="arsip-pagination">
            {{ $berita->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>
