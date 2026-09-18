@extends('layouts.app')

@section('title', 'Periode Survei')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Periode</span></nav>
            <h3>Periode Survei</h3>
            <p>Kelola rentang waktu yang dapat dipilih saat membuat survei.</p>
        </div>
        <a href="{{ route('periode.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah periode</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
    @endif

    <section class="card">
        <div class="card-header spl-toolbar">
            <div><h4 class="spl-toolbar-title">Daftar periode <span class="spl-filter-count">{{ $periodes->total() }} hasil</span></h4><p class="spl-toolbar-subtitle">Survei hanya dapat diisi pada rentang tanggal periode yang dipilih.</p></div>
        </div>
        <div class="table-responsive">
            <table class="table spl-table"><thead><tr><th>Kode</th><th>Nama periode</th><th>Tanggal mulai</th><th>Tanggal berakhir</th><th>Survei</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                @forelse($periodes as $periode)
                    <tr>
                        <td><code class="text-dark">{{ $periode->kode_periode }}</code></td>
                        <td><span class="spl-row-title">{{ $periode->nama_periode }}</span></td>
                        <td>{{ $periode->tanggal_mulai->translatedFormat('d M Y') }}</td>
                        <td>{{ $periode->tanggal_berakhir->translatedFormat('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $periode->surveys_count }}</span></td>
                        <td><div class="d-flex justify-content-center gap-1"><a href="{{ route('periode.edit', $periode) }}" class="spl-icon-action" title="Ubah periode"><i class="bi bi-pencil-square"></i></a><form action="{{ route('periode.destroy', $periode) }}" method="POST" onsubmit="return confirm('Hapus periode ini?');">@csrf @method('DELETE')<button class="spl-icon-action text-danger" title="Hapus periode" {{ $periode->surveys_count ? 'disabled' : '' }}><i class="bi bi-trash"></i></button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="spl-empty"><i class="bi bi-calendar-x"></i>Belum ada periode. Tambahkan periode sebelum membuat survei.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($periodes->hasPages())<div class="spl-pagination">{{ $periodes->links() }}</div>@endif
    </section>
</div>
@endsection
