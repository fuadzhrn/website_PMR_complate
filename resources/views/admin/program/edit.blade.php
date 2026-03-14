@extends('admin.layouts.app')
@section('title', 'Edit Program Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="{{ route('admin.program.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.program.update', $program->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    @include('admin.program._form')
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Perbarui Program</button>
                        <a href="{{ route('admin.program.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Dokumentasi yang diupload pengunjung --}}
        @php
            $docSlug  = $program->slug;
            $docDir   = public_path('images/program/uploads/' . $docSlug);
            $docFiles = [];
            if (is_dir($docDir)) {
                foreach (scandir($docDir) as $f) {
                    if ($f === '.' || $f === '..') continue;
                    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                        $docFiles[] = 'images/program/uploads/' . $docSlug . '/' . $f;
                    }
                }
            }
        @endphp
        @if(count($docFiles) > 0)
        <div class="card mt-4">
            <div class="card-header fw-semibold">Foto Dokumentasi ({{ count($docFiles) }} foto)</div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif
                <div class="row g-2">
                    @foreach($docFiles as $docPath)
                    <div class="col-3">
                        <div class="position-relative">
                            <img src="{{ asset($docPath) }}" class="img-fluid rounded" style="height:90px;width:100%;object-fit:cover;">
                            <form method="POST" action="{{ route('admin.program.deleteDoc', $docSlug) }}" class="m-0" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                <input type="hidden" name="path" value="{{ $docPath }}">
                                <button type="submit" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 rounded-circle" style="width:22px;height:22px;font-size:13px;line-height:1;">&times;</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
