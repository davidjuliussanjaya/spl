@extends('layouts.app')

@section('title', 'Ubah Pengguna Sistem')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('users.index') }}">Pengguna Sistem</a><span>/</span><span>Ubah</span></nav>
            <h3>Ubah Pengguna</h3>
            <p>Perbarui identitas, kata sandi, peran, atau status akses akun.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.users.partials.form', ['submitLabel' => 'Simpan perubahan'])
    </form>
</div>
@endsection
