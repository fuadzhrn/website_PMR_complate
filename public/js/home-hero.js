/* ========================================
   Hero Slider - Vanilla JavaScript
   ======================================== */

document.addEventListener('DOMContentLoaded', function() {
    // ====== SLIDER CONFIGURATION ======
    const sliderConfig = {
        autoPlayInterval: 5000,  // 5 seconds
        currentSlide: 0,
        autoPlayTimer: null,
        isHovering: false
    };

    // ====== DOM ELEMENTS ======
    const sliderItems = document.querySelectorAll('.slider-item');
    const indicators = document.querySelectorAll('.indicator');
    const heroSection = document.querySelector('.hero-section');
    const burgerBtn = document.getElementById('burgerBtn');
    const navOffcanvas = document.getElementById('navOffcanvas');
    const totalSlides = sliderItems.length;

    // ====== SLIDER FUNCTIONS ======

    /**
     * Show specific slide by index
     * @param {number} index - Slide index (0-based)
     */
    function showSlide(index) {
        // Validate index
        if (index < 0) {
            sliderConfig.currentSlide = totalSlides - 1;
        } else if (index >= totalSlides) {
            sliderConfig.currentSlide = 0;
        } else {
            sliderConfig.currentSlide = index;
        }

        // Remove active class from all slides and indicators
        sliderItems.forEach(item => item.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));

        // Add active class to current slide and indicator
        sliderItems[sliderConfig.currentSlide].classList.add('active');
        indicators[sliderConfig.currentSlide].classList.add('active');
    }

    /**
     * Move to next slide
     */
    function nextSlide() {
        showSlide(sliderConfig.currentSlide + 1);
    }

    /**
     * Start auto-play slider
     */
    function startAutoPlay() {
        if (sliderConfig.autoPlayTimer) {
            clearInterval(sliderConfig.autoPlayTimer);
        }
        sliderConfig.autoPlayTimer = setInterval(nextSlide, sliderConfig.autoPlayInterval);
    }

    /**
     * Stop auto-play slider
     */
    function stopAutoPlay() {
        if (sliderConfig.autoPlayTimer) {
            clearInterval(sliderConfig.autoPlayTimer);
            sliderConfig.autoPlayTimer = null;
        }
    }

    /**
     * Reset auto-play (pause and restart)
     */
    function resetAutoPlay() {
        stopAutoPlay();
        if (!sliderConfig.isHovering) {
            startAutoPlay();
        }
    }

    // ====== EVENT LISTENERS ======

    // Indicator click listener
    indicators.forEach(indicator => {
        indicator.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-slide'));
            showSlide(slideIndex);
            resetAutoPlay();
        });
    });

    // Pause on hover (optional)
    heroSection.addEventListener('mouseenter', function() {
        sliderConfig.isHovering = true;
        stopAutoPlay();
    });

    heroSection.addEventListener('mouseleave', function() {
        sliderConfig.isHovering = false;
        startAutoPlay();
    });

    // Burger menu click listener
    burgerBtn.addEventListener('click', function() {
        const offcanvas = new bootstrap.Offcanvas(navOffcanvas);
        offcanvas.show();
    });

    // ====== KEYBOARD NAVIGATION (BONUS) ======

    document.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') {
            showSlide(sliderConfig.currentSlide - 1);
            resetAutoPlay();
        } else if (event.key === 'ArrowRight') {
            nextSlide();
            resetAutoPlay();
        }
    });

    // ====== INITIALIZATION ======

    // Show first slide
    showSlide(0);

    // Start auto-play
    startAutoPlay();

    console.log('✓ Hero slider initialized successfully');
});

// Interaksi kartu Struktur Organisasi -> tampilkan/ sembunyikan staf per bagian
document.addEventListener('DOMContentLoaded', function() {
    const orgStructure = document.querySelector('.org-structure');
    if (!orgStructure) return;

    const configs = [
        {
            cardSelector: '.org-box-secretary',
            rowId: 'secretaryStaffRow',
            activeClass: 'active-secretary'
        },
        {
            cardSelector: '.org-box-kaderisasi',
            rowId: 'kaderisasiStaffRow',
            activeClass: 'active-kaderisasi'
        },
        {
            cardSelector: '.org-box-humas',
            rowId: 'humasStaffRow',
            activeClass: 'active-humas'
        },
        {
            cardSelector: '.org-box-kesling',
            rowId: 'keslingStaffRow',
            activeClass: 'active-kesling'
        },
        {
            cardSelector: '.org-box-danus',
            rowId: 'danusStaffRow',
            activeClass: 'active-danus'
        }
    ];

    const state = {
        openRole: null
    };

    function getRow(rowId) {
        return rowId ? document.getElementById(rowId) : null;
    }

    function closeAll() {
        configs.forEach(cfg => {
            const row = getRow(cfg.rowId);
            if (row) {
                row.classList.remove('active');
            }
            orgStructure.classList.remove(cfg.activeClass);
        });
        orgStructure.classList.remove('dim-others');
        state.openRole = null;
    }

    function openRole(cfg) {
        closeAll();
        const row = getRow(cfg.rowId);
        if (row) {
            row.classList.add('active');
        }
        orgStructure.classList.add('dim-others');
        orgStructure.classList.add(cfg.activeClass);
        state.openRole = cfg;
    }

    configs.forEach(cfg => {
        const card = document.querySelector(cfg.cardSelector);
        const row = getRow(cfg.rowId);
        if (!card || !row) return;

        card.addEventListener('click', function(event) {
            event.stopPropagation();
            if (state.openRole === cfg) {
                closeAll();
            } else {
                openRole(cfg);
            }
        });
    });

    // Klik di mana saja di dokumen (background/area lain) untuk menutup semua staf
    document.addEventListener('click', function(event) {
        if (!state.openRole) return;

        const clickedInsideAny = configs.some(cfg => {
            const card = document.querySelector(cfg.cardSelector);
            const row = getRow(cfg.rowId);
            const inCard = card && card.contains(event.target);
            const inRow = row && row.contains(event.target);
            return inCard || inRow;
        });

        if (!clickedInsideAny) {
            closeAll();
        }
    });
});

// Tombol "Selengkapnya" pada card Selayang Pandang & Tentang Kami (tanpa pindah halaman)
document.addEventListener('DOMContentLoaded', function() {
    const readMoreButtons = document.querySelectorAll('.home-section-button[data-toggle="home-readmore"]');

    if (!readMoreButtons.length) return;

    readMoreButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const targetSelector = this.getAttribute('data-target');
            if (!targetSelector) return;

            const section = document.querySelector(targetSelector);
            if (!section) return;

            const text = section.querySelector('.home-section-text');
            if (!text) return;

            const isExpanded = text.classList.toggle('expanded');
            this.textContent = isExpanded ? 'Tutup' : 'Selengkapnya';
        });
    });
});
