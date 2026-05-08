@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4 pb-3 border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h3 class="fw-bold mb-1 text-dark">Tambah Kategori</h3>
                    <p class="text-muted mb-0 small">Tambahkan kategori baru untuk pertanyaan survey.</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card shadow-sm border-0">
                <div class="card-body pt-4">
                    <form action="{{ route('kategori.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" required placeholder="Contoh: Fasilitas Akademik">
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Opsional, berikan penjelasan singkat tentang kategori ini">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('kategori.index') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
