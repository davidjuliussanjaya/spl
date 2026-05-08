@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4 pb-3 border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

                <div class="mb-3 mb-md-0">
                    <h3 class="fw-bold mb-1 text-dark">Data Kategori</h3>
                    <p class="text-muted mb-0 small">Kelola daftar kategori untuk pertanyaan survey.</p>
                </div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm border">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none text-primary">
                                <i class="bi bi-folder2-open me-1"></i> Survey
                            </a>
                        </li>
                        <li class="breadcrumb-item active fw-semibold text-secondary" aria-current="page">Kategori</li>
                    </ol>
                </nav>

            </div>
        </div>

        <section class="section">
            <div class="card shadow-sm border-0">

                <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 bg-white pt-4">
                    <h5 class="mb-0 fw-bold">Data Kategori</h5>

                    <a href="{{ route('kategori.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="dripicons-plus me-1"></i> Tambah Kategori
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="table1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama Kategori</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kategoris as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->nama_kategori }}</td>
                                        <td>{{ $item->deskripsi ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('kategori.edit', $item->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="dripicons-pencil"></i>
                                                </a>

                                                <form action="{{ route('kategori.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua pertanyaan dengan kategori ini akan direset.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="dripicons-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Data kategori belum tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
