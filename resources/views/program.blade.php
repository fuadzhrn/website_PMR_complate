<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Program Kegiatan</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Montserrat & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <!-- Program Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/program.css') }}">

    <!-- Shared Topbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">
</head>
<body class="with-fixed-navbar">
    @include('partials.topbar')

    <!-- Main Content -->
    <main class="program-main">
        <!-- Program Kegiatan Section -->
        <section class="program-section" id="program">
            <div class="section-header">
                <div class="section-title-block">
                    <h1 class="section-title">Program Kegiatan</h1>
                    <div class="section-underline"></div>
                </div>
                <form method="GET" action="{{ route('program.index') }}" class="filters" id="filterForm">
                    <select class="filter-select" name="status" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Status</option>
                        <option value="selesai"      {{ ($selectedStatus ?? '') === 'selesai'      ? 'selected' : '' }}>Selesai</option>
                        <option value="berlangsung"  {{ ($selectedStatus ?? '') === 'berlangsung'  ? 'selected' : '' }}>Berlangsung</option>
                        <option value="akan-datang"  {{ ($selectedStatus ?? '') === 'akan-datang'  ? 'selected' : '' }}>Akan Datang</option>
                    </select>
                    <select class="filter-select" name="month" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ (string)($selectedMonth ?? '') === (string)$m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                        @endforeach
                    </select>
                    <select class="filter-select" name="year" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Tahun</option>
                        @foreach($years ?? [] as $y)
                        <option value="{{ $y }}" {{ (string)($selectedYear ?? '') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    @if(($selectedStatus ?? '') || ($selectedMonth ?? '') || ($selectedYear ?? ''))
                    <a href="{{ route('program.index') }}" class="filter-reset-btn" title="Reset filter">&#x2715;</a>
                    @endif
                </form>
            </div>

            <div class="program-cards">
                @forelse($programs as $item)
                <a href="{{ route('program.detail', ['slug' => $item->slug]) }}" class="program-link">
                <article class="program-card">
                    <div class="card-image" style="--thumb: url('{{ asset($item->image ?? 'images/program/program-1.png') }}');">
                        <img src="{{ asset($item->image ?? 'images/program/program-1.png') }}" alt="{{ $item->title }}">
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">{{ $item->title }}</h2>
                        <p class="card-meta">
                            @if($item->date)
                                @php
                                    $d = $item->date;
                                    try {
                                        if (str_contains($d, '/')) {
                                            $clean = preg_replace('/\s*\/\s*/', '/', trim($d));
                                            $dt = \Carbon\Carbon::createFromFormat('d/m/Y', $clean);
                                        } else {
                                            $dt = \Carbon\Carbon::createFromFormat('Y-m-d', $d);
                                        }
                                        echo $dt->translatedFormat('d F Y');
                                    } catch (\Exception $e) { echo $d; }
                                @endphp
                            @else
                                –
                            @endif
                            &nbsp;&nbsp; {{ $item->location }}
                        </p>
                        @if($item->status === 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @elseif($item->status === 'berlangsung')
                            <span class="badge badge-warning">Berlangsung</span>
                        @else
                            <span class="badge badge-info">Akan Datang</span>
                        @endif
                        <p class="card-text">{{ Str::limit($item->intro, 80) }}</p>
                        <button class="card-button" type="button">Detail</button>
                    </div>
                </article>
                </a>
                @empty
                <div class="text-center py-5 w-100">
                    <p class="text-muted">Belum ada program kegiatan. Tambahkan melalui dashboard admin.</p>
                </div>
                @endforelse
            </div>
        </section>

    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Sidebar Partial -->
    @include('partials.sidebar')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar JS -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>
