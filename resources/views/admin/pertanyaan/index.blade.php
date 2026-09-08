@extends('layouts.app')

@section('title', 'Pertanyaan')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Pertanyaan</span></nav>
            <h3>Instrumen Survei</h3>
            <p>Atur pertanyaan yang digunakan untuk mengevaluasi lulusan.</p>
        </div>
        <a href="{{ route('addquestion') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat pertanyaan</a>
    </div>

    @if(session('success'))<div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>@endif

    <section class="card">
        <div class="card-header spl-toolbar"><div><h4 class="spl-toolbar-title">Daftar pertanyaan <span class="spl-filter-count">{{ $soal->count() }} hasil</span></h4><p class="spl-toolbar-subtitle">Pastikan status dan aspek evaluasi setiap pertanyaan sudah tepat.</p></div></div>
        <form action="{{ route('pertanyaan') }}" method="GET" class="spl-filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6"><label for="cari" class="form-label">Cari pertanyaan</label><div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span><input id="cari" type="search" name="cari" class="form-control border-start-0" value="{{ request('cari') }}" placeholder="Masukkan kata dalam pertanyaan"></div></div>
                <div class="col-6 col-md-2"><label for="jenis" class="form-label">Tipe</label><select id="jenis" name="jenis" class="form-select"><option value="">Semua tipe</option><option value="multiple_choice" @selected(request('jenis') === 'multiple_choice')>Pilihan</option><option value="essay" @selected(request('jenis') === 'essay')>Uraian</option><option value="rating" @selected(request('jenis') === 'rating')>Rating</option></select></div>
                <div class="col-6 col-md-2"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select"><option value="">Semua status</option><option value="aktif" @selected(request('status') === 'aktif')>Aktif</option><option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option></select></div>
                <div class="col-12 col-md-2 spl-filter-actions"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button><a href="{{ route('pertanyaan') }}" class="btn btn-outline-secondary">Reset</a></div>
            </div>
        </form>
        <div class="table-responsive"><table class="table spl-table" id="table1"><thead><tr><th>Pertanyaan</th><th>Aspek</th><th>Fakultas</th><th>Tipe</th><th>Wajib</th><th>Status</th><th class="text-center">Aksi</th></tr></thead><tbody>
            @forelse($soal as $item)
                <tr>
                    <td><span class="spl-row-title">{{ $item->soal }}</span><span class="spl-row-meta">Kode: {{ $item->kode ?? '-' }}</span></td>
                    <td><span class="badge bg-primary">{{ $item->kategori->nama_kategori ?? 'Tanpa aspek' }}</span></td>
                    <td>{{ $item->peruntukan_fakultas }}</td>
                    <td><span class="badge bg-secondary">{{ $item->jenis_soal === 'multiple_choice' ? 'Pilihan' : ucfirst($item->jenis_soal) }}</span></td>
                    <td>@if($item->is_required)<span class="badge bg-success">Wajib</span>@else<span class="badge bg-secondary">Opsional</span>@endif</td>
                    <td>@if($item->is_active)<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>@else<span class="badge bg-danger"><i class="bi bi-pause-circle me-1"></i>Nonaktif</span>@endif</td>
                    <td><div class="d-flex justify-content-center gap-1"><a href="{{ route('pertanyaan.edit', $item->id) }}" class="spl-icon-action" title="Ubah pertanyaan" aria-label="Ubah pertanyaan"><i class="bi bi-pencil-square"></i></a><form action="{{ route('pertanyaan.switch', $item->id) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="spl-icon-action {{ $item->is_active ? 'text-danger' : 'text-success' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pertanyaan" aria-label="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pertanyaan"><i class="bi {{ $item->is_active ? 'bi-pause' : 'bi-play' }}"></i></button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="spl-empty"><i class="bi bi-ui-checks-grid"></i>Belum ada pertanyaan yang sesuai dengan filter.</td></tr>
            @endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
