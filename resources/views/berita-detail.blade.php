<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Detail Berita</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Montserrat & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <!-- Detail Berita CSS -->
    <link rel="stylesheet" href="{{ asset('css/berita-detail.css') }}">

    <!-- Shared Topbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">
</head>
<body>

    <!-- Reading progress bar -->
    <div id="readingProgress"></div>

    @include('partials.topbar')

    <!-- Main Content -->
    <main class="detail-main">

        <!-- Hero detail: kartu gambar di kiri, teks di kanan -->
        <section class="detail-hero">
            <div class="detail-card-image">
                <div class="detail-card-inner">
                    <div class="detail-image-wrapper">
                        <img src="{{ asset($article->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $article->title }}">
                    </div>
                    <div class="detail-card-meta">
                        <span>{{ $article->date }}</span>
                        <span>{{ $article->location }}</span>
                    </div>
                </div>
            </div>
            <article class="detail-article">
                <a href="{{ route('berita.index') }}" class="back-link">&larr; Kembali ke Berita</a>
                <h1 class="detail-title">{{ $article->title }}</h1>
                @php
                    $allText = implode(' ', $article->paragraphs ?? []);
                    $wordCount = str_word_count(strip_tags($allText));
                    $readMinutes = max(1, (int) ceil($wordCount / 200));
                @endphp
                <div class="detail-read-time">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    {{ $readMinutes }} menit membaca
                </div>
                @foreach ($article->paragraphs ?? [] as $text)
                    <p class="detail-paragraph">{{ $text }}</p>
                @endforeach

                <div class="detail-footer-row">
                    <div class="detail-author">
                        <span class="detail-author-line"></span>
                        <span class="detail-author-name">{{ $article->author }}</span>
                    </div>
                    <div class="detail-stats">
                        <div class="detail-stat">
                            <span class="detail-stat-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-6a2 2 0 1 0 .001 3.999A2 2 0 0 0 12 10Z"/>
                                </svg>
                            </span>
                            <span id="news-view-count" class="detail-stat-number">{{ number_format($article->views) }}</span>
                        </div>
                        <button type="button" class="detail-stat detail-like-btn" data-slug="{{ $article->slug }}">
                            <span class="detail-stat-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="M5 21h2V9H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Zm4 0h7.5a2.5 2.5 0 0 0 2.45-2.02l1.2-6A2.5 2.5 0 0 0 17.7 10H14l.7-3.38A1.75 1.75 0 0 0 13 4h-1l-3 5.2V21Z"/>
                                </svg>
                            </span>
                            <span id="news-like-count" class="detail-stat-number">{{ number_format($article->likes) }}</span>
                        </button>
                    </div>
                </div>
            </article>
        </section>

        <!-- Berita Lainnya Slider -->
        @if($otherBerita->isNotEmpty())
        <section class="detail-daily">
            <div class="daily-header">
                <button class="daily-tag" type="button">Berita Lainnya</button>
                <span class="daily-arrow">&gt;</span>
                <span class="daily-title">Saksikan Berita Lainnya</span>
            </div>
            <div class="detail-daily-wrapper">
                <button type="button" class="daily-nav daily-nav-prev" aria-label="Berita sebelumnya">&#10094;</button>
                <div class="detail-daily-viewport">
                    <div class="detail-daily-track">
                        @foreach ($otherBerita as $other)
                        <a href="{{ route('berita.detail', ['slug' => $other->slug]) }}" class="daily-card-link">
                            <article class="daily-card">
                                <div class="daily-thumb">
                                    <img src="{{ asset($other->image ?? 'images/news/latihansingkatpembalutanringan.png') }}" alt="{{ $other->title }}">
                                </div>
                                <div class="daily-body">
                                    <h3 class="daily-card-title">{{ $other->title }}</h3>
                                    <p class="daily-text">{{ is_array($other->paragraphs) && count($other->paragraphs) ? Str::limit($other->paragraphs[0], 100) : '' }}</p>
                                </div>
                            </article>
                        </a>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="daily-nav daily-nav-next" aria-label="Berita berikutnya">&#10095;</button>
            </div>
        </section>
        @endif

        <!-- Komentar -->
        <section class="detail-comments">
            <h2 class="detail-comments-title">Komentar</h2>
            <form class="comment-form" action="{{ route('berita.comment', ['slug' => $article->slug]) }}" method="POST">
                @csrf
                <div class="comment-form-row">
                    <input type="text" name="name" placeholder="Nama" value="{{ old('name') }}" required>
                </div>
                <div class="comment-form-row">
                    <textarea name="message" rows="3" placeholder="Tulis komentar anda" required>{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="comment-submit-btn">Kirim Komentar</button>
            </form>

            @if ($comments->isNotEmpty())
                <div class="comment-list">
                    @foreach ($comments as $comment)
                        <div class="comment-item">
                            <div class="comment-name">{{ $comment->name }}</div>
                            <div class="comment-text">{{ $comment->message }}</div>
                            <div class="comment-meta">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="comment-empty">Belum ada komentar. Jadilah yang pertama!</p>
            @endif
        </section>
    </main>

    <!-- Footer -->
    @include('partials.footer')
    @include('partials.sidebar')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Reading progress bar
            const progressBar  = document.getElementById('readingProgress');
            const articleEl    = document.querySelector('.detail-article');
            if (progressBar && articleEl) {
                window.addEventListener('scroll', function () {
                    const rect   = articleEl.getBoundingClientRect();
                    const total  = articleEl.offsetHeight - window.innerHeight;
                    const scrolled = Math.max(0, -rect.top);
                    const pct    = total > 0 ? Math.min(100, (scrolled / total) * 100) : 100;
                    progressBar.style.width = pct + '%';
                }, { passive: true });
            }

            // Slider berita lainnya
            const viewport = document.querySelector('.detail-daily-viewport');
            const track    = document.querySelector('.detail-daily-track');
            const prevBtn  = document.querySelector('.daily-nav-prev');
            const nextBtn  = document.querySelector('.daily-nav-next');
            if (viewport && track && prevBtn && nextBtn) {
                const cardWidth = () => {
                    const card = track.querySelector('.daily-card-link');
                    return card ? card.offsetWidth + 16 : 280;
                };
                const visibleCount = () => Math.max(1, Math.floor(viewport.offsetWidth / cardWidth()));
                prevBtn.addEventListener('click', () => {
                    viewport.scrollBy({ left: -(cardWidth() * visibleCount()), behavior: 'smooth' });
                });
                nextBtn.addEventListener('click', () => {
                    viewport.scrollBy({ left: cardWidth() * visibleCount(), behavior: 'smooth' });
                });
            }

            const likeBtn = document.querySelector('.detail-like-btn');
            if (likeBtn) {
                const slug = likeBtn.getAttribute('data-slug');
                const likeStorageKey = 'liked_berita_' + slug;
                const serverLiked = {{ ($hasLiked ?? false) ? 'true' : 'false' }};

                // Sinkronkan state liked: jika server sudah catat, tulis ke localStorage
                if (serverLiked) localStorage.setItem(likeStorageKey, '1');

                // Terapkan tampilan awal jika sudah pernah like
                if (serverLiked || localStorage.getItem(likeStorageKey) === '1') {
                    likeBtn.classList.add('detail-like-btn--active');
                    likeBtn.disabled = true;
                }

                likeBtn.addEventListener('click', function () {
                    if (likeBtn.disabled) return;
                    fetch(`/berita/${slug}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                        .then(r => r.ok ? r.json() : null)
                        .then(data => {
                            if (!data) return;
                            const el = document.getElementById('news-like-count');
                            if (el) el.textContent = new Intl.NumberFormat('id-ID').format(data.likes);
                            localStorage.setItem(likeStorageKey, '1');
                            likeBtn.classList.add('detail-like-btn--active');
                            likeBtn.disabled = true;
                        });
                });
            }
        });
    </script>
</body>
</html>