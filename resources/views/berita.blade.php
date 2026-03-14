<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Berita</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Montserrat & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <!-- Berita Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/berita.css') }}">

    <!-- Shared Topbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">
</head>
<body class="{{ empty($q ?? '') ? '' : 'with-fixed-navbar' }}">
    @include('partials.topbar')

    @php $q = $q ?? ''; @endphp

    @if(empty($q))
    <!-- Hero Berita Utama -->
    @php
                $heroItems = $todayBerita->isNotEmpty() ? $todayBerita->take(4) : collect();
                $heroFirst = $featured ?? $heroItems->first();
            @endphp
    <section class="hero-news"
        data-hero-slides="{{ $heroItems->map(fn($b) => [
            'slug'        => $b->slug,
            'image'       => asset($b->image ?? 'images/news/latihansingkatpembalutanringan.png'),
            'title'       => $b->title,
            'description' => is_array($b->paragraphs) && count($b->paragraphs) ? $b->paragraphs[0] : '',
        ])->values()->toJson(JSON_UNESCAPED_UNICODE) }}">
        <div class="hero-image">
            <img src="{{ asset($heroFirst?->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $heroFirst?->title ?? 'Berita Utama' }}">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-content">
            <div class="hero-bullets">
                @foreach($heroItems as $i => $hItem)
                    <span class="bullet {{ $i === 0 ? 'active' : '' }}"></span>
                @endforeach
            </div>
            <div class="hero-text">
                <h1 class="hero-title">{{ $heroFirst?->title ?? 'Berita PMR' }}</h1>
                <p class="hero-description">
                    {{ is_array($heroFirst?->paragraphs) && count($heroFirst->paragraphs) ? $heroFirst->paragraphs[0] : 'Simak berita terbaru dari kegiatan PMR Keluarga Wira 242.' }}
                </p>
            </div>
        </div>

        <div class="hero-actions">
            <button type="button" class="hero-btn hero-btn-outline">Detail</button>
            <button type="button" class="hero-btn hero-btn-filled">Upload</button>
        </div>
    </section>

    <!-- Berita Harian Section -->
    <section class="daily-news">
        <div class="daily-news-inner">
            <div class="daily-header">
                <button class="daily-tag" type="button">Berita Harian</button>
                <span class="daily-arrow">&gt;</span>
                <span class="daily-title">Saksikan Berita Terbaru</span>
            </div>

