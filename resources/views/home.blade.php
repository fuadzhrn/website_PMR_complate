<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarga Wira 242 - Tentang Kami</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Montserrat & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Hero CSS -->
    <link rel="stylesheet" href="{{ asset('css/home-hero.css') }}">
    
    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Background Slider -->
        <div class="hero-slider">
            @foreach($heroSlides as $index => $slideUrl)
            <div class="slider-item {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $slideUrl }}');"></div>
            @endforeach
        </div>

        <!-- Dark Overlay + Gradient Vignette -->
        <div class="hero-overlay"></div>

        <!-- Top Navigation Bar -->
        @include('partials.topbar')

        <!-- Left Slider Indicators -->
        <div class="slider-indicators">
            <button class="indicator active" data-slide="0"></button>
            <button class="indicator" data-slide="1"></button>
            <button class="indicator" data-slide="2"></button>
            <button class="indicator" data-slide="3"></button>
        </div>

        <!-- Hero Content (Left Side) -->
        <div class="hero-content">
            <h1 class="hero-title">Keluarga Wira 242</h1>
            <p class="hero-description">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod. Lorem ipsum dolor
                sit amet consectetur adipisicing elit. Quisquam, quod.
            </p>
        </div>
    </section>

    <!-- Home Sections: Selayang Pandang, Tentang Kami, Struktur Organisasi -->
    <main class="home-main">
        <!-- Selayang Pandang -->
        <section class="home-section home-section-intro" id="selayang">
            <div class="home-section-inner">
                <div class="home-section-image">
                    @php $selayang_img = $selayang?->image ?? 'images/home/selayang-pandang.png'; @endphp
                    <img src="{{ asset($selayang_img) }}" alt="Selayang Pandang PMR" class="home-image">
                </div>
                <div class="home-section-content">
                    <h2 class="home-section-title">{{ $selayang?->title ?? 'Selayang Pandang' }}</h2>
                    <p class="home-section-text">
                        {{ $selayang?->content ?? 'Konten selayang pandang belum diisi. Silakan edit melalui dashboard admin.' }}
                    </p>
                    <button type="button"
                            class="home-section-button"
                            data-toggle="home-readmore"
                            data-target="#selayang">
                        Selengkapnya
                    </button>
                </div>
            </div>
        </section>

        <!-- Tentang Kami -->
        <section class="home-section home-section-about" id="tentang">
            <div class="home-section-inner">
                <div class="home-section-content">
                    <h2 class="home-section-title">{{ $tentang?->title ?? 'Tentang Kami' }}</h2>
                    <p class="home-section-text">
                        {{ $tentang?->content ?? 'Konten tentang kami belum diisi. Silakan edit melalui dashboard admin.' }}
                    </p>
                    <button type="button"
                            class="home-section-button"
                            data-toggle="home-readmore"
                            data-target="#tentang">
                        Selengkapnya
                    </button>
                </div>
                <div class="home-section-image">
                    @php $tentang_img = $tentang?->image ?? 'images/home/tentang-kami.png'; @endphp
                    <img src="{{ asset($tentang_img) }}" alt="Tentang Kami PMR" class="home-image">
                </div>
            </div>
        </section>

        <!-- Struktur Organisasi -->
        @php
            // Buat lookup flat dari semua anggota berdasarkan position_key
            $orgAll = collect();
            foreach ($members as $group) { $orgAll = $orgAll->merge($group); }
            $orgByKey = $orgAll->keyBy('position_key');

            // Helper closure: ambil field dari DB, fallback ke nilai default
            $orgName  = fn(string $key, string $fallback = 'Nama') => $orgByKey->get($key)?->name  ?? $fallback;
            $orgTitle = fn(string $key, string $fallback = '') => $orgByKey->get($key)?->title ?? $fallback;
            $orgPhoto = fn(string $key, string $fallbackImg) => $orgByKey->get($key)?->photo
                            ? asset($orgByKey->get($key)->photo)
                            : asset($fallbackImg);

            // Ambil staf berdasarkan parent_key
            $staffOf  = fn(string $parentKey) => $orgAll->where('parent_key', $parentKey)->sortBy('sort_order')->values();
        @endphp
        <section class="home-section home-section-structure">

            <!-- Dekorasi sisi kiri -->
            <div class="org-side-deco org-side-deco-left" aria-hidden="true">
                <svg class="org-deco-cross" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <rect x="38" y="5"  width="24" height="90" rx="8" fill="#c0392b"/>
                    <rect x="5"  y="38" width="90" height="24" rx="8" fill="#c0392b"/>
                </svg>
                <span class="org-deco-vtext">PMR &nbsp; WIRA &nbsp; 242</span>
                <span class="org-deco-dot" style="top:15%;left:30%"></span>
                <span class="org-deco-dot" style="top:35%;left:65%;animation-delay:.7s"></span>
                <span class="org-deco-dot" style="top:55%;left:20%;animation-delay:1.4s"></span>
                <span class="org-deco-dot" style="top:75%;left:55%;animation-delay:2.1s"></span>
                <span class="org-deco-dot" style="top:88%;left:38%;animation-delay:.4s"></span>
            </div>

            <!-- Dekorasi sisi kanan -->
            <div class="org-side-deco org-side-deco-right" aria-hidden="true">
                <svg class="org-deco-cross" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <rect x="38" y="5"  width="24" height="90" rx="8" fill="#c0392b"/>
                    <rect x="5"  y="38" width="90" height="24" rx="8" fill="#c0392b"/>
                </svg>
                <span class="org-deco-vtext">PMR &nbsp; WIRA &nbsp; 242</span>
                <span class="org-deco-dot" style="top:20%;left:60%"></span>
                <span class="org-deco-dot" style="top:40%;left:25%;animation-delay:.9s"></span>
                <span class="org-deco-dot" style="top:60%;left:70%;animation-delay:1.8s"></span>
                <span class="org-deco-dot" style="top:78%;left:40%;animation-delay:2.5s"></span>
                <span class="org-deco-dot" style="top:10%;left:48%;animation-delay:.3s"></span>
            </div>
            <div class="home-structure-header">
                <h2 class="home-section-title">Struktur Organisasi</h2>
                <p class="home-structure-subtitle" id="orgPeriodSubtitle">Periode {{ $selectedPeriod }}</p>
                <span class="home-section-underline"></span>
            </div>

            @if($orgPeriods->count() > 1)
            <div class="org-period-filter">
                <label class="org-period-label">Periode :</label>
                <form method="GET" action="/" class="org-period-form">
                    <select id="orgPeriodSelect" name="org_period" class="org-period-select">
                        @foreach($orgPeriods as $p)
                            <option value="{{ $p }}" {{ $p === $selectedPeriod ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif

            <div class="org-structure">
                <!-- Baris 1: Ketua Umum -->
                <div class="org-row org-row-top">
                    <div class="org-box org-box-lead" data-position-key="ketua-umum">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('ketua-umum', 'images/struktur/ketua-umum.jpg') }}" alt="Ketua Umum">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('ketua-umum', 'Ketua Umum') }}</p>
                        <p class="org-name">{{ $orgName('ketua-umum', 'Nama Ketua Umum') }}</p>
                        <span class="org-star org-star-left">★</span>
                        <span class="org-star org-star-right">★</span>
                    </div>
                </div>

                <!-- Baris 2: Sekretaris & Bendahara -->
                <div class="org-row org-row-middle">
                    <div class="org-box org-box-secretary" data-position-key="sekretaris">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('sekretaris', 'images/struktur/sekretaris.jpg') }}" alt="Sekretaris">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('sekretaris', 'Sekretaris') }}</p>
                        <p class="org-name">{{ $orgName('sekretaris', 'Nama Sekretaris') }}</p>
                    </div>
                    <div class="org-center-card">
                        <div class="org-center-inner">
                            <span class="org-center-line" data-text="WIRA">WIRA</span>
                            <span class="org-center-line org-center-line-main" data-text="242">242</span>
                            <span class="org-center-line" data-text="WIRA">WIRA</span>
                        </div>
                    </div>
                    <div class="org-box" data-position-key="bendahara">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('bendahara', 'images/struktur/bendahara.jfif') }}" alt="Bendahara">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('bendahara', 'Bendahara') }}</p>
                        <p class="org-name">{{ $orgName('bendahara', 'Nama Bendahara') }}</p>
                    </div>
                </div>

                <!-- Baris 2A: Staf Sekretaris -->
                <div class="org-row org-row-staff org-row-secretary-staff" id="secretaryStaffRow">
                    @forelse($staffOf('sekretaris') as $staf)
                    <div class="org-box org-box-staff">
                        <div class="org-photo">
                            <img src="{{ $staf->photo ? asset($staf->photo) : asset('images/struktur/sekretaris.jpg') }}" alt="{{ $staf->title }}">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $staf->title }}</p>
                        <p class="org-name">{{ $staf->name }}</p>
                    </div>
                    @empty
                    <div class="org-box org-box-staff">
                        <div class="org-photo"><img src="{{ asset('images/struktur/sekretaris.jpg') }}" alt="Staf Sekretaris 1"></div>
                        <div class="org-decor"></div>
                        <p class="org-title">Staf Sekretaris 1</p>
                        <p class="org-name">Nama Staf 1</p>
                    </div>
                    <div class="org-box org-box-staff">
                        <div class="org-photo"><img src="{{ asset('images/struktur/sekretaris.jpg') }}" alt="Staf Sekretaris 2"></div>
                        <div class="org-decor"></div>
                        <p class="org-title">Staf Sekretaris 2</p>
                        <p class="org-name">Nama Staf 2</p>
                    </div>
                    @endforelse
                </div>

                <!-- Baris 3: Kaderisasi, Humas, Kesling -->
                <div class="org-row org-row-third">
                    <div class="org-box org-box-kaderisasi" data-position-key="kaderisasi">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('kaderisasi', 'images/struktur/kaderisasi.jpg') }}" alt="Kaderisasi">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('kaderisasi', 'Kaderisasi') }}</p>
                        <p class="org-name">{{ $orgName('kaderisasi', 'Nama Kaderisasi') }}</p>
                    </div>
                    <div class="org-box org-box-humas" data-position-key="humas">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('humas', 'images/struktur/humas.jpg') }}" alt="Humas">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('humas', 'Humas') }}</p>
                        <p class="org-name">{{ $orgName('humas', 'Nama Humas') }}</p>
                    </div>
                    <div class="org-box org-box-kesling" data-position-key="kesling">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('kesling', 'images/struktur/kesling.webp') }}" alt="Kesling">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('kesling', 'Kesling') }}</p>
                        <p class="org-name">{{ $orgName('kesling', 'Nama Kesling') }}</p>
                    </div>
                </div>

                <!-- Baris 3A: Staf Kaderisasi -->
                <div class="org-row org-row-staff org-row-kaderisasi-staff" id="kaderisasiStaffRow">
                    @forelse($staffOf('kaderisasi') as $staf)
                    <div class="org-box org-box-staff">
                        <div class="org-photo">
                            <img src="{{ $staf->photo ? asset($staf->photo) : asset('images/struktur/kaderisasi.jpg') }}" alt="{{ $staf->title }}">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $staf->title }}</p>
                        <p class="org-name">{{ $staf->name }}</p>
                    </div>
                    @empty
                    @for($i = 1; $i <= 3; $i++)
                    <div class="org-box org-box-staff">
                        <div class="org-photo"><img src="{{ asset('images/struktur/kaderisasi.jpg') }}" alt="Anggota Kaderisasi {{ $i }}"></div>
                        <div class="org-decor"></div>
                        <p class="org-title">Anggota Kaderisasi {{ $i }}</p>
                        <p class="org-name">Nama Anggota {{ $i }}</p>
                    </div>
                    @endfor
                    @endforelse
                </div>

                <!-- Baris 3B: Staf Humas -->
                <div class="org-row org-row-staff org-row-humas-staff" id="humasStaffRow">
                    @forelse($staffOf('humas') as $staf)
                    <div class="org-box org-box-staff">
                        <div class="org-photo">
                            <img src="{{ $staf->photo ? asset($staf->photo) : asset('images/struktur/humas.jpg') }}" alt="{{ $staf->title }}">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $staf->title }}</p>
                        <p class="org-name">{{ $staf->name }}</p>
                    </div>
                    @empty
                    @for($i = 1; $i <= 3; $i++)
                    <div class="org-box org-box-staff">
                        <div class="org-photo"><img src="{{ asset('images/struktur/humas.jpg') }}" alt="Anggota Humas {{ $i }}"></div>
                        <div class="org-decor"></div>
                        <p class="org-title">Anggota Humas {{ $i }}</p>
                        <p class="org-name">Nama Anggota {{ $i }}</p>
                    </div>
                    @endfor
                    @endforelse
                </div>

                <!-- Baris 3C: Staf Kesling -->
                <div class="org-row org-row-staff org-row-kesling-staff" id="keslingStaffRow">
                    @forelse($staffOf('kesling') as $staf)
                    <div class="org-box org-box-staff">
                        <div class="org-photo">
                            <img src="{{ $staf->photo ? asset($staf->photo) : asset('images/struktur/kesling.webp') }}" alt="{{ $staf->title }}">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $staf->title }}</p>
                        <p class="org-name">{{ $staf->name }}</p>
                    </div>
                    @empty
                    @for($i = 1; $i <= 3; $i++)
                    <div class="org-box org-box-staff">
                        <div class="org-photo"><img src="{{ asset('images/struktur/kesling.webp') }}" alt="Anggota Kesling {{ $i }}"></div>
                        <div class="org-decor"></div>
                        <p class="org-title">Anggota Kesling {{ $i }}</p>
                        <p class="org-name">Nama Anggota {{ $i }}</p>
                    </div>
                    @endfor
                    @endforelse
                </div>

                <!-- Baris 4: Danus -->
                <div class="org-row org-row-bottom">
                    <div class="org-box org-box-danus" data-position-key="danus">
                        <div class="org-photo">
                            <img src="{{ $orgPhoto('danus', 'images/struktur/danus.jfif') }}" alt="Danus">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $orgTitle('danus', 'Danus') }}</p>
                        <p class="org-name">{{ $orgName('danus', 'Nama Danus') }}</p>
                    </div>
                </div>

                <!-- Baris 4A: Staf Danus -->
                <div class="org-row org-row-staff org-row-danus-staff" id="danusStaffRow">
                    @forelse($staffOf('danus') as $staf)
                    <div class="org-box org-box-staff">
                        <div class="org-photo">
                            <img src="{{ $staf->photo ? asset($staf->photo) : asset('images/struktur/danus.jfif') }}" alt="{{ $staf->title }}">
                        </div>
                        <div class="org-decor"></div>
                        <p class="org-title">{{ $staf->title }}</p>
                        <p class="org-name">{{ $staf->name }}</p>
                    </div>
                    @empty
                    @for($i = 1; $i <= 3; $i++)
                    <div class="org-box org-box-staff">
                        <div class="org-photo"><img src="{{ asset('images/struktur/danus.jfif') }}" alt="Anggota Danus {{ $i }}"></div>
                        <div class="org-decor"></div>
                        <p class="org-title">Anggota Danus {{ $i }}</p>
                        <p class="org-name">Nama Anggota {{ $i }}</p>
                    </div>
                    @endfor
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Footer -->
        @include('partials.footer')
    </main>

    <!-- Custom Sidebar Menu -->
    @include('partials.sidebar')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar JS -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    
    <!-- Custom Hero JS -->
    <script src="{{ asset('js/home-hero.js') }}"></script>

    <!-- Org Structure AJAX Period Switcher -->
    <script>
    (function () {
        var fallbackPhotos = {
            'ketua-umum': '{{ asset("images/struktur/ketua-umum.jpg") }}',
            'sekretaris':  '{{ asset("images/struktur/sekretaris.jpg") }}',
            'bendahara':   '{{ asset("images/struktur/bendahara.jfif") }}',
            'kaderisasi':  '{{ asset("images/struktur/kaderisasi.jpg") }}',
            'humas':       '{{ asset("images/struktur/humas.jpg") }}',
            'kesling':     '{{ asset("images/struktur/kesling.webp") }}',
            'danus':       '{{ asset("images/struktur/danus.jfif") }}',
        };

        var staffConfig = {
            'sekretaris': { rowId: 'secretaryStaffRow',  defaultCount: 2, label: 'Staf Sekretaris' },
            'kaderisasi': { rowId: 'kaderisasiStaffRow', defaultCount: 3, label: 'Anggota Kaderisasi' },
            'humas':      { rowId: 'humasStaffRow',      defaultCount: 3, label: 'Anggota Humas' },
            'kesling':    { rowId: 'keslingStaffRow',    defaultCount: 3, label: 'Anggota Kesling' },
            'danus':      { rowId: 'danusStaffRow',      defaultCount: 3, label: 'Anggota Danus' },
        };

        function esc(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function staffBoxHtml(photo, title, name) {
            return '<div class="org-box org-box-staff">'
                + '<div class="org-photo"><img src="' + esc(photo) + '" alt="' + esc(title) + '"></div>'
                + '<div class="org-decor"></div>'
                + '<p class="org-title">' + esc(title) + '</p>'
                + '<p class="org-name">' + esc(name) + '</p>'
                + '</div>';
        }

        function updateOrgStructure(period) {
            fetch('/org-members?period=' + encodeURIComponent(period))
                .then(function (r) { return r.json(); })
                .then(function (members) {
                    var byKey    = {};
                    var byParent = {};

                    members.forEach(function (m) {
                        byKey[m.position_key] = m;
                        if (m.parent_key) {
                            if (!byParent[m.parent_key]) byParent[m.parent_key] = [];
                            byParent[m.parent_key].push(m);
                        }
                    });

                    // Update fixed org boxes
                    document.querySelectorAll('[data-position-key]').forEach(function (box) {
                        var key      = box.dataset.positionKey;
                        var member   = byKey[key];
                        var img      = box.querySelector('.org-photo img');
                        var titleEl  = box.querySelector('.org-title');
                        var nameEl   = box.querySelector('.org-name');
                        var fallback = fallbackPhotos[key] || '';

                        if (img)     img.src            = (member && member.photo) ? member.photo : fallback;
                        if (titleEl) titleEl.textContent = member ? member.title : key;
                        if (nameEl)  nameEl.textContent  = member ? member.name  : 'Nama';
                    });

                    // Rebuild staff rows
                    Object.keys(staffConfig).forEach(function (parentKey) {
                        var cfg      = staffConfig[parentKey];
                        var row      = document.getElementById(cfg.rowId);
                        if (!row) return;
                        var staff    = byParent[parentKey] || [];
                        var fallback = fallbackPhotos[parentKey] || '';
                        var html     = '';

                        if (staff.length === 0) {
                            for (var i = 1; i <= cfg.defaultCount; i++) {
                                html += staffBoxHtml(fallback, cfg.label + ' ' + i, 'Nama Anggota ' + i);
                            }
                        } else {
                            staff.forEach(function (staf) {
                                html += staffBoxHtml(staf.photo || fallback, staf.title, staf.name);
                            });
                        }
                        row.innerHTML = html;
                    });

                    // Update displayed period label
                    var subtitle = document.getElementById('orgPeriodSubtitle');
                    if (subtitle) subtitle.textContent = 'Periode ' + period;

                    // Reflect period in URL without reload
                    var url = new URL(window.location.href);
                    url.searchParams.set('org_period', period);
                    history.replaceState({}, '', url.toString());
                });
        }

        var sel = document.getElementById('orgPeriodSelect');
        if (sel) {
            sel.addEventListener('change', function () {
                updateOrgStructure(this.value);
            });
        }
    })();
    </script>
</body>
</html>
