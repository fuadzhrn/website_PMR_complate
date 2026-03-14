@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
    $baseYear = 2002;
    $computeAngkatan = function (?string $period) use ($baseYear) {
        if (!$period || !preg_match('/^(\d{4})-(\d{4})$/', $period, $m)) {
            return '';
        }
        $startYear = (int) $m[1];
        return max(1, $startYear - $baseYear + 1);
    };

    $posisiOptions = [
        'ketua-umum'  => 'Ketua Umum',
        'sekretaris'  => 'Sekretaris',
        'bendahara'   => 'Bendahara',
        'kaderisasi'  => 'Kaderisasi',
        'humas'       => 'Humas',
        'kesling'     => 'Kesling',
        'danus'       => 'Danus',
    ];
    $currentRole   = old('role_group', $member->role_group ?? 'pengurus');
    $currentPosKey = old('position_key', $member->position_key ?? '');
    $currentPeriod = old('period', $member->period ?? '2025-2026');
    $currentAngkatan = old('angkatan', $member->angkatan ?? $computeAngkatan($currentPeriod));
@endphp

<div class="row g-3">

    {{-- Periode --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
        <input type="text" name="period" id="periodInput" class="form-control" placeholder="e.g. 2025-2026"
            value="{{ $currentPeriod }}" required pattern="\d{4}-\d{4}">
        <small class="text-muted">Format: TAHUN-TAHUN, contoh <code>2025-2026</code>.</small>
    </div>

    {{-- Angkatan (otomatis dari periode) --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Angkatan <span class="text-danger">*</span></label>
        <input type="text" id="angkatanDisplay" class="form-control" value="{{ $currentAngkatan }}" readonly>
        <input type="hidden" name="angkatan" id="angkatanInput" value="{{ $currentAngkatan }}">
        <small class="text-muted">Otomatis dihitung dari periode. Angkatan 1 dimulai pada periode 2002-2003.</small>
    </div>

    {{-- Tipe --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
        <div class="d-flex gap-4 mt-1">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="role_group" id="tipe_pengurus" value="pengurus"
                    {{ $currentRole === 'pengurus' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipe_pengurus">Pengurus (Jabatan Utama)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="role_group" id="tipe_staf" value="staf"
                    {{ $currentRole === 'staf' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipe_staf">Staf / Anggota</label>
            </div>
        </div>
    </div>

    {{-- Posisi Bagan (pengurus saja) --}}
    <div class="col-sm-6" id="posisiField">
        <label class="form-label fw-semibold">Posisi di Bagan <span class="text-danger">*</span></label>
        <select name="position_key" class="form-select" id="posisiSelect">
            <option value="">– Pilih posisi –</option>
            @foreach($posisiOptions as $key => $label)
                <option value="{{ $key }}" {{ $currentPosKey === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <small class="text-muted">Menentukan slot di bagan struktur organisasi.</small>
    </div>

    {{-- Label Jabatan --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Label Jabatan <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
            placeholder="e.g. Ketua Umum / Sekretaris I"
            value="{{ old('title', $member->title ?? '') }}" required>
        <small class="text-muted">Teks yang tampil di bawah foto pada bagan.</small>
    </div>

    {{-- Nama --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" placeholder="Nama lengkap"
            value="{{ old('name', $member->name ?? '') }}" required>
    </div>

    {{-- Domisili --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Domisili <span class="text-danger">*</span></label>
        <input type="text" name="domisili" class="form-control" placeholder="e.g. Makassar"
            value="{{ old('domisili', $member->domisili ?? '') }}" required>
    </div>

    {{-- Bergabung di bawah (staf saja) --}}
    <div class="col-sm-6" id="parentKeyField">
        <label class="form-label fw-semibold">Bergabung di bawah jabatan</label>
        <select name="parent_key" class="form-select">
            <option value="">– Tidak ada –</option>
            @foreach($pengurus as $p)
            <option value="{{ $p->position_key }}"
                {{ old('parent_key', $member->parent_key ?? '') === $p->position_key ? 'selected' : '' }}>
                {{ $p->title }} — {{ $p->name }}
            </option>
            @endforeach
        </select>
        <small class="text-muted">Staf ini akan muncul di bawah jabatan yang dipilih.</small>
    </div>

    {{-- Urutan Tampil --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Urutan Tampil</label>
        <input type="number" name="sort_order" class="form-control"
            value="{{ old('sort_order', $member->sort_order ?? 0) }}" min="0">
        <small class="text-muted">Angka kecil = tampil lebih awal.</small>
    </div>

    {{-- Foto --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Foto</label>
        @if(isset($member) && $member->photo)
            <div class="mb-2 d-flex align-items-center gap-3">
                <img src="{{ asset($member->photo) }}" id="currentPhoto" height="64" width="64"
                    class="rounded-circle border" style="object-fit:cover">
                <small class="text-muted">Foto saat ini. Pilih file baru untuk mengganti.</small>
            </div>
        @endif
        <input type="file" name="photo" class="form-control" accept="image/*" id="fotoInput">
        <div id="fotoPreview" class="mt-2" style="display:none">
            <img id="fotoPreviewImg" height="64" width="64" class="rounded-circle border" style="object-fit:cover">
            <small class="text-muted ms-2">Preview foto baru</small>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var posisiField    = document.getElementById('posisiField');
    var parentKeyField = document.getElementById('parentKeyField');
    var posisiSelect   = document.getElementById('posisiSelect');
    var periodInput    = document.getElementById('periodInput');
    var angkatanInput  = document.getElementById('angkatanInput');
    var angkatanDisplay = document.getElementById('angkatanDisplay');

    function toggleFields() {
        var val = document.querySelector('input[name="role_group"]:checked')?.value;
        if (val === 'pengurus') {
            posisiField.style.display    = '';
            parentKeyField.style.display = 'none';
            posisiSelect.required = true;
        } else {
            posisiField.style.display    = 'none';
            parentKeyField.style.display = '';
            posisiSelect.required = false;
        }
    }

    document.querySelectorAll('input[name="role_group"]')
        .forEach(function (r) { r.addEventListener('change', toggleFields); });
    toggleFields();

    function computeAngkatan(period) {
        var match = /^([0-9]{4})-([0-9]{4})$/.exec((period || '').trim());
        if (!match) return '';

        var startYear = parseInt(match[1], 10);
        var angkatan = startYear - 2002 + 1;
        return angkatan > 0 ? String(angkatan) : '';
    }

    function syncAngkatan() {
        var value = computeAngkatan(periodInput ? periodInput.value : '');
        if (angkatanInput) angkatanInput.value = value;
        if (angkatanDisplay) angkatanDisplay.value = value;
    }

    if (periodInput) {
        periodInput.addEventListener('input', syncAngkatan);
        periodInput.addEventListener('change', syncAngkatan);
        syncAngkatan();
    }

    // Photo preview
    var fotoInput = document.getElementById('fotoInput');
    if (fotoInput) {
        fotoInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('fotoPreviewImg').src = e.target.result;
                    document.getElementById('fotoPreview').style.display = 'flex';
                    document.getElementById('fotoPreview').style.alignItems = 'center';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>
