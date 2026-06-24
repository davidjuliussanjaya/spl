@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        --brand-50: #fde8ec;
        --brand-100: #fbbcca;
        --brand-500: #8b1a2a;
        --brand-700: #6c0215;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-700: #334155;
        --slate-900: #0f172a;
        --radius: 12px;
        --shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,.07);
    }

    .db-wrap { display: flex; flex-direction: column; gap: 1rem; }
    .db-header { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: .75rem; }
    .db-header h3 { font-size: 1.25rem; font-weight: 700; color: var(--slate-900); margin: 0; }
    .db-header p { font-size: .82rem; color: var(--slate-400); margin: .2rem 0 0; }

    .filter-bar {
        background: #fff; border: 1px solid var(--slate-200);
        border-radius: var(--radius); padding: .75rem 1rem; box-shadow: var(--shadow);
    }
    .filter-bar .form-label {
        font-size: .7rem; font-weight: 700; color: var(--slate-500);
        text-transform: uppercase; letter-spacing: .5px; margin-bottom: .25rem;
    }
    .filter-bar .form-select,
    .filter-bar .periode-toggle {
        font-size: .82rem; border-color: var(--slate-200); border-radius: 8px; color: var(--slate-700);
    }
    .filter-bar .form-select:focus,
    .filter-bar .periode-toggle:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(139,26,42,.12); }
    .periode-dropdown { width: 100%; }
    .periode-toggle {
        width: 100%; min-height: 31px; background: #fff; border: 1px solid var(--slate-200);
        display: flex; align-items: center; justify-content: space-between; gap: .5rem;
        padding: .25rem .5rem; text-align: left;
    }
    .periode-toggle span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .periode-menu {
        width: 100%; max-height: 220px; overflow-y: auto; padding: .45rem;
        border: 1px solid var(--slate-200); border-radius: 8px; box-shadow: var(--shadow-md);
    }
    .periode-option {
        display: flex; align-items: center; gap: .45rem; padding: .32rem .4rem;
        border-radius: 6px; font-size: .82rem; color: var(--slate-700); cursor: pointer;
    }
    .periode-option:hover { background: var(--slate-50); }
    .periode-option input { accent-color: var(--brand-500); }
    .btn-apply {
        font-size: .8rem; font-weight: 600; padding: .42rem 1rem;
        background: var(--brand-500); color: #fff; border: none; border-radius: 8px;
    }
    .btn-apply:hover { background: var(--brand-700); color: #fff; }
    .btn-reset { font-size: .8rem; padding: .42rem .85rem; border-radius: 8px; }
    .btn-extend {
        border: 1px solid var(--slate-200); background: #fff; color: var(--slate-700);
        border-radius: 8px; font-size: .75rem; font-weight: 600; padding: .32rem .65rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }
    .btn-extend:hover { border-color: var(--brand-100); color: var(--brand-700); background: var(--brand-50); }
    .chip {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .72rem; font-weight: 500; padding: .2rem .6rem;
        border-radius: 50px; background: var(--brand-50);
        color: var(--brand-700); border: 1px solid var(--brand-100);
    }

    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .85rem; }
    @media(max-width:1199px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:575px) { .stat-grid { grid-template-columns: 1fr; gap: .7rem; } }

    .panel, .stat-card {
        background: #fff; border: 1px solid var(--slate-200);
        border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;
    }
    .panel:hover, .stat-card:hover { box-shadow: var(--shadow-md); }
    .stat-card {
        padding: 1rem 1.1rem; position: relative; transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--brand-500);
    }
    .stat-card.green::before { background: #16a34a; }
    .stat-card.amber::before { background: #d97706; }
    .stat-card.red::before { background: #dc2626; }
    .stat-top { display: flex; justify-content: space-between; align-items: flex-start; gap: .75rem; margin-bottom: .7rem; }
    .stat-label {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .55px; color: var(--slate-500);
    }
    .stat-icon-wrap {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; background: var(--brand-50); color: var(--brand-500); flex-shrink: 0;
    }
    .stat-card.green .stat-icon-wrap { background: #f0fdf4; color: #16a34a; }
    .stat-card.amber .stat-icon-wrap { background: #fffbeb; color: #d97706; }
    .stat-card.red .stat-icon-wrap { background: #fef2f2; color: #dc2626; }
    .stat-value { font-size: 1.65rem; font-weight: 800; color: var(--slate-900); line-height: 1; }
    .stat-unit { font-size: .76rem; font-weight: 400; color: var(--slate-500); margin-left: .25rem; }
    .stat-name { font-size: .88rem; font-weight: 700; color: var(--slate-900); line-height: 1.25; margin-bottom: .22rem; }
    .stat-sub { font-size: .72rem; color: var(--slate-500); margin-top: .35rem; line-height: 1.35; }
    .stat-bar-track { height: 4px; background: var(--slate-100); border-radius: 99px; margin-top: .55rem; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 99px; background: #16a34a; }

    .chart-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; align-items: start; }
    @media(max-width:991px) { .chart-grid { grid-template-columns: 1fr; } }

    .panel-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: .75rem 1rem; border-bottom: 1px solid var(--slate-100); background: #fff;
        gap: .75rem; flex-wrap: wrap;
    }
    .panel-title { font-size: .86rem; font-weight: 700; color: var(--slate-700); margin: 0; }
    .panel-subtitle { font-size: .72rem; color: var(--slate-500); margin: .08rem 0 0; }
    .panel-body { padding: .75rem 1rem .9rem; }
    .chart-wrap { min-height: 0; }
    .chart-compact { height: 240px; }
    .chart-modal { min-height: 420px; }

    .feedback-list-main .fb-item:nth-child(n+4) { display: none; }
    .fb-item { padding: .75rem 1rem; border-bottom: 1px solid var(--slate-100); }
    .fb-item:last-child { border-bottom: none; }
    .fb-quote {
        font-size: .8rem; color: var(--slate-700); line-height: 1.5;
        font-style: italic; padding: .5rem .75rem;
        background: var(--brand-50); border-left: 3px solid var(--brand-500);
        border-radius: 0 8px 8px 0; margin-bottom: .45rem;
    }
    .fb-meta { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .35rem; }
    .fb-who { font-size: .74rem; }
    .fb-who strong { color: var(--slate-700); font-weight: 600; }
    .fb-who span { color: var(--slate-500); }
    .fb-tag {
        font-size: .68rem; font-weight: 500; padding: .16rem .55rem;
        background: var(--brand-50); color: var(--brand-700);
        border: 1px solid var(--brand-100); border-radius: 99px;
    }
    .feedback-modal-body { max-height: 70vh; overflow-y: auto; padding: 0; }

    .satisfaction-panel, .satisfaction-panel * { color: #111 !important; }
    .satisfaction-panel .panel-header { border: 1px solid #d1d5db; border-width: 0 0 1px 0; background: #fff; }
    .tbl-kepuasan { width: 100%; border-collapse: collapse; font-size: .81rem; }
    .tbl-kepuasan thead th {
        padding: .56rem .9rem; font-size: .7rem; font-weight: 700;
        text-align: center; white-space: nowrap; letter-spacing: .3px;
        border-bottom: 1px solid #111; background: #fff;
    }
    .tbl-kepuasan thead .th-name { text-align: left; width: 26%; }
    .tbl-kepuasan tbody td {
        padding: .52rem .9rem; border-bottom: 1px solid var(--slate-100);
        text-align: center; vertical-align: middle;
    }
    .tbl-kepuasan tbody td:first-child { text-align: left; font-weight: 500; }
    .tbl-kepuasan tbody tr:nth-child(even) td { background: var(--slate-50); }
    .tbl-kepuasan tbody tr:hover td { background: #f3f4f6; transition: background .1s; }
    .tbl-kepuasan tfoot td {
        padding: .56rem .9rem; font-weight: 700; text-align: center;
        font-size: .8rem; border-top: 1px solid #111;
    }
    .tbl-kepuasan tfoot td:first-child { text-align: left; }
    .empty-state { text-align: center; padding: 1.75rem 1rem; color: var(--slate-500); font-size: .84rem; }
    .empty-state i { display: block; font-size: 1.5rem; margin-bottom: .45rem; opacity: .45; }

    .modal-content { border: 0; border-radius: var(--radius); overflow: hidden; }
    .modal-header { border-bottom-color: var(--slate-100); padding: .85rem 1rem; }
    .modal-title { font-size: .95rem; font-weight: 700; color: var(--slate-900); }
</style>

@php
    $activePeriode = $filters['periode'] ?? [];
    $selectedFakultas = $filters['fakultas'] ?? '';
    $selectedProdi = $filters['program_studi'] ?? '';
    $fakultasProdi = $filterOptions['fakultasProdi'] ?? [];
    $fakultasLabels = $filterOptions['fakultasLabels'] ?? [];
    $availableProdi = $selectedFakultas && isset($fakultasProdi[$selectedFakultas])
        ? $fakultasProdi[$selectedFakultas]
        : $filterOptions['prodiList'];
    $hasFilters = !empty($activePeriode) || !empty($filters['fakultas']) || !empty($filters['program_studi']);
    $pct = min(100, round((($rataKeseluruhan ?? 0) / 4) * 100));
@endphp

<div class="db-wrap">
    <div class="db-header">
        <div>
            <h3>Dashboard Evaluasi Lulusan</h3>
            <p>Ringkasan performa dan kualitas lulusan Universitas Dinamika di dunia kerja.</p>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-auto d-flex align-items-center pe-3">
                    <span style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--slate-500);">
                        <i class="bi bi-sliders me-1" style="color:var(--brand-500);"></i>Filter
                    </span>
                </div>
                <div class="col-12 col-md-4 col-lg">
                    <label class="form-label">Periode</label>
                    <div class="dropdown periode-dropdown">
                        <button class="periode-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <span>
                                @if(empty($activePeriode))
                                    Semua Periode
                                @elseif(count($activePeriode) === 1)
                                    {{ $activePeriode[0] }}
                                @else
                                    {{ count($activePeriode) }} periode dipilih
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu periode-menu">
                            @foreach($filterOptions['periodeList'] as $periode)
                                <label class="periode-option">
                                    <input type="checkbox" name="periode[]" value="{{ $periode }}" {{ in_array($periode, $activePeriode, true) ? 'checked' : '' }}>
                                    <span>{{ $periode }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas" id="filterFakultas" class="form-select form-select-sm">
                        <option value="">Semua Fakultas</option>
                        @foreach($filterOptions['fakultasList'] as $fakultas)
                            <option value="{{ $fakultas }}" {{ $selectedFakultas == $fakultas ? 'selected' : '' }}>
                                {{ $fakultasLabels[$fakultas] ?? $fakultas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi" id="filterProdi" class="form-select form-select-sm">
                        <option value="">{{ $selectedFakultas ? 'Semua Prodi di Fakultas ini' : 'Semua Prodi' }}</option>
                        @foreach($availableProdi as $prodi)
                            <option value="{{ $prodi }}" {{ $selectedProdi == $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-auto d-flex gap-2">
                    <button type="submit" class="btn btn-apply">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('dashboard') }}" class="btn btn-reset btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>

            @if($hasFilters)
                <div class="d-flex flex-wrap gap-2 align-items-center mt-2 pt-2 border-top">
                    <span style="font-size:.72rem;color:var(--slate-500);">Aktif:</span>
                    @foreach($activePeriode as $periode)
                        <span class="chip"><i class="bi bi-calendar3"></i> {{ $periode }}</span>
                    @endforeach
                    @if(!empty($filters['fakultas']))
                        <span class="chip"><i class="bi bi-building"></i> {{ $fakultasLabels[$filters['fakultas']] ?? $filters['fakultas'] }}</span>
                    @endif
                    @if(!empty($filters['program_studi']))
                        <span class="chip"><i class="bi bi-book"></i> {{ $filters['program_studi'] }}</span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Lulusan Dinilai</span>
                <div class="stat-icon-wrap"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
            <span class="stat-value">{{ $totalLulusan ?? 0 }}</span>
            <span class="stat-unit">orang</span>
            <div class="stat-sub">{{ $totalSurvey ?? 0 }} respon survey terarsip</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <span class="stat-label">Indeks Kepuasan</span>
                <div class="stat-icon-wrap"><i class="bi bi-star-fill"></i></div>
            </div>
            <span class="stat-value">{{ number_format($rataKeseluruhan ?? 0, 2) }}</span>
            <span class="stat-unit">/ 4.00</span>
            <div class="stat-bar-track">
                <div class="stat-bar-fill" style="width:{{ $pct }}%;"></div>
            </div>
            <div class="stat-sub">{{ $pct }}% dari skor maksimal</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <span class="stat-label">Kategori Terbaik</span>
                <div class="stat-icon-wrap"><i class="bi bi-trophy-fill"></i></div>
            </div>
            <div class="stat-name">{{ $kategoriTerbaik->kategori ?? '-' }}</div>
            <div class="stat-sub">Skor {{ number_format($kategoriTerbaik->rata_rata ?? 0, 2) }} / 4.00</div>
        </div>

        <div class="stat-card red">
            <div class="stat-top">
                <span class="stat-label">Kategori Terendah</span>
                <div class="stat-icon-wrap"><i class="bi bi-arrow-down-right"></i></div>
            </div>
            <div class="stat-name">{{ $kategoriTerlemah->kategori ?? '-' }}</div>
            <div class="stat-sub">Skor {{ number_format($kategoriTerlemah->rata_rata ?? 0, 2) }} / 4.00</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h6 class="panel-title">Responden Berdasarkan Program Studi</h6>
                    <p class="panel-subtitle">Menampilkan data teratas agar panel tetap ringkas</p>
                </div>
                <button type="button" class="btn-extend" data-bs-toggle="modal" data-bs-target="#prodiChartModal">
                    <i class="bi bi-arrows-fullscreen"></i> Lihat Selengkapnya
                </button>
            </div>
            <div class="panel-body">
                <div id="chart-prodi" class="chart-wrap chart-compact"></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <h6 class="panel-title">Rata-Rata Penilaian per Kategori</h6>
                    <p class="panel-subtitle">Ringkasan kategori teratas pada tampilan utama</p>
                </div>
                <button type="button" class="btn-extend" data-bs-toggle="modal" data-bs-target="#kinerjaChartModal">
                    <i class="bi bi-arrows-fullscreen"></i> Lihat Selengkapnya
                </button>
            </div>
            <div class="panel-body">
                <div id="chart-kinerja" class="chart-wrap chart-compact"></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h6 class="panel-title">Umpan Balik Terbaru</h6>
                <p class="panel-subtitle">Tiga feedback terbaru ditampilkan di dashboard utama</p>
            </div>
            <button type="button" class="btn-extend" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                <i class="bi bi-chat-square-text"></i> Lihat Semua
            </button>
        </div>
        <div class="feedback-list-main">
            @forelse($komentarTerbaru->take(3) as $komen)
                @include('admin.dashboard.partials.feedback-item', ['komen' => $komen])
            @empty
                <div class="empty-state"><i class="bi bi-chat-dots"></i>Belum ada umpan balik.</div>
            @endforelse
        </div>
    </div>

    <div class="panel satisfaction-panel">
        <div class="panel-header">
            <div>
                <h6 class="panel-title">Tingkat Kepuasan Pengguna</h6>
                <p class="panel-subtitle">Persentase penilaian responden per kategori kompetensi lulusan</p>
            </div>
        </div>

        @if($kepuasanPerKategori->isEmpty())
            <div class="empty-state"><i class="bi bi-bar-chart"></i>Belum ada data penilaian.</div>
        @else
            <div style="overflow-x:auto;">
                <table class="tbl-kepuasan">
                    <thead>
                        <tr>
                            <th class="th-name">Jenis Kemampuan</th>
                            <th>Sangat Baik (4)</th>
                            <th>Baik (3)</th>
                            <th>Kurang (2)</th>
                            <th>Sangat Kurang (1)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kepuasanPerKategori as $kat)
                            <tr>
                                <td>{{ $kat['kategori'] }}</td>
                                <td>{{ $kat['pct_sb'] }}%</td>
                                <td>{{ $kat['pct_b'] }}%</td>
                                <td>{{ $kat['pct_k'] }}%</td>
                                <td>{{ $kat['pct_sk'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>{{ $kepuasanRingkasan['total']['sb'] }}%</td>
                            <td>{{ $kepuasanRingkasan['total']['b'] }}%</td>
                            <td>{{ $kepuasanRingkasan['total']['k'] }}%</td>
                            <td>{{ $kepuasanRingkasan['total']['sk'] }}%</td>
                        </tr>
                        <tr>
                            <td>Rata-Rata</td>
                            <td>{{ $kepuasanRingkasan['rata']['sb'] }}%</td>
                            <td>{{ $kepuasanRingkasan['rata']['b'] }}%</td>
                            <td>{{ $kepuasanRingkasan['rata']['k'] }}%</td>
                            <td>{{ $kepuasanRingkasan['rata']['sk'] }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="prodiChartModal" tabindex="-1" aria-labelledby="prodiChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prodiChartModalLabel">Responden Berdasarkan Program Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chart-prodi-full" class="chart-modal"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kinerjaChartModal" tabindex="-1" aria-labelledby="kinerjaChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kinerjaChartModalLabel">Rata-Rata Penilaian per Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chart-kinerja-full" class="chart-modal"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Seluruh Umpan Balik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body feedback-modal-body">
                @forelse($komentarTerbaru as $komen)
                    @include('admin.dashboard.partials.feedback-item', ['komen' => $komen])
                @empty
                    <div class="empty-state"><i class="bi bi-chat-dots"></i>Belum ada umpan balik.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script>
    const chartData = @json($chartData);
    const chartLabels = @json($chartLabels);
    const respondenProdiData = @json($respondenProdiData);
    const respondenProdiLabels = @json($respondenProdiLabels);
    const fakultasProdi = @json($fakultasProdi);
    const compactLimit = 6;

    const fakultasSelect = document.getElementById('filterFakultas');
    const prodiSelect = document.getElementById('filterProdi');

    const renderProdiOptions = () => {
        const selectedFakultas = fakultasSelect.value;
        const currentProdi = prodiSelect.value;
        const prodiList = selectedFakultas ? (fakultasProdi[selectedFakultas] || []) : Object.values(fakultasProdi).flat();
        const defaultLabel = selectedFakultas ? 'Semua Prodi di Fakultas ini' : 'Semua Prodi';

        prodiSelect.disabled = true;
        prodiSelect.innerHTML = '<option value="">Memuat prodi...</option>';

        window.setTimeout(() => {
            prodiSelect.innerHTML = '';
            prodiSelect.append(new Option(defaultLabel, ''));

            prodiList.forEach((prodi) => {
                prodiSelect.append(new Option(prodi, prodi));
            });

            prodiSelect.value = prodiList.includes(currentProdi) ? currentProdi : '';
            prodiSelect.disabled = false;
        }, 120);
    };

    fakultasSelect.addEventListener('change', renderProdiOptions);

    const sliceData = (labels, data, limit = compactLimit) => ({
        labels: labels.slice(0, limit),
        data: data.slice(0, limit)
    });

    const emptyChart = (selector, message) => {
        const target = document.querySelector(selector);
        if (target) target.innerHTML = `<div class="empty-state"><i class="bi bi-bar-chart"></i>${message}</div>`;
    };

    const chartHeight = (count, min = 220, row = 34, max = 420) => Math.min(max, Math.max(min, count * row + 46));

    const prodiOptions = (labels, data, expanded = false) => ({
        series: [{ name: 'Responden', data }],
        chart: {
            type: 'bar',
            height: expanded ? chartHeight(labels.length, 320, 38, 760) : 240,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 5,
                barHeight: expanded ? '54%' : '48%',
                dataLabels: { position: 'center' }
            }
        },
        colors: ['#8b1a2a'],
        dataLabels: {
            enabled: true,
            formatter: value => `${value}`,
            style: { colors: ['#fff'], fontSize: '11px', fontWeight: 700 }
        },
        xaxis: {
            categories: labels,
            decimalsInFloat: 0,
            labels: {
                trim: true,
                style: { colors: '#64748b', fontSize: '11px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 500 } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { title: { formatter: () => 'Responden:' } } }
    });

    const kinerjaOptions = (labels, data, expanded = false) => ({
        series: [{ name: 'Skor', data }],
        chart: { type: 'bar', height: expanded ? 420 : 240, toolbar: { show: false }, fontFamily: 'inherit' },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 5,
                columnWidth: expanded ? '44%' : '48%',
                dataLabels: { position: 'top' }
            }
        },
        colors: ['#8b1a2a'],
        dataLabels: {
            enabled: true,
            offsetY: -18,
            style: { colors: ['#334155'], fontSize: '11px', fontWeight: 700 },
            formatter: value => Number(value).toFixed(2)
        },
        xaxis: {
            categories: labels,
            labels: {
                trim: true,
                rotate: expanded ? -35 : -25,
                hideOverlappingLabels: true,
                style: { colors: '#64748b', fontSize: expanded ? '11px' : '10px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            max: 4,
            tickAmount: 4,
            labels: {
                formatter: value => Number(value).toFixed(0),
                style: { colors: '#334155', fontSize: '11px', fontWeight: 500 }
            }
        },
        grid: { yaxis: { lines: { show: true } }, borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { title: { formatter: () => 'Skor:' } } }
    });

    let prodiFullChart = null;
    let kinerjaFullChart = null;
    const compactProdi = sliceData(respondenProdiLabels, respondenProdiData);
    const compactKinerja = sliceData(chartLabels, chartData);

    if (compactProdi.data.length) {
        new ApexCharts(document.querySelector('#chart-prodi'), prodiOptions(compactProdi.labels, compactProdi.data)).render();
    } else {
        emptyChart('#chart-prodi', 'Belum ada data responden per Prodi.');
    }

    if (compactKinerja.data.length) {
        new ApexCharts(document.querySelector('#chart-kinerja'), kinerjaOptions(compactKinerja.labels, compactKinerja.data)).render();
    } else {
        emptyChart('#chart-kinerja', 'Belum ada data penilaian.');
    }

    document.getElementById('prodiChartModal').addEventListener('shown.bs.modal', () => {
        if (!respondenProdiData.length) {
            emptyChart('#chart-prodi-full', 'Belum ada data responden per Prodi.');
            return;
        }

        if (!prodiFullChart) {
            prodiFullChart = new ApexCharts(document.querySelector('#chart-prodi-full'), prodiOptions(respondenProdiLabels, respondenProdiData, true));
            prodiFullChart.render();
        }
    });

    document.getElementById('kinerjaChartModal').addEventListener('shown.bs.modal', () => {
        if (!chartData.length) {
            emptyChart('#chart-kinerja-full', 'Belum ada data penilaian.');
            return;
        }

        if (!kinerjaFullChart) {
            kinerjaFullChart = new ApexCharts(document.querySelector('#chart-kinerja-full'), kinerjaOptions(chartLabels, chartData, true));
            kinerjaFullChart.render();
        }
    });
</script>
@endsection
