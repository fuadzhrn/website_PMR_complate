@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">
    {{-- Judul --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Judul Berita <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $berita->title ?? '') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Tanggal & Lokasi --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Tanggal Tampil</label>
        @php
            // Konversi format lama "21 / 02 / 2026" atau "21/02/2026" ke "2026-02-21" untuk date picker
            $rawDate = old('date', $berita->date ?? '');
            if ($rawDate && str_contains($rawDate, '/')) {
                // Normalisasi spasi di sekitar slash: "21 / 02 / 2026" → "21/02/2026"
                $clean = preg_replace('/\s*\/\s*/', '/', trim($rawDate));
                try {
                    $rawDate = \Carbon\Carbon::createFromFormat('d/m/Y', $clean)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            $dateVal = $rawDate ?: now()->format('Y-m-d');
        @endphp
        <input type="date" name="date" class="form-control" value="{{ $dateVal }}">
        <div class="form-text">Berita akan muncul di &quot;Berita Harian&quot; pada tanggal yang dipilih.</div>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Lokasi</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $berita->location ?? '') }}">
    </div>

    {{-- Penulis --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Penulis / Author</label>
        <input type="text" name="author" class="form-control" value="{{ old('author', $berita->author ?? '') }}">
    </div>

    {{-- Featured --}}
    <div class="col-sm-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="is_featured" value="0">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                {{ old('is_featured', ($berita->is_featured ?? false)) ? 'checked' : '' }}>
            <label class="form-check-label" for="isFeatured">Jadikan Berita Utama (Featured)</label>
        </div>
    </div>

    {{-- Thumbnail --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Thumbnail Berita</label>
        @if(isset($berita) && $berita->image)
            <div class="mb-2">
                <img src="{{ asset($berita->image) }}" height="80" class="rounded border" style="object-fit:cover">
                <small class="d-block text-muted mt-1">Gambar saat ini. Pilih file baru untuk mengganti.</small>
            </div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*">
        <small class="text-muted">Maks. 4 MB. Format: JPG, PNG, WebP.</small>
    </div>

    {{-- Isi Paragraf --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Isi Artikel</label>
        <small class="text-muted d-block mb-1">Tulis setiap paragraf di baris baru (Enter = paragraf baru).</small>
        <textarea name="paragraphs" rows="10" class="form-control @error('paragraphs') is-invalid @enderror" placeholder="Paragraf pertama...&#10;Paragraf kedua...&#10;Paragraf ketiga...">{{ old('paragraphs', isset($berita) ? implode("\n", $berita->paragraphs ?? []) : '') }}</textarea>
        @error('paragraphs')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
