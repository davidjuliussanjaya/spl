@extends('layouts.app')

@section('title', 'Daftar Pengguna Lulusan')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4 pb-3 border-bottom">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

            <div class="mb-3 mb-md-0">
                <h3 class="fw-bold mb-1 text-dark">Pengguna Lulusan</h3>
                <p class="text-muted mb-0 small">Daftar perusahaan dan instansi penyerap lulusan.</p>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm border">
                    <li class="breadcrumb-item">
                        <a href="#" class="text-decoration-none text-primary">
                            <i class="bi bi-folder2-open me-1"></i> Survey
                        </a>
                    </li>
                    <li class="breadcrumb-item active fw-semibold text-secondary" aria-current="page">Pengguna Lulusan</li>
                </ol>
            </nav>

        </div>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Instansi / Perusahaan</h5>
                <a href="{{ route('create') }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Perusahaan</th>
                                <th>Penyelia (Atasan)</th>
                                <th>Kontak/Email</th>
                                <th>Jenis</th>
                                <th>Jumlah Lulusan</th>
                                <th>Cakupan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengguna as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">{{ $item->nama_perusahaan }}</span><br>
                                    <small class="text-muted">{{ Str::limit($item->alamat_perusahaan, 40) }}</small>
                                </td>
                                <td>
                                    {{ $item->nama_penyelia }}
                                    @if($item->jabatan_penyelia)
                                    <br><small class="text-muted">{{ $item->jabatan_penyelia }}</small>
                                    @endif
                                </td>
                                <td>
                                    <i class="bi bi-envelope small"></i> {{ $item->email_penyelia }}<br>
                                    <i class="bi bi-telephone small"></i> {{ $item->kontak_penyelia ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $item->jenis_perusahaan ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $jumlahTampil = $item->jumlah_lulusan ?? $item->lulusans_count;
                                    @endphp
                                    <span class="fw-bold text-primary">{{ $jumlahTampil }}</span>
                                    @if($item->jumlah_lulusan && $item->jumlah_lulusan != $item->lulusans_count)
                                    <br><small class="text-muted">(sistem: {{ $item->lulusans_count }})</small>
                                    @endif
                                </td>
                                <td>
                                    @if($item->cabang_negara)
                                    <span class="badge bg-light-info text-info">Internasional</span>
                                    @elseif($item->cabang_kota)
                                    <span class="badge bg-light-primary text-primary">Nasional</span>
                                    @else
                                    <span class="badge bg-light-secondary text-secondary">Lokal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('penggunalulusan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('penggunalulusan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data instansi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection