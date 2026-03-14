@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card blue h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Berita</div>
                    <div class="fs-3 fw-bold">{{ $totalBerita }}</div>
                </div>
                <i class="bi bi-newspaper fs-2 text-primary opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card green h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Program Kegiatan</div>
                    <div class="fs-3 fw-bold">{{ $totalProgram }}</div>
                </div>
                <i class="bi bi-calendar-check fs-2 text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card orange h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Foto Galeri</div>
                    <div class="fs-3 fw-bold">{{ $totalGallery }}</div>
                </div>
                <i class="bi bi-images fs-2 text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card red h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Komentar</div>
                    <div class="fs-3 fw-bold">{{ $totalKomentar }}</div>
                </div>
                <i class="bi bi-chat-dots fs-2 text-danger opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Shortcuts -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="bi bi-lightning-charge"></i> Aksi Cepat</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.berita.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Tambah Berita</a>
                    <a href="{{ route('admin.program.create') }}" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> Tambah Program</a>
                    <a href="{{ route('admin.home-content.index') }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit Beranda</a>
                    <a href="{{ route('admin.org.create') }}" class="btn btn-sm btn-info text-white"><i class="bi bi-person-plus"></i> Tambah Anggota</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Latest Data -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                Berita Terbaru
                <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($latestBerita as $item)
                <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                    @if($item->image)
                        <img src="{{ asset($item->image) }}" class="rounded" width="40" height="40" style="object-fit:cover">
                    @else
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px"><i class="bi bi-image text-white"></i></div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-truncate fw-semibold small">{{ $item->title }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $item->created_at->diffForHumans() }} · <i class="bi bi-eye"></i> {{ $item->views }}</div>
                    </div>
                    <a href="{{ route('admin.berita.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                </div>
                @empty
                <div class="text-muted text-center py-4">Belum ada berita.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                Program Terbaru
                <a href="{{ route('admin.program.index') }}" class="btn btn-sm btn-outline-success">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($latestProgram as $item)
                <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                    @if($item->image)
                        <img src="{{ asset($item->image) }}" class="rounded" width="40" height="40" style="object-fit:cover">
                    @else
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px"><i class="bi bi-image text-white"></i></div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-truncate fw-semibold small">{{ $item->title }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $item->created_at->diffForHumans() }} · <i class="bi bi-eye"></i> {{ $item->views }}</div>
                    </div>
                    <a href="{{ route('admin.program.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                </div>
                @empty
                <div class="text-muted text-center py-4">Belum ada program.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
