<!-- Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Sidebar Menu -->
<nav class="sidebar" id="sidebar">
    <!-- Header: Close (left) -->
    <div class="sidebar-header">
        <button class="sidebar-close-btn" id="sidebarCloseBtn" type="button">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span class="close-text">Tutup</span>
        </button>
    </div>

    <!-- Menu Items -->
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="{{ url('/') }}" class="sidebar-link">
                <span class="item-text">Tentang Kami</span>
                <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ url('/gallery') }}" class="sidebar-link">
                <span class="item-text">Galeri</span>
                <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ url('/program') }}" class="sidebar-link">
                <span class="item-text">
                    Program<br>Kegiatan
                </span>
                <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="{{ url('/berita') }}" class="sidebar-link">
                <span class="item-text">Berita</span>
                <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </li>
    </ul>

    <!-- Login Button -->
    <div class="sidebar-footer">
        <a href="{{ route('login') }}" class="sidebar-login-btn">Login</a>
    </div>
</nav>
