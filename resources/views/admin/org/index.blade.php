@extends('admin.layouts.app')
@section('title', 'Struktur Organisasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    {{-- Period dropdown filter --}}
    <form method="GET" action="{{ route('admin.org.index') }}" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 text-muted small fw-semibold">Periode:</label>
        <select name="period" class="form-select form-select-sm" style="width:auto;min-width:120px" onchange="this.form.submit()">
            @forelse($periods as $p)
                <option value="{{ $p }}" {{ $p === $selectedPeriod ? 'selected' : '' }}>{{ $p }}</option>
            @empty
                <option disabled>Belum ada data</option>
            @endforelse
        </select>
    </form>
    <a href="{{ route('admin.org.create') }}" class="btn btn-info text-white flex-shrink-0">
        <i class="bi bi-person-plus"></i> Tambah Anggota
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="60">Foto</th>
                    <th>Label Jabatan</th>
                    <th>Nama</th>
                    <th>Domisili</th>
                    <th>Tipe</th>
                    <th>Posisi / Induk</th>
                    <th>Periode</th>
                    <th>Angkatan</th>
                    <th width="70">Urutan</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $m)
                <tr>
                    <td>
                        @if($m->photo)
                            <img src="{{ asset($m->photo) }}" width="40" height="40" class="rounded-circle" style="object-fit:cover">
                        @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                <i class="bi bi-person text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $m->title }}</td>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->domisili ?? '–' }}</td>
                    <td>
                        @if($m->role_group === 'pengurus')
                            <span class="badge bg-primary">Pengurus</span>
                        @else
                            <span class="badge bg-secondary">Staf</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        @if($m->role_group === 'pengurus')
                            <span class="badge bg-light text-dark border font-monospace">{{ $m->position_key }}</span>
                        @else
                            {{ $m->parent_key ?? '–' }}
                        @endif
                    </td>
                    <td><span class="badge bg-info text-dark">{{ $m->period }}</span></td>
                    <td><span class="badge bg-dark">Angkatan {{ $m->angkatan ?? '–' }}</span></td>
                    <td class="text-center">{{ $m->sort_order }}</td>
                    <td>
                        <a href="{{ route('admin.org.edit', $m->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.org.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus anggota ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data anggota untuk periode <strong>{{ $selectedPeriod }}</strong>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
