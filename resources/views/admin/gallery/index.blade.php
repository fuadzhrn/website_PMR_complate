@extends('admin.layouts.app')
@section('title', 'Kelola Galeri')

@push('styles')
<style>
/* ── Filter bar ── */
.ag-filter-bar { display:flex; align-items:center; flex-wrap:wrap; gap:10px; }
.ag-year-select {
    font-size: 13px; font-weight:600;
    border: 1.5px solid #dee2e6; border-radius: 999px;
    padding: 5px 30px 5px 14px;
    appearance: none; -webkit-appearance: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23495057' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;
    cursor:pointer;
}
.ag-month-scroll { overflow-x:auto; overflow-y:hidden; scrollbar-width:none; }
.ag-month-scroll::-webkit-scrollbar { display:none; }
.ag-month-pills { display:flex; gap:6px; width:max-content; }
.ag-pill {
    font-size:11px; font-weight:500; white-space:nowrap;
    padding:4px 14px; border-radius:999px;
    border:1.5px solid #dee2e6; background:transparent; color:#6c757d;
    cursor:pointer; transition:all .15s;
}
.ag-pill:hover { background:#f0f0f0; color:#212529; }
.ag-pill.active { background:#212529; color:#fff; border-color:#212529; }

/* ── Bulk action toolbar ── */
#bulkBar {
    position:sticky; top:0; z-index:50;
    background:#fff3cd; border:1px solid #ffc107;
    border-radius:8px; padding:10px 16px;
    display:none; align-items:center; gap:12px;
    margin-bottom:16px;
}
#bulkBar.visible { display:flex; }

/* ── Month section header ── */
.ag-month-heading {
    font-size:13px; font-weight:700; letter-spacing:.18em;
    text-transform:uppercase; color:#343a40;
    display:flex; align-items:center; gap:10px;
    margin:24px 0 12px;
}
.ag-month-heading .ag-line { flex:1; height:1px; background:#dee2e6; }
.ag-month-heading .ag-count {
    font-size:11px; font-weight:400; letter-spacing:0;
    text-transform:none; color:#adb5bd;
    background:#f8f9fa; border:1px solid #dee2e6;
    padding:1px 10px; border-radius:999px;
}

/* ── Photo card ── */
.ag-card { position:relative; border-radius:8px; overflow:hidden; border:2px solid transparent; transition:border-color .15s; }
.ag-card.selected { border-color:#0d6efd; }
.ag-card img { width:100%; height:130px; object-fit:cover; display:block; }
.ag-card-cb {
    position:absolute; top:7px; left:7px;
    width:20px; height:20px; cursor:pointer;
    accent-color:#0d6efd;
}
.ag-card-info { padding:6px 8px 4px; background:#fff; font-size:11px; color:#6c757d; }
.ag-card-title { font-weight:600; color:#343a40; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* select-all row */
.ag-select-all-row { display:flex; align-items:center; gap:8px; font-size:12px; color:#495057; margin-bottom:4px; cursor:pointer; }
</style>
@endpush

@section('content')
@php
    $monthNames = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
@endphp

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Info bar --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <p class="text-muted small mb-0">
        Upload foto baru di halaman publik <a href="{{ route('gallery.index') }}" target="_blank">Galeri</a>.
        Total <strong>{{ $itemsByMonth->flatten()->count() }}</strong> foto.
    </p>
</div>

{{-- ── Filter bar ── --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="ag-filter-bar">
            {{-- Year dropdown --}}
            <form method="GET" action="{{ route('admin.gallery.index') }}" id="yearForm">
                <select name="year" class="ag-year-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ (int)$y===(int)$selectedYear?'selected':'' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>

            {{-- Month pills (scrollable) --}}
            @if(count($monthsForYear))
            <div class="ag-month-scroll flex-grow-1">
                <form method="GET" action="{{ route('admin.gallery.index') }}" class="ag-month-pills">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <button type="submit" name="month" value="all"
                        class="ag-pill {{ $selectedMonth==='all'?'active':'' }}">Semua</button>
                    @foreach($monthsForYear as $m)
                    <button type="submit" name="month" value="{{ $m }}"
                        class="ag-pill {{ (string)$selectedMonth===(string)$m?'active':'' }}">
                        {{ $monthNames[$m] ?? $m }}
                    </button>
                    @endforeach
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Bulk action toolbar ── --}}
<div id="bulkBar">
    <span class="fw-semibold" id="bulkLabel">0 foto dipilih</span>
    <form method="POST" action="{{ route('admin.gallery.bulkDestroy') }}" id="bulkForm"
          onsubmit="return confirmBulk()">
        @csrf
        <div id="bulkHiddenIds"></div>
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i> Hapus yang dipilih
        </button>
    </form>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAll()">Batalkan</button>
</div>

{{-- ── Gallery grouped by month ── --}}
@if($itemsByMonth->isEmpty())
    <div class="text-center text-muted py-5">Belum ada foto untuk periode ini.</div>
@else
    <form id="selectAllMonthForm">
    @foreach($itemsByMonth as $month => $photos)
    <div class="ag-month-section" data-month="{{ $month }}">
        <div class="ag-month-heading">
            <label class="ag-select-all-row" title="Pilih semua di bulan ini">
                <input type="checkbox" class="month-select-all" data-month="{{ $month }}" style="accent-color:#0d6efd;width:16px;height:16px;">
                <strong>{{ $monthNames[(int)$month] ?? $month }}</strong>
                <span style="font-weight:400;color:#868e96">{{ $selectedYear }}</span>
            </label>
            <span class="ag-count">{{ $photos->count() }} foto</span>
            <div class="ag-line"></div>
        </div>

        <div class="row g-3">
            @foreach($photos as $photo)
            <div class="col-6 col-md-4 col-xl-3">
                <div class="ag-card" data-id="{{ $photo->id }}">
                    <input type="checkbox" class="ag-card-cb photo-cb"
                        data-id="{{ $photo->id }}" data-month="{{ $month }}"
                        title="Pilih foto ini">
                    <img src="{{ asset($photo->path) }}" alt="{{ $photo->title }}" loading="lazy">
                    <div class="ag-card-info">
                        <div class="ag-card-title">{{ $photo->title ?: '(tanpa judul)' }}</div>
                        <div>{{ \Carbon\Carbon::parse($photo->uploaded_at)->format('d/m/Y') }}</div>
                    </div>
                    <div class="px-2 pb-2">
                        <form action="{{ route('admin.gallery.destroy', $photo->id) }}" method="POST"
                              onsubmit="return confirm('Hapus foto ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" style="font-size:11px">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    </form>
@endif

@push('scripts')
<script>
(function () {
    const selected = new Set();
    const bulkBar  = document.getElementById('bulkBar');
    const bulkLabel = document.getElementById('bulkLabel');
    const bulkHiddenIds = document.getElementById('bulkHiddenIds');

    function refreshBar() {
        const n = selected.size;
        bulkLabel.textContent = n + ' foto dipilih';
        bulkBar.classList.toggle('visible', n > 0);
        // sync hidden inputs
        bulkHiddenIds.innerHTML = '';
        selected.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
            bulkHiddenIds.appendChild(inp);
        });
    }

    // Individual checkboxes
    document.querySelectorAll('.photo-cb').forEach(cb => {
        cb.addEventListener('change', function () {
            const id = this.dataset.id;
            if (this.checked) selected.add(id); else selected.delete(id);
            // sync parent card style
            this.closest('.ag-card').classList.toggle('selected', this.checked);
            // sync month select-all state
            syncMonthHeader(this.dataset.month);
            refreshBar();
        });
    });

    // Month-level select-all
    document.querySelectorAll('.month-select-all').forEach(cb => {
        cb.addEventListener('change', function () {
            const month = this.dataset.month;
            document.querySelectorAll('.photo-cb[data-month="'+ month +'"]').forEach(pcb => {
                pcb.checked = this.checked;
                pcb.closest('.ag-card').classList.toggle('selected', this.checked);
                if (this.checked) selected.add(pcb.dataset.id);
                else selected.delete(pcb.dataset.id);
            });
            refreshBar();
        });
    });

    function syncMonthHeader(month) {
        const all = document.querySelectorAll('.photo-cb[data-month="'+ month +'"]');
        const header = document.querySelector('.month-select-all[data-month="'+ month +'"]');
        if (!header) return;
        const checked = [...all].filter(c => c.checked).length;
        header.checked = checked === all.length;
        header.indeterminate = checked > 0 && checked < all.length;
    }

    window.clearAll = function () {
        selected.clear();
        document.querySelectorAll('.photo-cb, .month-select-all').forEach(cb => {
            cb.checked = false; cb.indeterminate = false;
        });
        document.querySelectorAll('.ag-card').forEach(c => c.classList.remove('selected'));
        refreshBar();
    };

    window.confirmBulk = function () {
        return confirm(selected.size + ' foto akan dihapus permanen. Lanjutkan?');
    };
})();
</script>
@endpush
@endsection
