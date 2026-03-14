<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Detail Program</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Montserrat & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Program Detail Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/program-detail.css') }}">

    <!-- Gunakan style logo pills & utility dari home-hero -->
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">
</head>
<body>

    <!-- Navbar sama seperti detail berita -->
    <header class="detail-header">
        <div class="detail-header-inner">
            <a href="{{ url('/') }}" class="detail-logo-group">
                <div class="logo-pill">
                    <img src="{{ asset('images/logo/logo-wira242.jpg') }}" alt="Logo Wira 242" class="logo-img">
                </div>
            </a>

            <nav class="detail-nav-links">
                <a href="{{ url('/#tentang') }}">Tentang kami</a>
                <a href="{{ url('/gallery') }}">Galery</a>
                <a href="{{ route('program.index') }}" class="active">Program Kegiatan</a>
                <a href="{{ route('berita.index') }}">Berita</a>
            </nav>

            <button class="detail-search-btn" type="button" onclick="handleSearchClick()">
                <span class="detail-search-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="16" y1="16" x2="21" y2="21"></line>
                    </svg>
                </span>
                <span>Search</span>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="program-detail-main">

    @php
        $hasDocs = !empty($uploadedDocs);
    @endphp

    <section class="program-detail-layout">
            <div class="program-detail-left">
                <section class="program-detail-hero">
                    <div class="program-detail-card">
                        <div class="program-detail-image-wrapper" style="--thumb: url('{{ asset($program->image ?? 'images/program/program-1.png') }}');">
                            <img src="{{ asset($program->image ?? 'images/program/program-1.png') }}" alt="{{ $program->title }}" class="program-detail-image">
                        </div>
                        <div class="program-detail-meta">
                            <span>
                                @if($program->date)
                                    @php
                                        $d = $program->date;
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
                            </span>
                            <span>{{ $program->location }}</span>
                        </div>
                    </div>

                    <article class="program-detail-article">
                        <a href="{{ route('program.index') }}" class="back-link">&larr; Kembali ke Program Kegiatan</a>
                        <h1 class="program-detail-title">{{ $program->title }}</h1>
                        @if($program->intro)<p>{{ $program->intro }}</p>@endif
                        @foreach ($program->paragraphs ?? [] as $text)
                            <p>{{ $text }}</p>
                        @endforeach

                        @if ($program->has_report && $program->report_file)
                            <div class="program-report-actions">
                                <a href="{{ asset($program->report_file) }}" class="program-report-download-btn" download>
                                    Unduh Laporan Kegiatan
                                </a>
                            </div>
                        @endif

                        <div class="program-detail-footer-row">
                            <div class="program-author">
                                <span class="program-author-line"></span>
                                <span class="program-author-name">{{ $program->author }}</span>
                            </div>
                            <div class="program-stats">
                                <div class="program-stat">
                                    <span class="program-stat-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor" d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-6a2 2 0 1 0 .001 3.999A2 2 0 0 0 12 10Z"/>
                                        </svg>
                                    </span>
                                    <span id="program-view-count" class="program-stat-number">{{ number_format($program->views) }}</span>
                                </div>
                                <button type="button" class="program-stat program-like-btn" data-slug="{{ $program->slug }}">
                                    <span class="program-stat-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor" d="M5 21h2V9H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Zm4 0h7.5a2.5 2.5 0 0 0 2.45-2.02l1.2-6A2.5 2.5 0 0 0 17.7 10H14l.7-3.38A1.75 1.75 0 0 0 13 4h-1l-3 5.2V21Z"/>
                                        </svg>
                                    </span>
                                    <span id="program-like-count" class="program-stat-number">{{ number_format($program->likes) }}</span>
                                </button>
                            </div>
                        </div>
                    </article>
                </section>
            </div>

            <aside class="program-detail-right">
                <!-- Dokumentasi Section -->
                <section class="program-documentation">
                    <h2 class="program-doc-title">{{ count($uploadedDocs) }} Dokumentasi</h2>

                    <div class="program-doc-card">
                        @if ($hasDocs)
                            <div class="program-doc-grid" id="docGrid">
                                @foreach ($uploadedDocs as $i => $path)
                                    <div class="program-doc-item-wrap{{ $i >= 6 ? ' doc-hidden' : '' }}">
                                        <button type="button" class="program-doc-item" data-src="{{ asset($path) }}" aria-label="Lihat foto dokumentasi">
                                            <img src="{{ asset($path) }}" alt="Dokumentasi program" loading="lazy">
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($uploadedDocs) > 6)
                            <div class="program-doc-see-more-wrap">
                                <button type="button" class="program-doc-see-more-btn" id="docSeeMoreBtn">
                                    Lihat semua {{ count($uploadedDocs) }} foto
                                </button>
                            </div>
                            @endif
                        @else
                            <div class="program-doc-empty">
                                Upload kegiatan anda di sini
                            </div>
                        @endif

                        <div class="program-doc-actions">
                            <button type="button" class="program-doc-upload-btn program-doc-open-modal-btn">
                                Upload Dokumentasi
                            </button>
                        </div>
                    </div>
                </section>
            </aside>
        </section>
    </main>

    <!-- Popup Upload Dokumentasi -->
    <div class="program-doc-modal-overlay" id="programDocModal">
        <div class="program-doc-modal">
            <button type="button" class="program-doc-modal-close" aria-label="Tutup popup">×</button>
            <h3 class="program-doc-modal-title">Upload Dokumentasi</h3>
            <p class="program-doc-modal-subtitle">Pilih satu atau beberapa foto dokumentasi kegiatan ini, lalu simpan untuk menambahkan ke galeri dokumentasi.</p>

            @if($errors->any())
            <div class="program-upload-alert program-upload-alert-error">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('program.upload', ['slug' => $program->slug]) }}" enctype="multipart/form-data" class="program-doc-modal-form">
                @csrf
                <div class="program-doc-modal-field">
                    <label for="program-doc-photos">Foto / Gambar Dokumentasi</label>
                    <input id="program-doc-photos" type="file" name="photos[]" accept="image/*" multiple required>
                    <small>Anda dapat memilih lebih dari satu foto sekaligus. Format: JPG, PNG, WEBP. Maks. 8 MB / foto.</small>
                </div>

                <!-- Preview area -->
                <div id="uploadPreviewArea" style="display:none;margin-top:10px;">
                    <p style="font-size:12px;color:#888;margin-bottom:6px;"><span id="previewCount">0</span> foto dipilih</p>
                    <div id="uploadPreviewGrid" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>

                <div class="program-doc-modal-actions">
                    <button type="button" class="program-doc-modal-btn program-doc-modal-cancel">Batal</button>
                    <button type="submit" class="program-doc-modal-btn program-doc-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('upload_success'))
    <div id="uploadSuccessToast" class="program-upload-toast">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M20 6L9 17l-5-5"/></svg>
        {{ session('upload_success') }}
    </div>
    @endif

    <!-- Lightbox foto dokumentasi -->
    <div id="docLightbox" class="doc-lightbox-overlay" role="dialog" aria-modal="true" aria-label="Lihat foto">
        <button type="button" class="doc-lightbox-close" aria-label="Tutup">&times;</button>
        <button type="button" class="doc-lightbox-prev" aria-label="Sebelumnya">&#8249;</button>
        <div class="doc-lightbox-img-wrap">
            <img id="docLightboxImg" src="" alt="Dokumentasi">
        </div>
        <button type="button" class="doc-lightbox-next" aria-label="Berikutnya">&#8250;</button>
    </div>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar JS (pakai fungsi handleSearchClick saja, sidebar tidak dipakai di halaman ini) -->
    <script src="{{ asset('js/sidebar.js') }}"></script>

    <!-- Like button AJAX handler & popup upload dokumentasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Like button
            const likeBtn = document.querySelector('.program-like-btn');
            if (likeBtn) {
                const slug = likeBtn.getAttribute('data-slug');
                const likeStorageKey = 'liked_program_' + slug;
                const serverLiked = {{ ($hasLiked ?? false) ? 'true' : 'false' }};

                // Sinkronkan state liked: jika server sudah catat, tulis ke localStorage
                if (serverLiked) localStorage.setItem(likeStorageKey, '1');

                // Terapkan tampilan awal jika sudah pernah like
                if (serverLiked || localStorage.getItem(likeStorageKey) === '1') {
                    likeBtn.classList.add('program-like-btn--active');
                    likeBtn.disabled = true;
                }

                likeBtn.addEventListener('click', function () {
                    if (likeBtn.disabled) return;
                    fetch(`/program/${slug}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                        .then(response => response.ok ? response.json() : null)
                        .then(data => {
                            if (!data) return;
                            const likeCountEl = document.getElementById('program-like-count');
                            if (likeCountEl && typeof data.likes !== 'undefined') {
                                likeCountEl.textContent = new Intl.NumberFormat('id-ID').format(data.likes);
                            }
                            localStorage.setItem(likeStorageKey, '1');
                            likeBtn.classList.add('program-like-btn--active');
                            likeBtn.disabled = true;
                        })
                        .catch(() => {});
                });
            }

            // Popup upload dokumentasi
            const modalOverlay = document.getElementById('programDocModal');
            const openBtn = document.querySelector('.program-doc-open-modal-btn');
            const closeBtn = document.querySelector('.program-doc-modal-close');
            const cancelBtn = document.querySelector('.program-doc-modal-cancel');

            function openModal() {
                if (!modalOverlay) return;
                modalOverlay.classList.add('is-open');
            }

            function closeModal() {
                if (!modalOverlay) return;
                modalOverlay.classList.remove('is-open');
            }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modalOverlay) {
                modalOverlay.addEventListener('click', function (e) {
                    if (e.target === modalOverlay) closeModal();
                });
            }

            // Auto-reopen modal jika ada error validasi
            @if($errors->any())
            openModal();
            @endif

            // Preview foto yang dipilih
            const fileInput = document.getElementById('program-doc-photos');
            const previewArea = document.getElementById('uploadPreviewArea');
            const previewGrid = document.getElementById('uploadPreviewGrid');
            const previewCount = document.getElementById('previewCount');
            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    const files = Array.from(this.files);
                    previewGrid.innerHTML = '';
                    if (files.length === 0) {
                        previewArea.style.display = 'none';
                        return;
                    }
                    previewCount.textContent = files.length;
                    previewArea.style.display = 'block';
                    files.forEach(function (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.cssText = 'width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;';
                            previewGrid.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            // Tampilkan toast sukses lalu sembunyikan otomatis
            const toast = document.getElementById('uploadSuccessToast');
            if (toast) {
                toast.classList.add('is-visible');
                setTimeout(function () {
                    toast.classList.remove('is-visible');
                }, 4000);
            }

            // Tampilkan semua foto dokumentasi
            const docSeeMoreBtn = document.getElementById('docSeeMoreBtn');
            if (docSeeMoreBtn) {
                docSeeMoreBtn.addEventListener('click', function () {
                    document.querySelectorAll('.doc-hidden').forEach(function (el) {
                        el.classList.remove('doc-hidden');
                    });
                    docSeeMoreBtn.parentElement.remove();
                    buildDocSrcs();
                });
            }

            // Lightbox foto dokumentasi
            const lightbox    = document.getElementById('docLightbox');
            const lightboxImg = document.getElementById('docLightboxImg');
            const lightboxClose = lightbox ? lightbox.querySelector('.doc-lightbox-close') : null;
            const lightboxPrev  = lightbox ? lightbox.querySelector('.doc-lightbox-prev')  : null;
            const lightboxNext  = lightbox ? lightbox.querySelector('.doc-lightbox-next')  : null;

            let docSrcs = [];
            let docIndex = 0;

            function buildDocSrcs() {
                docSrcs = Array.from(document.querySelectorAll('.program-doc-item[data-src]'))
                               .map(btn => btn.dataset.src);
            }

            function openLightbox(idx) {
                if (!lightbox || docSrcs.length === 0) return;
                docIndex = (idx + docSrcs.length) % docSrcs.length;
                lightboxImg.src = docSrcs[docIndex];
                lightbox.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                if (lightboxPrev) lightboxPrev.style.display = docSrcs.length > 1 ? '' : 'none';
                if (lightboxNext) lightboxNext.style.display = docSrcs.length > 1 ? '' : 'none';
            }

            function closeLightbox() {
                if (!lightbox) return;
                lightbox.classList.remove('is-open');
                document.body.style.overflow = '';
                setTimeout(function () { lightboxImg.src = ''; }, 200);
            }

            buildDocSrcs();

            document.querySelectorAll('.program-doc-item[data-src]').forEach(function (btn, i) {
                btn.addEventListener('click', function () { openLightbox(i); });
            });

            if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
            if (lightboxPrev)  lightboxPrev .addEventListener('click', function () { openLightbox(docIndex - 1); });
            if (lightboxNext)  lightboxNext .addEventListener('click', function () { openLightbox(docIndex + 1); });

            if (lightbox) {
                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox || e.target === lightbox.querySelector('.doc-lightbox-img-wrap')) {
                        closeLightbox();
                    }
                });
            }

            // Keyboard navigation
            document.addEventListener('keydown', function (e) {
                if (!lightbox || !lightbox.classList.contains('is-open')) return;
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft')  openLightbox(docIndex - 1);
                if (e.key === 'ArrowRight') openLightbox(docIndex + 1);
            });
        });
    </script>
</body>
</html>
