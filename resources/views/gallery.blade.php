<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Gallery</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Montserrat & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <!-- Gallery Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">

    <!-- Shared Topbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">
</head>
<body class="with-fixed-navbar">
    @include('partials.topbar')

    <!-- Main Content -->
    <main class="gallery-main">
        <section class="gallery-intro">
            <div class="gallery-intro-left">
                <h1 class="gallery-title">Gallery</h1>
                <hr class="gallery-divider">
                <p class="gallery-description">
                    Galeri ini berisi dokumentasi kegiatan PMR Man 3 Makassar, mulai dari latihan rutin, aksi sosial,
                    edukasi kesehatan, hingga dukungan acara sekolah. Setiap foto menjadi bukti semangat kebersamaan,
                    kepedulian, dan kesiapsiagaan anggota PMR.
                </p>
            </div>
            <div class="gallery-intro-right">
                <button type="button" class="upload-link" id="toggleUploadForm">
                    <span>Upload Kegiatan</span>
                    <span class="upload-arrow">&#8594;</span>
                </button>
            </div>
        </section>

        <!-- Upload Form Popup -->
        <section class="gallery-upload-overlay" id="galleryUploadSection" aria-hidden="true" role="dialog">
            <div class="gallery-upload-popup">
                <button type="button" class="gallery-upload-close" id="galleryUploadClose" aria-label="Tutup form">×</button>
                <h2 class="gallery-upload-title">Upload Kegiatan</h2>
                <p class="gallery-upload-subtitle">
                    Bagikan momen terbaik kegiatan PMR. Tambahkan nama kegiatan dan beberapa foto favoritmu agar tersimpan rapi di galeri ini.
                </p>
                <form class="gallery-upload-form" action="{{ route('gallery.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="upload-form-column">
                        <div class="upload-form-field">
                            <label for="title">Nama Kegiatan (opsional)</label>
                            <input type="text" id="title" name="title" maxlength="150">
                        </div>
                        <div class="upload-form-field">
                            <label for="activity_date">Tanggal Kegiatan</label>
                            <input type="date" id="activity_date" name="activity_date"
                                value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="upload-form-field">
                            <label for="photo">Foto Kegiatan</label>
                            <input type="file" id="photo" name="photos[]" accept="image/*" multiple required>
                            <small>Anda dapat memilih lebih dari satu foto sekaligus.</small>
                        </div>
                        <div class="upload-form-actions">
                            <button type="button" class="upload-cancel-btn" id="galleryUploadCancel">Batal</button>
                            <button type="submit" class="upload-submit-btn">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Filters: Tahun & Bulan -->
        @php
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
        @endphp
        <section class="gallery-filters">

            {{-- Row 1: Year dropdown (scales to any number of years) --}}
            <div class="gallery-filter-row">
                <span class="gallery-filter-label">Tahun</span>
                <form method="GET" action="{{ route('gallery.index') }}" id="yearForm">
                    <select name="year" class="gallery-year-select" id="galleryYearSelect">
                        @forelse($years as $y)
                        <option value="{{ $y }}" {{ (int)$y === (int)$selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                        @empty
                        <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                        @endforelse
                    </select>
                </form>
            </div>

            {{-- Row 2: Month scrollable pill bar (max 12 pills, swipeable) --}}
            <div class="gallery-filter-row" id="galleryMonthFilterRow" {{ count($monthsForYear) < 1 ? 'style="display:none"' : '' }}>
                <span class="gallery-filter-label">Bulan</span>
                <div class="gallery-month-scroll-wrap">
                    <div class="gallery-month-scroll" id="monthScroll">
                        <div class="gallery-month-pills" id="galleryMonthPills">
                            <button type="button" class="gallery-month-pill {{ $selectedMonth === 'all' ? 'active' : '' }}" data-month="all">
                                Semua
                            </button>
                            @foreach($monthsForYear as $m)
                            <button type="button" class="gallery-month-pill {{ (string)$selectedMonth === (string)$m ? 'active' : '' }}" data-month="{{ $m }}">
                                {{ $monthNames[$m] ?? $m }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <div id="galleryPhotoContainer">
        {{-- Gallery grouped by month --}}
        @if($photosByMonth->isEmpty())
            <div class="gallery-empty-state">
                <p>Belum ada foto untuk periode ini.</p>
            </div>
        @else
            @foreach($photosByMonth as $month => $photos)
            <section class="gallery-month-section">
                <div class="gallery-month-header">
                    <span class="gallery-month-label">{{ $monthNames[(int)$month] ?? $month }}</span>
                    <span class="gallery-month-year">{{ $selectedYear }}</span>
                    <span class="gallery-month-count">{{ $photos->count() }} foto</span>
                    <div class="gallery-month-line"></div>
                </div>
                <div class="photo-grid">
                    @foreach($photos as $photo)
                    <button type="button" class="photo-item"
                        data-src="{{ asset($photo->path) }}"
                        data-title="{{ $photo->title }}"
                        data-date="{{ \Carbon\Carbon::parse($photo->uploaded_at)->translatedFormat('d F Y') }}">
                        <img src="{{ asset($photo->path) }}" alt="{{ $photo->title ?? 'Dokumentasi PMR' }}" loading="lazy">
                        <div class="photo-detail-badge">
                            @if($photo->title)<span>{{ Str::limit($photo->title, 22) }}</span> · @endif
                            <span>{{ \Carbon\Carbon::parse($photo->uploaded_at)->format('d/m/Y') }}</span>
                        </div>
                    </button>
                    @endforeach
                </div>
            </section>
            @endforeach
        @endif
        </div>{{-- #galleryPhotoContainer --}}
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Sidebar Partial -->
    @include('partials.sidebar')

    <!-- Fullscreen zoom container -->
    <div class="gallery-modal" id="galleryModal" aria-hidden="true" role="dialog">
        <button type="button" class="gallery-modal-nav gallery-modal-prev" id="galleryModalPrev" aria-label="Foto sebelumnya">&#10094;</button>
        <div class="gallery-modal-inner">
            <button type="button" class="gallery-modal-close" id="galleryModalClose" aria-label="Tutup foto">×</button>
            <img src="" alt="Foto kegiatan PMR" id="galleryModalImage" class="gallery-modal-photo">
        </div>
        <button type="button" class="gallery-modal-nav gallery-modal-next" id="galleryModalNext" aria-label="Foto berikutnya">&#10095;</button>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar JS -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/gallery.js') }}"></script>
</body>
</html>
