@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Aspek Evaluasi</span></nav>
            <h3>Aspek Evaluasi</h3>
            <p>Kelola kategori yang digunakan untuk mengelompokkan pertanyaan survei.</p>
        </div>
        <a href="{{ route('kategori.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah kategori</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert alert-dismissible fade show" role="status">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <section class="card">
        <div class="card-header spl-toolbar">
            <div>
                <h4 class="spl-toolbar-title">Daftar kategori <span class="spl-filter-count">{{ $kategoris->total() }} hasil</span></h4>
                <p class="spl-toolbar-subtitle">Kategori membantu membaca hasil kepuasan per aspek secara lebih terarah.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table spl-table" id="table1">
                <thead>
                    <tr><th class="text-center" style="width:72px;">No.</th><th>Nama kategori</th><th>Deskripsi</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $index => $item)
                        <tr>
                            <td class="text-center text-muted">{{ $kategoris->firstItem() + $index }}</td>
                            <td><span class="spl-row-title">{{ $item->nama_kategori }}</span></td>
                            <td>{{ $item->deskripsi ?: '-' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('kategori.edit', $item->id) }}" class="spl-icon-action" title="Ubah kategori" aria-label="Ubah kategori {{ $item->nama_kategori }}"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('kategori.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua pertanyaan dengan kategori ini akan direset.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="spl-icon-action text-danger" title="Hapus kategori" aria-label="Hapus kategori {{ $item->nama_kategori }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="spl-empty"><i class="bi bi-tags"></i>Belum ada kategori. Tambahkan kategori untuk mulai mengelompokkan pertanyaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kategoris->hasPages())
            <div class="spl-pagination">
                <span>Menampilkan {{ $kategoris->firstItem() }}–{{ $kategoris->lastItem() }} dari {{ $kategoris->total() }} data</span>
                {{ $kategoris->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
