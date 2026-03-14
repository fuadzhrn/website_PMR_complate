@extends('admin.layouts.app')
@section('title', 'Moderasi Komentar')

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Berita</th>
                    <th>Nama</th>
                    <th>Komentar</th>
                    <th>Waktu</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $c)
                <tr>
                    <td class="text-muted small">{{ $loop->iteration }}</td>
                    <td>
                        @if($c->berita)
                            <a href="{{ route('berita.detail', $c->berita->slug) }}" target="_blank" class="text-decoration-none small">
                                {{ Str::limit($c->berita->title, 40) }}
                            </a>
                        @else
                            <span class="text-muted">–</span>
                        @endif
                    </td>
                    <td class="fw-semibold small">{{ $c->name }}</td>
                    <td class="small">{{ Str::limit($c->message, 80) }}</td>
                    <td class="text-muted small text-nowrap">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.comment.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada komentar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comments->hasPages())
    <div class="card-footer bg-white">
        {{ $comments->links() }}
    </div>
    @endif
</div>
@endsection
