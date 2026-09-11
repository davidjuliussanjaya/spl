@extends('layouts.app')

@section('title', 'Tambah Pengguna Sistem')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><a href="{{ route('users.index') }}">Pengguna Sistem</a><span>/</span><span>Tambah</span></nav>
            <h3>Tambah Pengguna</h3>
            <p>Buat akun dan tentukan peran aksesnya.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        @include('admin.users.partials.form', ['submitLabel' => 'Simpan pengguna'])
    </form>
</div>
@endsection
