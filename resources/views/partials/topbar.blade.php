<div class="hero-navbar">
    <!-- Burger Menu Button -->
    <button class="nav-btn nav-burger" id="burgerBtn" type="button" onclick="openSidebar()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <!-- Center Logo Pills (clickable, go to home) -->
    <a href="{{ url('/') }}" class="nav-logo-container">
        <div class="logo-pill">
            <img src="{{ asset('images/logo/logo-man3.png') }}" alt="Logo MAN 3" class="logo-img">
        </div>
        <div class="logo-pill">
            <img src="{{ asset('images/logo/logo-pmr.jpg') }}" alt="Logo PMR" class="logo-img">
        </div>
        <div class="logo-pill">
            <img src="{{ asset('images/logo/logo-wira242.jpg') }}" alt="Logo Wira 242" class="logo-img">
        </div>
    </a>

    <!-- Search Button -->
    <button class="nav-btn nav-search" type="button" id="searchOpenBtn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
        </svg>
    </button>
</div>

<!-- Search Overlay -->
<div class="topbar-search-overlay" id="searchOverlay" aria-hidden="true">
    <div class="topbar-search-form">
        <svg class="topbar-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
        </svg>
        <div class="topbar-search-wrap">
            <input type="text" id="searchInput"
                   class="topbar-search-input"
                   placeholder="Cari berita PMR Wira 242..."
                   autocomplete="off"
                   aria-autocomplete="list"
                   aria-controls="searchSuggestions">
            <!-- Suggestion Dropdown -->
            <div class="search-suggestions" id="searchSuggestions" role="listbox"></div>
        </div>
        <button type="button" class="topbar-search-btn" id="searchSubmitBtn">Cari</button>
        <button type="button" class="topbar-search-close" id="searchCloseBtn" aria-label="Tutup">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function () {
    var searchUrl      = '{{ route('search.index') }}';
    var suggestionsUrl = '{{ route('search.suggestions') }}';

    var debounceTimer = null;
    var activeIndex   = -1;

    function openSearch() {
        var overlay = document.getElementById('searchOverlay');
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        setTimeout(function () {
            document.getElementById('searchInput').focus();
        }, 120);
    }

    function closeSearch() {
        document.getElementById('searchOverlay').classList.remove('is-open');
        document.getElementById('searchOverlay').setAttribute('aria-hidden', 'true');
        document.getElementById('searchInput').value = '';
        hideSuggestions();
    }

    function doSearch(q) {
        q = (q || document.getElementById('searchInput').value).trim();
        if (!q) { document.getElementById('searchInput').focus(); return; }
        hideSuggestions();
        window.location.href = searchUrl + '?q=' + encodeURIComponent(q);
    }

    function hideSuggestions() {
        var box = document.getElementById('searchSuggestions');
        box.innerHTML = '';
        box.classList.remove('is-open');
        activeIndex = -1;
    }

    var typeLabel = { berita: 'Berita', program: 'Program' };
    var typeColor = { berita: '#c0392b', program: '#1a6eb5' };

    function renderSuggestions(items, q) {
        var box = document.getElementById('searchSuggestions');
        if (!items.length) { hideSuggestions(); return; }

        var re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        var html = items.map(function (item, i) {
            var highlighted = item.title.replace(re, '<mark>$1</mark>');
            var badgeStyle = 'background:' + (typeColor[item.type] || '#888') + '22;color:' + (typeColor[item.type] || '#888') + ';border:1px solid ' + (typeColor[item.type] || '#888') + '44';
            return '<div class="search-suggestion-item" role="option" data-index="' + i + '" data-url="' + item.url + '" data-title="' + item.title.replace(/"/g, '&quot;') + '">' +
                '<img src="' + item.image + '" alt="" class="sugg-thumb">' +
                '<div class="sugg-body">' +
                    '<span class="sugg-title">' + highlighted + '</span>' +
                    '<span class="sugg-meta">' +
                        '<span class="sugg-badge" style="' + badgeStyle + '">' + (typeLabel[item.type] || item.type) + '</span>' +
                        (item.meta ? '<span class="sugg-date">' + item.meta + '</span>' : '') +
                    '</span>' +
                '</div>' +
                '<svg class="sugg-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>' +
            '</div>';
        }).join('');

        html += '<div class="search-suggestion-footer">' +
            '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>' +
            'Cari semua hasil untuk <strong>&ldquo;' + q + '&rdquo;</strong>' +
        '</div>';

        box.innerHTML = html;
        box.classList.add('is-open');
        activeIndex = -1;

        box.querySelectorAll('.search-suggestion-item').forEach(function (el) {
            el.addEventListener('mousedown', function (e) {
                e.preventDefault();
                window.location.href = el.dataset.url;
            });
        });

        box.querySelector('.search-suggestion-footer').addEventListener('mousedown', function (e) {
            e.preventDefault();
            doSearch();
        });
    }

    function fetchSuggestions(q) {
        if (q.length < 2) { hideSuggestions(); return; }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            fetch(suggestionsUrl + '?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) { renderSuggestions(data, q); })
                .catch(function () { hideSuggestions(); });
        }, 220);
    }

    function navigateSuggestions(dir) {
        var items = document.querySelectorAll('.search-suggestion-item');
        if (!items.length) return;
        if (activeIndex >= 0) items[activeIndex].classList.remove('is-active');
        activeIndex = (activeIndex + dir + items.length) % items.length;
        items[activeIndex].classList.add('is-active');
        document.getElementById('searchInput').value = items[activeIndex].dataset.title;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('searchOpenBtn').addEventListener('click', openSearch);
        document.getElementById('searchSubmitBtn').addEventListener('click', function () { doSearch(); });
        document.getElementById('searchCloseBtn').addEventListener('click', closeSearch);

        var input = document.getElementById('searchInput');
        input.addEventListener('input', function () { fetchSuggestions(this.value.trim()); });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter')           { doSearch(); }
            else if (e.key === 'ArrowDown')  { e.preventDefault(); navigateSuggestions(1); }
            else if (e.key === 'ArrowUp')    { e.preventDefault(); navigateSuggestions(-1); }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSearch();
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#searchOverlay')) hideSuggestions();
        });
    });
})();
</script>
