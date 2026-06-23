@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="page-heading">

    {{-- Page Header --}}
    <div class="page-title mb-4 pb-3" style="border-bottom:1px solid #f0e0e4;">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="fw-bold mb-1">Profil Saya</h3>
                <p class="text-muted mb-0 small">Kelola informasi akun dan keamanan Anda</p>
            </div>
            <div class="col-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Kolom Kiri: Avatar & Info Singkat --}}
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="d-flex align-items-center justify-content-center mx-auto mb-3 text-white fw-bold"
                     style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#8B1A2A,#B91C3A);font-size:1.6rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <h6 class="fw-bold mb-1">{{ Auth::user()->name }}</h6>
                <p class="text-muted small mb-1">{{ Auth::user()->email }}</p>
                <span class="badge text-white px-3 py-1" style="background:#8B1A2A;font-size:0.7rem;border-radius:50px;">
                    {{ Auth::user()->roles->first()->name ?? 'User' }}
                </span>
            </div>
        </div>

        {{-- Kolom Kanan: Form --}}
        <div class="col-12 col-lg-9">
            <div class="d-flex flex-column gap-4">

                {{-- ── Update Info Profil ── --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 border-0 pb-0">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center text-white rounded"
                                 style="width:32px;height:32px;background:#8B1A2A;border-radius:8px !important;">
                                <i class="bi bi-person-fill" style="font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Informasi Profil</h6>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">Perbarui nama dan alamat email akun Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">

                        @if(session('status') === 'profile-updated')
                            <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background:#FDE8EC;color:#8B1A2A;border-radius:8px;">
                                <i class="bi bi-check-circle-fill"></i>
                                <span class="fw-semibold small">Profil berhasil diperbarui.</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label fw-semibold small">Nama Lengkap</label>
                                    <input id="name" type="text" name="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="form-control @error('name') is-invalid @enderror"
                                           required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label fw-semibold small">Alamat Email</label>
                                    <input id="email" type="email" name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="form-control @error('email') is-invalid @enderror"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn text-white px-4" style="background:#8B1A2A;border-color:#8B1A2A;" onmouseover="this.style.background='#6C0215'" onmouseout="this.style.background='#8B1A2A'">
                                    <i class="bi bi-check2 me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Ubah Password ── --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 border-0 pb-0">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center text-white rounded"
                                 style="width:32px;height:32px;background:#C9A227;border-radius:8px !important;">
                                <i class="bi bi-shield-lock-fill" style="font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Ubah Password</h6>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">Gunakan password yang kuat dan unik untuk keamanan akun.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">

                        @if(session('status') === 'password-updated')
                            <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background:#FDE8EC;color:#8B1A2A;border-radius:8px;">
                                <i class="bi bi-check-circle-fill"></i>
                                <span class="fw-semibold small">Password berhasil diperbarui.</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="current_password" class="form-label fw-semibold small">Password Saat Ini</label>
                                    <input id="current_password" type="password" name="current_password"
                                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                           autocomplete="current-password">
                                    @error('current_password', 'updatePassword')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="password" class="form-label fw-semibold small">Password Baru</label>
                                    <input id="password" type="password" name="password"
                                           class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                           autocomplete="new-password">
                                    @error('password', 'updatePassword')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="password_confirmation" class="form-label fw-semibold small">Konfirmasi Password</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                           class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                           autocomplete="new-password">
                                    @error('password_confirmation', 'updatePassword')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn text-white px-4" style="background:#8B1A2A;border-color:#8B1A2A;" onmouseover="this.style.background='#6C0215'" onmouseout="this.style.background='#8B1A2A'">
                                    <i class="bi bi-lock-fill me-1"></i> Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Hapus Akun ── --}}
                <div class="card border-0 shadow-sm" style="border-left:3px solid #dc2626 !important;">
                    <div class="card-header bg-white pt-4 border-0 pb-0">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center text-white rounded"
                                 style="width:32px;height:32px;background:#dc2626;border-radius:8px !important;">
                                <i class="bi bi-trash3-fill" style="font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-danger">Hapus Akun</h6>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <p class="text-muted small mb-3">
                            Setelah akun dihapus, semua data terkait akan dihapus secara permanen.
                            Pastikan Anda telah mengunduh semua data yang diperlukan sebelum melanjutkan.
                        </p>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash3 me-1"></i> Hapus Akun Saya
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Akun --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded"
                         style="width:36px;height:36px;border-radius:10px !important;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0">Konfirmasi Hapus Akun</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Apakah Anda yakin ingin menghapus akun ini? Semua data akan hilang secara permanen.
                    Masukkan password Anda untuk konfirmasi.
                </p>
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <label for="delete_password" class="form-label fw-semibold small">Password</label>
                    <input id="delete_password" type="password" name="password"
                           class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                           placeholder="Masukkan password Anda">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Ya, Hapus Akun
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
