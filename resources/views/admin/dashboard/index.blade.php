@extends('layouts.app')

@section('title', 'Dashboard Eksekutif')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4 pb-3 border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Dashboard Evaluasi Lulusan</h3>
                    <p class="text-muted mb-0 small">Ringkasan performa dan kualitas lulusan Universitas Dinamika di dunia
                        kerja.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-12 d-flex justify-content-center mb-3 mb-md-0">
                                <div class="stats-icon purple mb-0">
                                    <i class="bi bi-buildings-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-12 text-center text-md-start">
                                <h6 class="text-muted font-semibold">Total Responden</h6>
                                <h4 class="font-extrabold mb-0 text-dark">{{ $totalSurvey ?? 0 }} <span
                                        class="fs-6 fw-normal text-muted">Instansi</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-12 d-flex justify-content-center mb-3 mb-md-0">
                                <div class="stats-icon blue mb-0">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-12 text-center text-md-start">
                                <h6 class="text-muted font-semibold">Lulusan Dinilai</h6>
                                <h4 class="font-extrabold mb-0 text-dark">{{ $totalLulusan ?? 0 }} <span
                                        class="fs-6 fw-normal text-muted">Orang</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-12 d-flex justify-content-center mb-3 mb-md-0">
                                <div class="stats-icon green mb-0">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-12 text-center text-md-start">
                                <h6 class="text-muted font-semibold">Indeks Kepuasan</h6>
                                <h4 class="font-extrabold mb-0 text-success">{{ number_format($rataKeseluruhan ?? 0, 2) }}
                                    <span class="fs-6 fw-normal text-muted">/ 4.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-12 d-flex justify-content-center mb-3 mb-md-0">
                                <div class="stats-icon red mb-0">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-12 text-center text-md-start">
                                <h6 class="text-muted font-semibold text-truncate">Kategori Terbaik</h6>
                                <h6 class="font-bold mb-0 text-dark text-truncate"
                                    title="{{ $kategoriTerbaik->kategori ?? '-' }}">{{ $kategoriTerbaik->kategori ?? '-' }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row">
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-4 pb-2 border-0">
                        <h5 class="fw-bold">Rata-Rata Penilaian Kinerja Lulusan oleh Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-kinerja-horizontal"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4 mt-4 mt-xl-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-4 pb-2 border-0">
                        <h5 class="fw-bold">Fokus Evaluasi</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-arrow-up-circle-fill fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold text-success">Kekuatan Lulusan</h6>
                                    <p class="mb-0 small text-dark">Lulusan sangat unggul pada aspek
                                        <strong>{{ $kategoriTerbaik->kategori ?? '-' }}</strong> dengan skor
                                        {{ number_format($kategoriTerbaik->rata_rata ?? 0, 2) }}.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-danger border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-circle-fill fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold text-danger">Area Peningkatan</h6>
                                    <p class="mb-0 small text-dark">Aspek
                                        <strong>{{ $kategoriTerlemah->kategori ?? '-' }}</strong> perlu perbaikan, dengan
                                        skor terendah {{ number_format($kategoriTerlemah->rata_rata ?? 0, 2) }}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 border-bottom">
                        <h5 class="fw-bold mb-0">Daftar Lulusan</h5>
                        <a href="{{ route('lulusan') }}" class="btn btn-sm btn-primary rounded-pill px-3">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Nama Lulusan</th>
                                        <th>NIM</th>
                                        <th>Program Studi</th>
                                        <th>Tahun Lulus</th>
                                        <th class="pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($daftarLulusan as $index => $l)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td class="fw-bold text-dark">{{ $l->nama }}</td>
                                            <td>{{ $l->nim }}</td>
                                            <td>{{ $l->program_studi }}</td>
                                            <td>{{ $l->tahun_lulus ? $l->tahun_lulus->format('Y') : '-' }}</td>
                                            <td class="pe-4">
                                                @if($l->status)
                                                    <span class="badge bg-success">Bekerja</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum Bekerja</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data lulusan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Umpan Balik Kualitatif Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Instansi / Responden</th>
                                        <th>Aspek Pertanyaan</th>
                                        <th class="pe-4">Komentar / Umpan Balik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($komentarTerbaru as $komen)
                                        <tr>
                                            <td class="ps-4">
                                                <p class="font-bold mb-0 text-dark">
                                                    {{ $komen->survey->pengguna_lulusan->nama_perusahaan ?? 'Anonim' }}</p>
                                                <small class="text-muted">{{ $komen->responden ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border text-wrap text-start"
                                                    style="max-width: 250px;">{{ $komen->soal->soal ?? 'Essay' }}</span>
                                            </td>
                                            <td class="pe-4 text-dark fst-italic">
                                                "{{ Str::limit($komen->jawaban_text, 150) }}"
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada umpan balik kualitatif
                                                dari instansi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var options = {
                series: [{
                    name: 'Rata-rata Skor',
                    data: @json($chartData)
                }],
                chart: {
                    type: 'bar',
                    height: 450, // Ditinggikan agar batang horizontal tidak bertumpuk
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true, // Mengubah menjadi horizontal
                        barHeight: '60%',
                    }
                },
                colors: ['#435ebe'],
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: { colors: ['#fff'] },
                    formatter: function (val) { return val },
                    offsetX: 0,
                },
                xaxis: {
                    categories: @json($chartLabels),
                    max: 4, // Sesuai dengan skala maksimal nilai yaitu 4
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 500,
                        }
                    }
                },
                grid: {
                    xaxis: { lines: { show: true } }
                },
                tooltip: {
                    theme: 'light',
                    y: { title: { formatter: function () { return 'Skor: ' } } }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart-kinerja-horizontal"), options);
            chart.render();
        });
    </script>
@endsection