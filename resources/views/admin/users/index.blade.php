@extends('layouts.app')

@section('title', 'Pengguna Sistem')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Pengguna Sistem</span></nav>
            <h3>Pengguna Sistem</h3>
            <p>Kelola akun, status akses, dan peran administrator atau pengguna biasa.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus-fill"></i> Tambah pengguna</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
    @endif

    <section class="card">
        <div class="card-header spl-toolbar">
            <div><h4 class="spl-toolbar-title">Daftar pengguna <span class="spl-filter-count">{{ $users->count() }} hasil</span></h4><p class="spl-toolbar-subtitle">Hanya akun aktif yang dapat masuk ke sistem.</p></div>
        </div>
        <form action="{{ route('users.index') }}" method="GET" class="spl-filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-7">
                    <label for="cari" class="form-label">Cari pengguna</label>
                    <div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span><input id="cari" type="search" name="cari" class="form-control border-start-0" value="{{ request('cari') }}" placeholder="Nama atau alamat email"></div>
                </div>
                <div class="col-6 col-md-3">
                    <label for="status" class="form-label">Status akun</label>
                    <select id="status" name="status" class="form-select"><option value="">Semua status</option><option value="active" @selected(request('status') === 'active')>Aktif</option><option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option></select>
                </div>
                <div class="col-6 col-md-2 spl-filter-actions"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button><a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table spl-table">
                <thead><tr><th>Pengguna</th><th>Peran</th><th>Status</th><th>Dibuat</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        @php($role = $user->roles->first())
                        <tr>
                            <td><span class="spl-row-title">{{ $user->name }}</span><span class="spl-row-meta">{{ $user->email }}</span></td>
                            <td><span class="badge {{ $role?->code === 'admin' ? 'bg-primary' : 'bg-secondary' }}">{{ $role?->name ?? 'Belum memiliki peran' }}</span></td>
                            <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>{{ $user->created_at?->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('users.edit', $user) }}" class="spl-icon-action" title="Ubah pengguna" aria-label="Ubah {{ $user->name }}"><i class="bi bi-pencil-square"></i></a>
                                    @if(! $user->is(auth()->user()))
                                        <form action="{{ route('users.status', $user) }}" method="POST" onsubmit="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="spl-icon-action {{ $user->is_active ? 'text-warning' : 'text-success' }}" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun" aria-label="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $user->name }}"><i class="bi {{ $user->is_active ? 'bi-person-dash-fill' : 'bi-person-check-fill' }}"></i></button>
                                        </form>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun ini? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="spl-icon-action text-danger" title="Hapus pengguna" aria-label="Hapus {{ $user->name }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="spl-empty"><i class="bi bi-people"></i>Data pengguna tidak ditemukan. Ubah filter atau tambahkan pengguna baru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
