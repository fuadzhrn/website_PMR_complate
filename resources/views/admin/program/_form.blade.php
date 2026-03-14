@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">Judul Program <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $program->title ?? '') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php
        $rawDate = old('date', $program->date ?? '');
        if ($rawDate && str_contains($rawDate, '/')) {
            $clean = preg_replace('/\s*\/\s*/', '/', trim($rawDate));
            try { $rawDate = \Carbon\Carbon::createFromFormat('d/m/Y', $clean)->format('Y-m-d'); } catch (\Exception $e) {}
        }
        $dateVal = $rawDate ?: now()->format('Y-m-d');
    @endphp
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Tanggal</label>
        <input type="date" name="date" class="form-control" value="{{ $dateVal }}">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Lokasi</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $program->location ?? '') }}">
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Penulis / Penanggung Jawab</label>
        <input type="text" name="author" class="form-control" value="{{ old('author', $program->author ?? '') }}">
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(['selesai' => 'Selesai', 'berlangsung' => 'Berlangsung', 'akan-datang' => 'Akan Datang'] as $val => $label)
            <option value="{{ $val }}" {{ old('status', $program->status ?? 'selesai') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Thumbnail Program</label>
        @if(isset($program) && $program->image)
            <div class="mb-2">
                <img src="{{ asset($program->image) }}" height="80" class="rounded border" style="object-fit:cover">
                <small class="d-block text-muted mt-1">Gambar saat ini. Pilih file baru untuk mengganti.</small>
            </div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*">
        <small class="text-muted">Maks. 4 MB.</small>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Intro / Ringkasan</label>
        <textarea name="intro" rows="2" class="form-control" placeholder="Deskripsi singkat program...">{{ old('intro', $program->intro ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Isi Konten</label>
        <small class="text-muted d-block mb-1">Tulis setiap paragraf di baris baru.</small>
        <textarea name="paragraphs" rows="8" class="form-control" placeholder="Paragraf pertama...&#10;Paragraf kedua...">{{ old('paragraphs', isset($program) ? implode("\n", $program->paragraphs ?? []) : '') }}</textarea>
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="has_report" value="0">
            <input class="form-check-input" type="checkbox" name="has_report" value="1" id="hasReport"
                {{ old('has_report', ($program->has_report ?? false)) ? 'checked' : '' }}
                onchange="document.getElementById('reportFileWrap').style.display = this.checked ? 'block' : 'none'">
            <label class="form-check-label" for="hasReport">Ada laporan/dokumentasi PDF?</label>
        </div>
    </div>

    <div class="col-12" id="reportFileWrap"
        style="display: {{ old('has_report', ($program->has_report ?? false)) ? 'block' : 'none' }}">
        <label class="form-label fw-semibold">File Laporan (PDF)</label>
        @if(isset($program) && $program->report_file)
            <div class="mb-2 d-flex align-items-center gap-2">
                <svg width="16" height="16" fill="currentColor" class="text-danger" viewBox="0 0 16 16">
                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0H4zm5.5 0v4H14L9.5 0zM4.5 8h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1zm0 2h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1zm0 2h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1 0-1z"/>
                </svg>
                <a href="{{ asset($program->report_file) }}" target="_blank" class="text-sm">{{ basename($program->report_file) }}</a>
                <small class="text-muted">&mdash; Pilih file baru untuk mengganti</small>
            </div>
        @endif
        <input type="file" name="report_file" class="form-control @error('report_file') is-invalid @enderror"
            accept="application/pdf">
        @error('report_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Format PDF. Maks. 10 MB.</small>
    </div>
</div>
