@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>DataTable</h3>
                            <p class="text-subtitle text-muted">For user to check they list</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Lulusan</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Index</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                            <h5 class="mb-0">Data Lulusan Universitas Dinamika</h5>

                            <a href="{{ route('addsurvey') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Add Survey
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nama Lulusan:</label>
                                    <input type="text" class="form-control" placeholder="Ex: Nashir Jamali">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NIM Lulusan:</label>
                                    <input type="text" class="form-control" placeholder="Ex: 1741001088">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Program Studi:</label>
                                    <select class="form-select">
                                        <option selected>Select</option>
                                        <option>Sistem Informasi</option>
                                        <option>Teknik Informatika</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tahun Lulus:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Dari">
                                        <span class="input-group-text">...</span>
                                        <input type="text" class="form-control" placeholder="Sampai">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status:</label>
                                    <select class="form-select">
                                        <option selected>Select</option>
                                        <option>Bekerja</option>
                                        <option>Belum Bekerja</option>
                                    </select>
                                </div>
                            </div>
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Nim</th>
                                        <th>Prodi</th>
                                        <th>Tahun Lulus</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
@endsection