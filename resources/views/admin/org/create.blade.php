@extends('admin.layouts.app')
@section('title', 'Tambah Anggota Organisasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <a href="{{ route('admin.org.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.org.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.org._form', ['member' => null])
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-info text-white">Tambah Anggota</button>
                        <a href="{{ route('admin.org.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
