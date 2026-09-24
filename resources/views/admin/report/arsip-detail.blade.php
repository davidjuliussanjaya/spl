@extends('layouts.app')

@php
    $detailTitle = $detailTitle ?? 'Detail Arsip Survey';
    $backUrl = $backUrl ?? route('report.arsip');
    $backLabel = $backLabel ?? 'Kembali ke Daftar Arsip';
    $breadcrumbLabel = $breadcrumbLabel ?? 'Arsip Survey';
@endphp

@section('title', $detailTitle . ' — ' . ($arsip->lulusan_nama ?? $arsip->access_code))

@push('styles')
<style>
    @media print {
        body * { visibility: hidden !important; }
        #cetakArea, #cetakArea * { visibility: visible !important; }
        #cetakArea {
            position: fixed !important;
            inset: 0 !important;
            padding: 24px 32px !important;
            background: #fff !important;
            border: 0 !important;
            box-shadow: none !important;
        }
        .no-print { display: none !important; }
    }

    .spl-survey-detail { padding-bottom: 1rem; }
    .spl-survey-detail-header { align-items: flex-start; border-bottom: 1px solid var(--spl-border); display: flex; gap: 1rem; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 1.25rem; }
    .spl-survey-detail-header > .row { width: 100%; }
    .spl-survey-detail-title { color: var(--spl-text); font-size: 1.45rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.2; margin: 0; }
    .spl-survey-detail-subtitle { color: var(--spl-muted); font-size: .83rem; margin: .35rem 0 0; }
    .spl-survey-detail .breadcrumb { justify-content: flex-end; margin: .15rem 0 0; }
    .spl-survey-detail .breadcrumb-item, .spl-survey-detail .breadcrumb-item a { font-size: .78rem; }
    .spl-survey-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.25rem; }
    .spl-archive-document { background: #fff; border: 1px solid var(--spl-border); border-radius: var(--spl-radius); box-shadow: var(--spl-shadow); padding: 1.5rem; }
    .spl-archive-document-head { align-items: center; border-bottom: 2px solid var(--spl-brand); display: flex; flex-direction: column; gap: .35rem; padding: .25rem 0 1.25rem; text-align: center; }
    .spl-archive-document-title { color: var(--spl-brand); font-size: 1rem; font-weight: 800; letter-spacing: -.01em; margin: 0; }
    .spl-archive-document-subtitle { color: var(--spl-muted); font-size: .78rem; margin: 0; }
    .spl-archive-document-head .text-muted { color: var(--spl-muted) !important; font-size: .78rem; margin: 0; }
    .spl-archive-document-badges { display: flex; flex-wrap: wrap; gap: .45rem; justify-content: center; margin-top: .45rem; }
    .spl-archive-code-badge { background: var(--spl-brand) !important; color: #fff !important; }
    .spl-archive-instrument-badge { background: #fff5d6 !important; color: #92660a !important; }
    .spl-archive-info-grid { margin-bottom: 1.5rem; margin-top: 1.5rem; }
    .spl-archive-info-card { background: #fff; border: 1px solid var(--spl-border); border-radius: 10px; height: 100%; padding: 1rem; }
    .spl-archive-section-title { align-items: center; border-bottom: 1px solid var(--spl-border); color: var(--spl-text); display: flex; font-size: .78rem; font-weight: 800; gap: .45rem; letter-spacing: .035em; margin-bottom: 1rem; padding-bottom: .7rem; text-transform: uppercase; }
    .spl-archive-section-title i { color: var(--spl-brand); font-size: .92rem; }
    .spl-archive-info-grid .section-header { align-items: center; border-bottom: 1px solid var(--spl-border); color: var(--spl-text); display: flex; font-size: .78rem; font-weight: 800; gap: .45rem; letter-spacing: .035em; margin-bottom: 1rem; padding-bottom: .7rem; text-transform: uppercase; }
    .spl-archive-info-grid .section-header i { color: var(--spl-brand); font-size: .92rem; }
    .spl-archive-info-grid .ps-1 { display: flex; flex-direction: column; gap: .8rem; padding-left: 0 !important; }
    .spl-archive-info-grid .mb-2 { margin-bottom: 0 !important; }
    .spl-archive-fields { display: flex; flex-direction: column; gap: .8rem; }
    .spl-archive-field { min-width: 0; }
    .label-arsip { color: #94a3b8; font-size: .67rem; font-weight: 800; letter-spacing: .06em; line-height: 1.25; text-transform: uppercase; }
    .value-arsip { color: #334155; font-size: .86rem; font-weight: 600; line-height: 1.45; margin-top: .18rem; overflow-wrap: anywhere; }
    .spl-archive-answers { border: 1px solid var(--spl-border); border-radius: 10px; overflow-x: auto; }
    .spl-archive-answers .table { font-size: .82rem; margin: 0; min-width: 760px; table-layout: auto; }
    .spl-archive-answers .table thead th { background: #f8fafc; color: #475569; font-size: .68rem; font-weight: 800; letter-spacing: .045em; padding: .75rem 1rem; text-transform: uppercase; white-space: nowrap; }
    .spl-archive-answers .table tbody td { border-color: #edf2f7; overflow-wrap: normal; padding: .85rem 1rem; vertical-align: top; word-break: normal; }
    .spl-answer-code-column { min-width: 112px; width: 112px; }
    .spl-answer-value-column { min-width: 84px; width: 84px; }
    .spl-archive-answers .table th:first-child, .spl-archive-answers .table td:first-child { min-width: 112px; width: 112px; }
    .spl-archive-answers .table th:last-child, .spl-archive-answers .table td:last-child { min-width: 84px; width: 84px; }
    .spl-archive-category-row td { background: var(--spl-brand-soft); color: var(--spl-brand); font-size: .7rem; font-weight: 800; letter-spacing: .055em; padding: .52rem 1rem !important; text-transform: uppercase; }
    .jawaban-row:nth-child(even) { background: #fbfdff; }
    .spl-answer-code { color: var(--spl-brand); font-weight: 800; white-space: nowrap; }
    .badge-jenis { border-radius: 999px; display: inline-block; font-size: .64rem; font-weight: 700; line-height: 1.2; margin-top: .3rem; padding: .2rem .45rem; white-space: nowrap; }
    .spl-answer-value { color: var(--spl-brand); font-weight: 800; }
    .spl-archive-footer { border-top: 1px dashed #cbd5e1; color: var(--spl-muted); font-size: .72rem; margin-top: 1.5rem; padding-top: 1rem; text-align: center; }

    @media (max-width: 767.98px) {
        .spl-survey-detail-header { flex-direction: column; gap: .75rem; }
        .spl-survey-detail .breadcrumb { justify-content: flex-start; }
        .spl-survey-detail-title { font-size: 1.2rem; }
        .spl-survey-actions .btn { flex: 1 1 auto; }
        .spl-archive-document { padding: 1rem; }
        .spl-archive-document-title { font-size: .9rem; }
        .spl-archive-info-grid { margin-top: 1rem; }
        .spl-archive-answers .table { min-width: 760px; }
    }
</style>
@endpush

@section('content')
<div class="page-heading spl-survey-detail">

    {{-- Header navigasi (tidak ikut cetak) --}}
    <header class="no-print spl-survey-detail-header">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="spl-survey-detail-title">{{ $detailTitle }}</h3>
                <p class="spl-survey-detail-subtitle">
                    Rekaman permanen — tidak dapat diubah meski data master berubah.
                </p>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ $backUrl }}">{{ $breadcrumbLabel }}</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>

    {{-- Tombol aksi (tidak ikut cetak) --}}
    <div class="no-print spl-survey-actions">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer-fill me-2"></i>Cetak / Simpan PDF
        </button>
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ $backLabel }}
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         AREA CETAK
    ════════════════════════════════════════════════════════════ --}}
    <div id="cetakArea" class="spl-archive-document">

        {{-- Kop dokumen --}}
        <div class="spl-archive-document-head">
            <h5 class="spl-archive-document-title">ARSIP SURVEY EVALUASI PENGGUNA LULUSAN</h5>
            <p class="text-muted small mb-0">Universitas Dinamika — Tracer Study</p>
            <div class="spl-archive-document-badges">
                <span class="badge spl-archive-code-badge">
                    Kode: {{ $arsip->access_code ?? '-' }}
                </span>
                @if($arsip->periode_kode)
                <span class="badge spl-archive-instrument-badge">
                    {{ $arsip->periode_nama ?: $arsip->periode_kode }}
                </span>
                @endif
                @if($arsip->tahun_instrumen)
                <span class="badge spl-archive-instrument-badge">
                    Instrumen {{ $arsip->tahun_instrumen }}
                </span>
                @endif
            </div>
        </div>

        <div class="row g-4 spl-archive-info-grid">

            {{-- Identitas Lulusan --}}
            <div class="col-12 col-md-4">
                <section class="spl-archive-info-card">
                <div class="section-header"><i class="bi bi-person-fill me-1"></i>Identitas Lulusan</div>
                <div class="ps-1">
                    <div class="mb-2">
                        <div class="label-arsip">Nama</div>
                        <div class="value-arsip">{{ $arsip->lulusan_nama ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">NIM</div>
                        <div class="value-arsip">{{ $arsip->lulusan_nim ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Program Studi</div>
                        <div class="value-arsip">{{ $arsip->lulusan_program_studi ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Fakultas</div>
                        <div class="value-arsip">{{ $arsip->lulusan_fakultas ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Tahun Lulus</div>
                        <div class="value-arsip">{{ $arsip->lulusan_tahun_lulus ?? '-' }}</div>
                    </div>
                </div>
                </section>
            </div>

            {{-- Identitas Perusahaan --}}
            <div class="col-12 col-md-4">
                <section class="spl-archive-info-card">
                <div class="section-header"><i class="bi bi-building me-1"></i>Identitas Perusahaan</div>
                <div class="ps-1">
                    <div class="mb-2">
                        <div class="label-arsip">Nama Perusahaan</div>
                        <div class="value-arsip">{{ $arsip->perusahaan_nama ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Jenis Perusahaan</div>
                        <div class="value-arsip">{{ $arsip->perusahaan_jenis ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Alamat</div>
                        <div class="value-arsip">{{ $arsip->perusahaan_alamat ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Kota / Negara</div>
                        <div class="value-arsip">
                            {{ implode(', ', array_filter([$arsip->perusahaan_cabang_kota, $arsip->perusahaan_cabang_negara])) ?: '-' }}
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">No. Badan Hukum</div>
                        <div class="value-arsip">{{ $arsip->perusahaan_nomor_badan_hukum ?? '-' }}</div>
                    </div>
                </div>
                </section>
            </div>

            {{-- Identitas Penyelia --}}
            <div class="col-12 col-md-4">
                <section class="spl-archive-info-card">
                <div class="section-header"><i class="bi bi-person-badge-fill me-1"></i>Penyelia Pengisi</div>
                <div class="ps-1">
                    <div class="mb-2">
                        <div class="label-arsip">Nama Penyelia</div>
                        <div class="value-arsip">{{ $arsip->penyelia_nama ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Jabatan</div>
                        <div class="value-arsip">{{ $arsip->penyelia_jabatan ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Email</div>
                        <div class="value-arsip">{{ $arsip->penyelia_email ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Kontak</div>
                        <div class="value-arsip">{{ $arsip->penyelia_kontak ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Jml. Lulusan Bekerja</div>
                        <div class="value-arsip">{{ $arsip->jumlah_lulusan_bekerja ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="label-arsip">Tanggal Pengisian</div>
                        <div class="value-arsip">
                            {{ $arsip->submitted_at ? \Carbon\Carbon::parse($arsip->submitted_at)->translatedFormat('d F Y, H:i') : '-' }}
                        </div>
                    </div>
                </div>
                </section>
            </div>
        </div>

        {{-- Tabel Jawaban --}}
        <div class="spl-archive-section-title"><i class="bi bi-list-check"></i>Jawaban Survey</div>

        @php $jawabans = $arsip->jawaban_json ?? []; @endphp

        @if(empty($jawabans))
            <p class="text-muted text-center py-3">Tidak ada data jawaban.</p>
        @else
        <div class="table-responsive spl-archive-answers">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center py-2 spl-answer-code-column" style="color:#2563EB;">Kode</th>
                        <th class="py-2" style="color:#2563EB;">Aspek / Pertanyaan</th>
                        <th class="py-2" style="color:#2563EB;">Jawaban</th>
                        <th class="text-center py-2 spl-answer-value-column" style="color:#2563EB;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevKategori = null; @endphp
                    @foreach($jawabans as $j)
                        @if(($j['kategori'] ?? null) !== $prevKategori)
                            <tr class="spl-archive-category-row">
                                <td colspan="4">
                                    {{ $j['kategori'] ?? 'Tidak Berkategori' }}
                                </td>
                            </tr>
                            @php $prevKategori = $j['kategori'] ?? null; @endphp
                        @endif
                        <tr class="jawaban-row">
                            <td class="text-center spl-answer-code">
                                {{ $j['kode'] ?? '-' }}
                                <div>
                                    @php
                                        $jenis = $j['jenis'] ?? '';
                                        $labelJenis = match($jenis) {
                                            'rating'          => ['label' => 'Rating',    'bg' => '#EFF6FF', 'color' => '#1D4ED8'],
                                            'essay'           => ['label' => 'Esai',      'bg' => '#F0FDF4', 'color' => '#166534'],
                                            'multiple_choice' => ['label' => 'Pilihan',   'bg' => '#FFF7ED', 'color' => '#92400E'],
                                            default           => ['label' => $jenis,      'bg' => '#F1F5F9', 'color' => '#475569'],
                                        };
                                    @endphp
                                    <span class="badge-jenis" style="background:{{ $labelJenis['bg'] }};color:{{ $labelJenis['color'] }};">
                                        {{ $labelJenis['label'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-2">{{ $j['soal'] ?? '-' }}</td>
                            <td class="py-2">
                                @if(is_array($j['jawaban'] ?? null))
                                    <ul class="mb-0 ps-3">
                                        @foreach($j['jawaban'] as $pil)
                                            <li>{{ $pil }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $j['jawaban'] ?? '<em class="text-muted">Tidak dijawab</em>' }}
                                @endif
                            </td>
                            <td class="text-center spl-answer-value">
                                @if(isset($j['nilai']) && $j['nilai'] !== null)
                                    {{ $j['nilai'] }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Footer dokumen --}}
        <div class="no-print spl-archive-footer">
            Arsip ID #{{ $arsip->id }} &nbsp;·&nbsp; Disimpan: {{ $arsip->created_at?->format('d M Y H:i') }}
            &nbsp;·&nbsp; Data ini bersifat permanen dan tidak dapat diubah.
        </div>

        {{-- Area tanda tangan untuk versi cetak --}}
        <div class="d-none d-print-block mt-5">
            <div class="row text-center">
                <div class="col-6">
                    <p class="mb-5">Surabaya, {{ \Carbon\Carbon::parse($arsip->submitted_at ?? now())->format('d F Y') }}</p>
                    <p class="mb-0 fw-bold">{{ $arsip->penyelia_nama ?? '_________________' }}</p>
                    <p class="text-muted small">{{ $arsip->penyelia_jabatan ?? 'Penyelia' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-5">Mengetahui,</p>
                    <p class="mb-0 fw-bold">_________________</p>
                    <p class="text-muted small">Koordinator Tracer Study</p>
                </div>
            </div>
        </div>

    </div>{{-- end #cetakArea --}}

</div>
@endsection
