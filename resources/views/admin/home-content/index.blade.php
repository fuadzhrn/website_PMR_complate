@extends('admin.layouts.app')
@section('title', 'Edit Konten Beranda')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('admin.home-content.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Selayang Pandang -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold">Selayang Pandang</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="selayang_title" class="form-control" value="{{ old('selayang_title', $sections['selayang-pandang']->title ?? 'Selayang Pandang') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Teks <span class="text-danger">*</span></label>
                        <textarea name="selayang_content" rows="6" class="form-control" required>{{ old('selayang_content', $sections['selayang-pandang']->content ?? '') }}</textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Gambar</label>
                        @if(isset($sections['selayang-pandang']) && $sections['selayang-pandang']->image)
                            <div class="mb-2">
                                <img src="{{ asset($sections['selayang-pandang']->image) }}" height="80" class="rounded border" style="object-fit:cover">
                                <small class="d-block text-muted mt-1">Gambar saat ini. Pilih file baru untuk mengganti.</small>
                            </div>
                        @endif
                        <input type="file" name="selayang_image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Tentang Kami -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold">Tentang Kami</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="tentang_title" class="form-control" value="{{ old('tentang_title', $sections['tentang-kami']->title ?? 'Tentang Kami') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Teks <span class="text-danger">*</span></label>
                        <textarea name="tentang_content" rows="6" class="form-control" required>{{ old('tentang_content', $sections['tentang-kami']->content ?? '') }}</textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Gambar</label>
                        @if(isset($sections['tentang-kami']) && $sections['tentang-kami']->image)
                            <div class="mb-2">
                                <img src="{{ asset($sections['tentang-kami']->image) }}" height="80" class="rounded border" style="object-fit:cover">
                                <small class="d-block text-muted mt-1">Gambar saat ini. Pilih file baru untuk mengganti.</small>
                            </div>
                        @endif
                        <input type="file" name="tentang_image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Gambar Slider Hero -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                    Gambar Slider Hero
                    <small class="text-muted fw-normal">{{ $slideRecords->count() }} slide aktif</small>
                </div>
                <div class="card-body">

                    {{-- Slide yang sudah ada: ganti atau hapus --}}
                    @if($slideRecords->isNotEmpty())
                    <p class="text-muted small mb-2">Pilih file baru pada slide yang ingin diganti, atau klik Hapus untuk menghapus slide tersebut.</p>
                    <div class="row g-3 mb-4">
                        @foreach($slideRecords as $rec)
                        <div class="col-sm-6 col-lg-4">
                            <div class="border rounded p-2 h-100">
                                <img src="{{ asset($rec->image) }}" class="rounded mb-2" style="width:100%;height:100px;object-fit:cover">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-secondary">Slide #{{ $loop->iteration }}</span>
                                </div>
                                <input type="file" name="hero_slide_replace[{{ $rec->id }}]" class="form-control form-control-sm mb-2" accept="image/*">
                                <button type="submit" name="delete_slide" value="{{ $rec->id }}"
                                    class="btn btn-sm btn-outline-danger w-100"
                                    onclick="return confirm('Hapus slide ini?')">
                                    Hapus Slide
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Tambah slide baru --}}
                    <p class="text-muted small mb-1">Tambah slide baru (bisa pilih lebih dari satu file sekaligus):</p>
                    <input type="file" name="hero_slide_new[]" class="form-control" accept="image/*" multiple>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