@php
                $leftCards  = $todayBerita->take(ceil($todayBerita->count() / 2));
                $rightCards = $todayBerita->slice(ceil($todayBerita->count() / 2));
            @endphp
            <div class="daily-grid">
                <!-- Left column: berita hari ini (separuh kiri) -->
                <div class="daily-left">
                    @forelse($leftCards as $item)
                    <a href="{{ route('berita.detail', ['slug' => $item->slug]) }}" class="news-link">
                        <article class="daily-card daily-card-horizontal">
                            <div class="card-thumb">
                                <img src="{{ asset($item->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $item->title }}">
                            </div>
                            <div class="card-content">
                                <h2 class="card-title"><span class="card-title-underline">{{ Str::words($item->title, 2) }}</span></h2>
                                <p class="card-text">{{ is_array($item->paragraphs) && count($item->paragraphs) ? Str::limit($item->paragraphs[0], 120) : Str::limit($item->title, 120) }}</p>
                                <p class="card-meta">Hari ini</p>
                            </div>
                        </article>
                    </a>
                    @empty
                    <p class="text-muted p-3">Belum ada berita hari ini.</p>
                    @endforelse
                </div>

                <!-- Right column: berita hari ini (separuh kanan) -->
                <div class="daily-right">
                    @foreach($rightCards as $item)
                    <a href="{{ route('berita.detail', ['slug' => $item->slug]) }}" class="news-link">
                        <article class="daily-card daily-card-horizontal">
                            <div class="card-thumb">
                                <img src="{{ asset($item->image ?? 'images/news/pengecekanperlengkapanpmr.png') }}" alt="{{ $item->title }}">
                            </div>
                            <div class="card-content">
                                <h2 class="card-title"><span class="card-title-underline">{{ Str::words($item->title, 2) }}</span></h2>
                                <p class="card-text">{{ is_array($item->paragraphs) && count($item->paragraphs) ? Str::limit($item->paragraphs[0], 120) : Str::limit($item->title, 120) }}</p>
                                <p class="card-meta">Hari ini</p>
                            </div>
                        </article>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Panel berita lainnya (hari-hari sebelumnya) --}}
            @if($olderBerita->isNotEmpty())
            <div class="daily-more-wrapper" id="moreNewsWrapper">
                <button type="button" class="daily-more-btn" id="moreNewsBtn">
                    Berita lainnya <span class="arrow">&#9660;</span>
                </button>
            </div>
            <div class="daily-more-panel" id="moreNewsPanel" style="display:none">
                <h3 class="daily-more-title">Berita lainnya</h3>
                <div class="daily-more-scroll">
                    @foreach($olderBerita as $item)
                    <div class="daily-more-item">
                        <a href="{{ route('berita.detail', ['slug' => $item->slug]) }}" class="daily-more-link">
                            <div class="daily-more-thumb">
                                <img src="{{ asset($item->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $item->title }}">
                            </div>
                            <div class="daily-more-body">
                                <span class="daily-more-item-title">{{ $item->title }}</span>
                                @if(is_array($item->paragraphs) && count($item->paragraphs))
                                <span class="daily-more-item-desc">{{ Str::limit($item->paragraphs[0], 100) }}</span>
                                @endif
                            </div>
                        </a>
                        @php
                            // Normalisasi date ke Carbon
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
                                $diffDays = (int) $carbonD->diffInDays($today); // selalu positif, karena carbonD adalah masa lalu
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
                        <span class="daily-more-item-meta">{{ $dateLabel }}</span>
                    </div>
                    @endforeach
                </div>
                @if($olderBeritaSisa > 0)
                <div class="daily-more-footer">
                    <a href="{{ route('berita.arsip') }}" class="daily-more-all-btn">
                        Lihat {{ number_format($olderBeritaSisa, 0, ',', '.') }} berita lainnya &rarr;
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </section>
    @else
    {{-- ======= MODE PENCARIAN ======= --}}
    <main class="news-main search-mode">
        <section class="search-results-section">
            <div class="search-results-header">
                <a href="{{ route('berita.index') }}" class="search-back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                    Kembali ke Berita
                </a>
                <h2 class="search-results-title">Hasil pencarian &ldquo;<em>{{ $q }}</em>&rdquo;</h2>
                <p class="search-results-count">{{ $searchResults->count() }} berita ditemukan</p>
            </div>

            @if($searchResults->isEmpty())
                <div class="search-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="1.5" opacity=".4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M8 11h6M11 8v6" stroke-width="2"/></svg>
                    <p>Tidak ada berita yang cocok dengan &ldquo;<strong>{{ $q }}</strong>&rdquo;.</p>
                    <p class="search-empty-hint">Coba gunakan kata kunci yang lebih umum.</p>
                </div>
            @else
                <div class="search-results-grid">
                    @foreach($searchResults as $item)
                    <a href="{{ route('berita.detail', ['slug' => $item->slug]) }}" class="search-result-link">
                        <article class="search-result-card">
                            <div class="search-result-thumb">
                                <img src="{{ asset($item->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $item->title }}">
                            </div>
                            <div class="search-result-body">
                                <h3 class="search-result-title">{{ $item->title }}</h3>
                                <p class="search-result-text">{{ is_array($item->paragraphs) && count($item->paragraphs) ? Str::limit($item->paragraphs[0], 120) : '' }}</p>
                                <span class="search-result-date">{{ $item->date ?? '' }}</span>
                            </div>
                        </article>
                    </a>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
    @endif

    <!-- Open Recruitment Section -->
    <section class="recruitment-section">
        <div class="recruitment-bg">
            <img src="{{ asset('images/news/openrecruitment.png') }}" alt="Open Recruitment PMR">
            <div class="recruitment-overlay"></div>
        </div>
        <div class="recruitment-content">
            <h2 class="recruitment-title">Open Recruitment PMR<br>MAN 3 Makassar</h2>
            <p class="recruitment-text">
                Kami mengundang seluruh siswa/i yang ingin belajar pertolongan pertama, kerja tim, serta kegiatan kemanusiaan
                untuk bergabung di PMR. Pendaftaran dibuka mulai [Tgl Mulai] sampai [Tgl Selesai]. Pilih cara daftar yang paling mudah.
            </p>
            <p class="recruitment-text small">
                Online: isi formulir pendaftaran melalui tombol Daftar Online.<br>
                Offline: datang ke [Lokasi] jam [Jam] dengan membawa persyaratan yang ditentukan.
            </p>
            <div class="recruitment-actions">
                <button type="button" class="recruit-btn recruit-online">Online</button>
                <button type="button" class="recruit-btn recruit-offline">Offline</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Sidebar Partial -->
    @include('partials.sidebar')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar JS -->
    <script src="{{ asset('js/sidebar.js') }}"></script>

    <!-- Hero Berita Slider JS (auto-slide + bullets clickable) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const heroSection = document.querySelector('.hero-news');
            if (!heroSection) return;

            // Data slides dari server (dinamis via data-attribute)
            let slides = [];
            try {
                slides = JSON.parse(heroSection.dataset.heroSlides || '[]');
            } catch (e) {}

            if (slides.length === 0) return;

            const imageEl  = heroSection.querySelector('.hero-image img');
            const titleEl  = heroSection.querySelector('.hero-title');
            const descEl   = heroSection.querySelector('.hero-description');
            const bullets  = heroSection.querySelectorAll('.hero-bullets .bullet');
            const detailBtn = heroSection.querySelector('.hero-btn-outline');
            const dailySection = document.querySelector('.daily-news');

            if (!imageEl || !titleEl || !descEl || bullets.length === 0) return;

            let current = 0;
            let timer = null;

            function setActiveBullet(index) {
                bullets.forEach((b, i) => b.classList.toggle('active', i === index));
            }

            function showSlide(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                current = index;
                const slide = slides[current];

                heroSection.classList.add('hero-fade');
                setTimeout(() => {
                    imageEl.src           = slide.image;
                    titleEl.textContent   = slide.title;
                    descEl.textContent    = slide.description;
                    setActiveBullet(current);
                    heroSection.classList.remove('hero-fade');
                }, 150);
            }

            function nextSlide()    { showSlide(current + 1); }
            function startAutoPlay(){ timer = setInterval(nextSlide, 5000); }
            function stopAutoPlay() { if (timer) { clearInterval(timer); timer = null; } }

            if (detailBtn && dailySection) {
                detailBtn.addEventListener('click', () =>
                    dailySection.scrollIntoView({ behavior: 'smooth', block: 'start' })
                );
            }

            bullets.forEach((bullet, index) => {
                bullet.addEventListener('click', () => { showSlide(index); stopAutoPlay(); startAutoPlay(); });
            });

            showSlide(0);
            startAutoPlay();
        });

        // Toggle panel berita harian lainnya
        (function () {
            const btn   = document.getElementById('moreNewsBtn');
            const panel = document.getElementById('moreNewsPanel');
            if (!btn || !panel) return;
            let expanded = false;
            btn.addEventListener('click', function () {
                expanded = !expanded;
                panel.style.display = expanded ? 'block' : 'none';
                btn.classList.toggle('expanded', expanded);
                const arrow = btn.querySelector('.arrow');
                if (arrow) arrow.innerHTML = expanded ? '&#9650;' : '&#9660;';
            });
        })();
    </script>
</body>
</html>
