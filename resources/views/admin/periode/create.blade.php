@extends('layouts.app')

@section('title', 'Tambah Periode')

@section('content')
<div class="page-heading"><div class="page-title mb-4"><h3 class="mb-1">Tambah Periode</h3><p class="text-muted mb-0">Tentukan identitas dan masa pengisian survei.</p></div><section class="section"><div class="card shadow-sm border-0"><div class="card-body p-4"><form action="{{ route('periode.store') }}" method="POST" data-draft-key="spl:draft:period-create">@csrf @include('admin.periode._form')<div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top"><a href="{{ route('periode.index') }}" class="btn btn-light border">Batal</a><button class="btn btn-primary">Simpan</button></div></form></div></div></section></div>
@endsection
