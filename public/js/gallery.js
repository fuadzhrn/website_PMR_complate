document.addEventListener('DOMContentLoaded', function () {

    /* -----------------------------------------------------------
       State
    ----------------------------------------------------------- */
    var yearSelect  = document.getElementById('galleryYearSelect');
    var activeYear  = yearSelect ? parseInt(yearSelect.value) : new Date().getFullYear();
    var activeMonth = 'all';

    // Read initial selected month from server-rendered pills
    var initActivePill = document.querySelector('#galleryMonthPills .gallery-month-pill.active');
    if (initActivePill) activeMonth = initActivePill.dataset.month;

    /* -----------------------------------------------------------
       Helpers
    ----------------------------------------------------------- */
    var MONTH_NAMES = {
        1:'Januari', 2:'Februari', 3:'Maret', 4:'April',
        5:'Mei', 6:'Juni', 7:'Juli', 8:'Agustus',
        9:'September', 10:'Oktober', 11:'November', 12:'Desember'
    };

    function escapeAttr(s) {
        return String(s || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');
    }
    function escapeHtml(s) {
        return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* -----------------------------------------------------------
       Render helpers
    ----------------------------------------------------------- */
    function renderMonthPills(monthsForYear, selectedMonth) {
        var container = document.getElementById('galleryMonthPills');
        if (!container) return;

        var html = '<button type="button" class="gallery-month-pill' +
            (String(selectedMonth) === 'all' ? ' active' : '') +
            '" data-month="all">Semua</button>';

        monthsForYear.forEach(function (m) {
            html += '<button type="button" class="gallery-month-pill' +
                (String(selectedMonth) === String(m) ? ' active' : '') +
                '" data-month="' + m + '">' + (MONTH_NAMES[m] || m) + '</button>';
        });
        container.innerHTML = html;

        var filterRow = document.getElementById('galleryMonthFilterRow');
        if (filterRow) filterRow.style.display = monthsForYear.length >= 1 ? '' : 'none';
    }

    function renderPhotos(photosByMonth, selectedYear) {
        var container = document.getElementById('galleryPhotoContainer');
        if (!container) return;

        var keys = Object.keys(photosByMonth).map(Number).sort(function (a, b) { return a - b; });

        if (keys.length === 0) {
            container.innerHTML = '<div class="gallery-empty-state"><p>Belum ada foto untuk periode ini.</p></div>';
            refreshPhotos();
            return;
        }

        var html = '';
        keys.forEach(function (month) {
            var photos = photosByMonth[month];
            html += '<section class="gallery-month-section">';
            html += '<div class="gallery-month-header">';
            html += '<span class="gallery-month-label">' + (MONTH_NAMES[month] || month) + '</span>';
            html += '<span class="gallery-month-year">' + selectedYear + '</span>';
            html += '<span class="gallery-month-count">' + photos.length + ' foto</span>';
            html += '<div class="gallery-month-line"></div>';
            html += '</div>';
            html += '<div class="photo-grid">';
            photos.forEach(function (p) {
                var titleLimit = p.title ? escapeHtml(p.title.substring(0, 22)) : '';
                var badgeTitle = p.title ? '<span>' + titleLimit + '</span> · ' : '';
                html += '<button type="button" class="photo-item"' +
                    ' data-src="'   + escapeAttr(p.src)           + '"' +
                    ' data-title="' + escapeAttr(p.title || '')    + '"' +
                    ' data-date="'  + escapeAttr(p.dateFormatted)  + '">';
                html += '<img src="' + escapeAttr(p.src) + '" alt="' + escapeAttr(p.title || 'Dokumentasi PMR') + '" loading="lazy">';
                html += '<div class="photo-detail-badge">' + badgeTitle + '<span>' + escapeHtml(p.date) + '</span></div>';
                html += '</button>';
            });
            html += '</div></section>';
        });
        container.innerHTML = html;
        refreshPhotos();
    }

    /* -----------------------------------------------------------
       AJAX load
    ----------------------------------------------------------- */
    function loadGallery(year, month) {
        activeYear  = year;
        activeMonth = month;

        fetch('/gallery/data?year=' + year + '&month=' + (month || 'all'))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                renderMonthPills(data.monthsForYear, data.selectedMonth);
                renderPhotos(data.photosByMonth, data.selectedYear);

                var url = '/gallery?year=' + year;
                if (month !== 'all') url += '&month=' + month;
                history.replaceState(null, '', url);
            });
    }

    /* -----------------------------------------------------------
       Modal
    ----------------------------------------------------------- */
    var modal      = document.getElementById('galleryModal');
    var modalImage = document.getElementById('galleryModalImage');
    var modalClose = document.getElementById('galleryModalClose');
    var modalPrev  = document.getElementById('galleryModalPrev');
    var modalNext  = document.getElementById('galleryModalNext');

    var allPhotos  = [];
    var currentIdx = 0;

    function refreshPhotos() {
        allPhotos = Array.from(document.querySelectorAll('.photo-item[data-src]'));
    }
    refreshPhotos(); // initial scan

    function updateNav() {
        if (modalPrev) modalPrev.disabled = currentIdx === 0;
        if (modalNext) modalNext.disabled = currentIdx === allPhotos.length - 1;
    }

    function openModal(index) {
        currentIdx = index;
        var btn = allPhotos[index];
        modalImage.src = btn.dataset.src;
        modalImage.alt = btn.dataset.title || 'Foto kegiatan PMR';
        modal.classList.add('is-visible');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        updateNav();
    }

    function closeModal() {
        modal.classList.remove('is-visible');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        modalImage.src = '';
    }

    function showPrev() { if (currentIdx > 0) openModal(currentIdx - 1); }
    function showNext() { if (currentIdx < allPhotos.length - 1) openModal(currentIdx + 1); }

    // Delegated click for photos (works after AJAX re-render)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.photo-item[data-src]');
        if (!btn) return;
        refreshPhotos();
        var idx = allPhotos.indexOf(btn);
        if (idx !== -1) openModal(idx);
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalPrev)  modalPrev.addEventListener('click', showPrev);
    if (modalNext)  modalNext.addEventListener('click', showNext);

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
    }

    /* -----------------------------------------------------------
       Year select AJAX
    ----------------------------------------------------------- */
    if (yearSelect) {
        yearSelect.addEventListener('change', function () {
            loadGallery(parseInt(this.value), 'all');
        });
    }

    /* -----------------------------------------------------------
       Month pill delegation
    ----------------------------------------------------------- */
    document.addEventListener('click', function (e) {
        var pill = e.target.closest('#galleryMonthPills .gallery-month-pill');
        if (!pill) return;
        loadGallery(activeYear, pill.dataset.month);
    });

    /* -----------------------------------------------------------
       Upload popup
    ----------------------------------------------------------- */
    var toggleBtn      = document.getElementById('toggleUploadForm');
    var uploadOverlay  = document.getElementById('galleryUploadSection');
    var uploadCloseBtn = document.getElementById('galleryUploadClose');
    var uploadCancelBtn = document.getElementById('galleryUploadCancel');

    function openUploadPopup() {
        if (!uploadOverlay) return;
        uploadOverlay.classList.add('is-open');
        uploadOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeUploadPopup() {
        if (!uploadOverlay) return;
        uploadOverlay.classList.remove('is-open');
        uploadOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (toggleBtn && uploadOverlay) {
        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openUploadPopup();
        });
    }
    if (uploadCloseBtn)  uploadCloseBtn.addEventListener('click', closeUploadPopup);
    if (uploadCancelBtn) uploadCancelBtn.addEventListener('click', function (e) {
        e.preventDefault();
        closeUploadPopup();
    });
    if (uploadOverlay) {
        uploadOverlay.addEventListener('click', function (e) {
            if (e.target === uploadOverlay) closeUploadPopup();
        });
    }

    /* -----------------------------------------------------------
       Keyboard shortcuts (modal + upload)
    ----------------------------------------------------------- */
    document.addEventListener('keydown', function (e) {
        if (modal && modal.classList.contains('is-visible')) {
            if (e.key === 'Escape')      closeModal();
            if (e.key === 'ArrowLeft')  showPrev();
            if (e.key === 'ArrowRight') showNext();
            return;
        }
        if (e.key === 'Escape' && uploadOverlay && uploadOverlay.classList.contains('is-open')) {
            closeUploadPopup();
        }
    });
});

