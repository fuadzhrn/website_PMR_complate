/* ========================================
   Sidebar Menu - JavaScript Controls
   ======================================== */

/**
 * Sidebar Management
 * Handles open/close animations and backdrop interactions
 */

const SidebarManager = {
    sidebar: null,
    backdrop: null,
    closeBtn: null,
    loginBtn: null,
    isOpen: false,

    /**
     * Initialize sidebar elements and event listeners
     */
    init: function() {
        this.sidebar = document.getElementById('sidebar');
        this.backdrop = document.getElementById('sidebarBackdrop');
        this.closeBtn = document.getElementById('sidebarCloseBtn');
        this.loginBtn = document.querySelector('.sidebar-login-btn');

        if (!this.sidebar || !this.backdrop) {
            console.warn('⚠ Sidebar elements not found');
            return;
        }

        this.attachEventListeners();
        console.log('✓ Sidebar manager initialized');
    },

    /**
     * Attach all event listeners
     */
    attachEventListeners: function() {
        // Close button click
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        // Backdrop click
        this.backdrop.addEventListener('click', () => this.close());

        // Sidebar links (optional: close after click)
        const links = this.sidebar.querySelectorAll('.sidebar-link');
        links.forEach(link => {
            link.addEventListener('click', () => {
                // Optional: close sidebar on menu click
                // this.close();
            });
        });

        // Login button click
        if (this.loginBtn) {
            this.loginBtn.addEventListener('click', () => {
                console.log('Login button clicked');
                // Add your login logic here
                // this.close();
            });
        }

        // Keyboard: ESC to close sidebar
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Prevent body scroll when sidebar is open
        this.sidebar.addEventListener('wheel', (event) => {
            const isAtTop = this.sidebar.scrollTop === 0;
            const isAtBottom = this.sidebar.scrollTop + this.sidebar.clientHeight >= this.sidebar.scrollHeight;

            if ((isAtTop && event.deltaY < 0) || (isAtBottom && event.deltaY > 0)) {
                event.preventDefault();
            }
        });
    },

    /**
     * Open sidebar with animation
     */
    open: function() {
        if (this.isOpen) return;

        this.sidebar.classList.add('active');
        this.backdrop.classList.add('active');
        document.body.classList.add('sidebar-open');
        this.isOpen = true;

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        console.log('Sidebar opened');
    },

    /**
     * Close sidebar with animation
     */
    close: function() {
        if (!this.isOpen) return;

        this.sidebar.classList.remove('active');
        this.backdrop.classList.remove('active');
        document.body.classList.remove('sidebar-open');
        this.isOpen = false;

        // Restore body scroll
        document.body.style.overflow = '';

        console.log('Sidebar closed');
    },

    /**
     * Toggle sidebar open/close
     */
    toggle: function() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
};

/**
 * Global function to open sidebar from navbar burger button
 */
function openSidebar() {
    SidebarManager.open();
}

/**
 * Global function to close sidebar
 */
function closeSidebar() {
    SidebarManager.close();
}

/**
 * Global function to toggle sidebar
 */
function toggleSidebar() {
    SidebarManager.toggle();
}

/**
 * Global handler for topbar search button
 * For now, redirect pengguna ke halaman berita
 */
function handleSearchClick() {
    try {
        window.location.href = '/berita';
    } catch (e) {
        console.error('Gagal menjalankan pencarian:', e);
    }
}

// Initialize sidebar when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    SidebarManager.init();

    // Hide navbar on scroll, show again when scroll stops
    const navbar = document.querySelector('.hero-navbar');
    if (navbar) {
        let scrollTimer = null;

        window.addEventListener('scroll', function () {
            // Sembunyikan navbar saat scroll dimulai
            navbar.classList.add('navbar-hidden');

            // Batalkan timer sebelumnya
            clearTimeout(scrollTimer);

            // Munculkan kembali setelah 400ms berhenti scroll
            scrollTimer = setTimeout(function () {
                navbar.classList.remove('navbar-hidden');
            }, 400);
        }, { passive: true });
    }
});
