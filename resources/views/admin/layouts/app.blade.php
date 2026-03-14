<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – @yield('title', 'Dashboard') | Wira 242</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f6f9; font-family:'Poppins',sans-serif; }
        .sidebar {
            width: 240px; min-height: 100vh; background:#1a2035; color:#fff;
            position: fixed; top:0; left:0; z-index:1000; transition:.3s;
        }
        .sidebar .brand {
            padding:20px 16px; font-weight:700; font-size:1rem;
            border-bottom:1px solid rgba(255,255,255,.1);
            color:#fff; text-decoration:none; display:block;
        }
        .sidebar .nav-link {
            color:rgba(255,255,255,.7); padding:10px 16px; border-radius:6px;
            margin:2px 8px; font-size:.875rem; display:flex; align-items:center; gap:8px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background:rgba(255,255,255,.12); color:#fff;
        }
        .sidebar .nav-section { padding:12px 16px 4px; font-size:.7rem; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:1px; }
        .main-content { margin-left:240px; padding:24px; min-height:100vh; }
        .topbar { background:#fff; padding:12px 24px; margin:-24px -24px 24px; border-bottom:1px solid #e9ecef; display:flex; justify-content:space-between; align-items:center; }
        .topbar .page-title { font-weight:600; font-size:1.1rem; margin:0; }
        .card { border:none; box-shadow:0 2px 8px rgba(0,0,0,.06); border-radius:10px; }
        .stat-card { border-left:4px solid; }
        .stat-card.blue   { border-color:#0d6efd; }
        .stat-card.green  { border-color:#198754; }
        .stat-card.orange { border-color:#fd7e14; }
        .stat-card.red    { border-color:#dc3545; }
        .badge-selesai     { background:#d1fae5; color:#065f46; }
        .badge-berlangsung { background:#dbeafe; color:#1e40af; }
        .badge-akan-datang { background:#fef3c7; color:#92400e; }
        @media(max-width:768px){.sidebar{width:0;overflow:hidden;} .main-content{margin-left:0;}}
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            🩺 Admin Wira 242
        </a>
        <nav class="mt-2 pb-4">
            <div class="nav-section">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section">Konten</div>
            <a href="{{ route('admin.berita.index') }}" class="nav-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> Berita
            </a>
            <a href="{{ route('admin.program.index') }}" class="nav-link {{ request()->routeIs('admin.program.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Program Kegiatan
            </a>
            <a href="{{ route('admin.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                <i class="bi bi-images"></i> Galeri
            </a>
            <a href="{{ route('admin.comment.index') }}" class="nav-link {{ request()->routeIs('admin.comment.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> Komentar
            </a>

            <div class="nav-section">Halaman</div>
            <a href="{{ route('admin.home-content.index') }}" class="nav-link {{ request()->routeIs('admin.home-content.*') ? 'active' : '' }}">
                <i class="bi bi-house"></i> Konten Beranda
            </a>
            <a href="{{ route('admin.org.index') }}" class="nav-link {{ request()->routeIs('admin.org.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Struktur Organisasi
            </a>

            <div class="nav-section">Akun</div>
            <a href="{{ url('/') }}" target="_blank" class="nav-link">
                <i class="bi bi-box-arrow-up-right"></i> Lihat Website
            </a>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>