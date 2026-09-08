@extends('layouts.app')

@section('title', 'Perusahaan')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Perusahaan</span></nav>
            <h3>Data Perusahaan</h3>
            <p>Kelola instansi dan penyelia yang menjadi responden survei lulusan.</p>
        </div>
        <a href="{{ route('create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah perusahaan</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif

    <section class="card">
        <div class="card-header spl-toolbar">
            <div><h4 class="spl-toolbar-title">Daftar perusahaan <span class="spl-filter-count">{{ $pengguna->count() }} hasil</span></h4><p class="spl-toolbar-subtitle">Cari perusahaan, penyelia, atau alamat email responden.</p></div>
        </div>
        <form action="{{ route('penggunalulusan') }}" method="GET" class="spl-filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-7">
                    <label for="cari" class="form-label">Cari perusahaan</label>
                    <div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span><input id="cari" type="search" name="cari" class="form-control border-start-0" value="{{ request('cari') }}" placeholder="Nama perusahaan, penyelia, atau email"></div>
                </div>
                <div class="col-6 col-md-3">
                    <label for="jenis" class="form-label">Jenis perusahaan</label>
                    <select id="jenis" name="jenis" class="form-select"><option value="">Semua jenis</option>@foreach($jenisList as $jenis)<option value="{{ $jenis }}" @selected(request('jenis') === $jenis)>{{ $jenis }}</option>@endforeach</select>
                </div>
                <div class="col-6 col-md-2 spl-filter-actions"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button><a href="{{ route('penggunalulusan') }}" class="btn btn-outline-secondary">Reset</a></div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table spl-table" id="table1">
                <thead><tr><th>Perusahaan</th><th>Penyelia</th><th>Kontak</th><th>Jenis</th><th>Lulusan</th><th>Cakupan</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($pengguna as $item)
                        <tr>
                            <td><span class="spl-row-title">{{ $item->nama_perusahaan }}</span><span class="spl-row-meta">{{ Str::limit($item->alamat_perusahaan, 48) }}</span></td>
                            <td><span class="spl-row-title">{{ $item->nama_penyelia }}</span>@if($item->jabatan_penyelia)<span class="spl-row-meta">{{ $item->jabatan_penyelia }}</span>@endif</td>
                            <td><span>{{ $item->email_penyelia }}</span><span class="spl-row-meta">{{ $item->kontak_penyelia ?? 'Kontak belum tersedia' }}</span></td>
                            <td><span class="badge bg-primary">{{ $item->jenis_perusahaan ?? 'Tidak diketahui' }}</span></td>
                            <td class="text-center"><span class="spl-row-title">{{ $item->lulusans_count }}</span></td>
                            <td>@if($item->cabang_negara)<span class="badge bg-success">Internasional</span>@elseif($item->cabang_kota)<span class="badge bg-primary">Nasional</span>@else<span class="badge bg-secondary">Lokal</span>@endif</td>
                            <td><div class="d-flex justify-content-center gap-1"><a href="{{ route('penggunalulusan.edit', $item->id) }}" class="spl-icon-action" title="Ubah perusahaan" aria-label="Ubah {{ $item->nama_perusahaan }}"><i class="bi bi-pencil-square"></i></a><form action="{{ route('penggunalulusan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data perusahaan ini? Tindakan ini tidak dapat dibatalkan.');">@csrf @method('DELETE')<button type="submit" class="spl-icon-action text-danger" title="Hapus perusahaan" aria-label="Hapus {{ $item->nama_perusahaan }}"><i class="bi bi-trash"></i></button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="spl-empty"><i class="bi bi-buildings"></i>Data perusahaan tidak ditemukan. Ubah filter atau tambahkan perusahaan baru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
