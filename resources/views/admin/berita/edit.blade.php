@extends('admin.layouts.app')
@section('title', 'Edit Berita')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    @include('admin.berita._form')
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Perbarui Berita</button>
                        <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
