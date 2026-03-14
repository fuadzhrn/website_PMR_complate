@extends('admin.layouts.app')
@section('title', 'Kelola Berita')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('admin.berita.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Berita</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th width="70">Foto</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Featured</th>
                    <th><i class="bi bi-eye"></i></th>
                    <th><i class="bi bi-heart"></i></th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($berita as $item)
                <tr>
                    <td class="text-muted small">{{ $loop->iteration }}</td>
                    <td>
                        @if($item->image)
                            <img src="{{ asset($item->image) }}" width="48" height="36" style="object-fit:cover;border-radius:4px">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="width:48px;height:36px;font-size:.7rem">–</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ Str::limit($item->title, 55) }}</div>
                        <div class="text-muted small">{{ $item->author }}</div>
                    </td>
                    <td class="small text-muted">{{ $item->date }}</td>
                    <td>
                        @if($item->is_featured)
                            <span class="badge bg-warning text-dark">Featured</span>
                        @endif
                    </td>
                    <td class="small">{{ $item->views }}</td>
                    <td class="small">{{ $item->likes }}</td>
                    <td>
                        <a href="{{ route('admin.berita.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.berita.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berita ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada berita.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($berita->hasPages())
    <div class="card-footer bg-white">
        {{ $berita->links() }}
    </div>
    @endif
</div>
@endsection
