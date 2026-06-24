@extends('layouts.app')

@section('title', 'Detail Arsip Survey — ' . ($arsip->lulusan_nama ?? $arsip->access_code))

@push('styles')
<style>
    @media print {
        /* Sembunyikan semua elemen kecuali area cetak */
        body * { visibility: hidden !important; }
        #cetakArea, #cetakArea * { visibility: visible !important; }
        #cetakArea {
            position: fixed !important;
            inset: 0 !important;
            padding: 24px 32px !important;
            background: #fff !important;
        }
        .no-print { display: none !important; }
    }

    .label-arsip {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
    }
    .value-arsip {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }
    .section-header {
        background: linear-gradient(to right, #8B1A2A, #B91C3A);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 6px 14px;
        border-radius: 6px;
        margin-bottom: 12px;
    }
    .jawaban-row:nth-child(even) { background: #fafafa; }
    .badge-jenis {
        font-size: 0.65rem;
        padding: 2px 7px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="page-heading">

    {{-- Header navigasi (tidak ikut cetak) --}}
    <div class="no-print page-title mb-4 pb-3 border-bottom">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="fw-bold mb-1">Detail Arsip Survey</h3>
                <p class="text-muted mb-0 small">
                    Rekaman permanen — tidak dapat diubah meski data master berubah.
                </p>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('report.arsip') }}">Arsip Survey</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Tombol aksi (tidak ikut cetak) --}}
    <div class="no-print d-flex gap-2 mb-4">
        <button onclick="window.print()" class="btn text-white fw-semibold px-4" style="background:#8B1A2A;">
            <i class="bi bi-printer-fill me-2"></i>Cetak / Simpan PDF
        </button>
        <a href="{{ route('report.arsip') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         AREA CETAK
    ════════════════════════════════════════════════════════════ --}}
    <div id="cetakArea">

        {{-- Kop dokumen --}}
        <div class="text-center mb-4 pb-3" style="border-bottom:2px solid #8B1A2A;">
            <h5 class="fw-bold mb-0" style="color:#8B1A2A;">ARSIP SURVEY EVALUASI PENGGUNA LULUSAN</h5>
            <p class="text-muted small mb-0">Universitas Dinamika — Tracer Study</p>
            <div class="mt-2">
                <span class="badge text-white me-2" style="background:#8B1A2A;">
                    Kode: {{ $arsip->access_code ?? '-' }}
                </span>
                @if($arsip->tahun_instrumen)
                <span class="badge" style="background:#FFF5D6;color:#92660A;">
                    Instrumen {{ $arsip->tahun_instrumen }}
                </span>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">

            {{-- Identitas Lulusan --}}
            <div class="col-12 col-md-4">
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
            </div>

            {{-- Identitas Perusahaan --}}
            <div class="col-12 col-md-4">
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
            </div>

            {{-- Identitas Penyelia --}}
            <div class="col-12 col-md-4">
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
            </div>
        </div>

        {{-- Tabel Jawaban --}}
        <div class="section-header"><i class="bi bi-list-check me-1"></i>Jawaban Survey</div>

        @php $jawabans = $arsip->jawaban_json ?? []; @endphp

        @if(empty($jawabans))
            <p class="text-muted text-center py-3">Tidak ada data jawaban.</p>
        @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" style="font-size:0.82rem;">
                <thead style="background:#f8f0f2;">
                    <tr>
                        <th class="text-center py-2" style="width:60px;color:#8B1A2A;">Kode</th>
                        <th class="py-2" style="color:#8B1A2A;">Aspek / Pertanyaan</th>
                        <th class="py-2" style="color:#8B1A2A;">Jawaban</th>
                        <th class="text-center py-2" style="width:60px;color:#8B1A2A;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevKategori = null; @endphp
                    @foreach($jawabans as $j)
                        @if(($j['kategori'] ?? null) !== $prevKategori)
                            <tr style="background:#FFF5F7;">
                                <td colspan="4" class="py-1 px-3" style="font-size:0.72rem;font-weight:700;color:#8B1A2A;text-transform:uppercase;letter-spacing:0.5px;">
                                    {{ $j['kategori'] ?? 'Tidak Berkategori' }}
                                </td>
                            </tr>
                            @php $prevKategori = $j['kategori'] ?? null; @endphp
                        @endif
                        <tr class="jawaban-row">
                            <td class="text-center fw-bold" style="color:#8B1A2A;">
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
                            <td class="text-center fw-bold" style="color:#8B1A2A;">
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
        <div class="mt-4 pt-3 text-muted text-center no-print" style="border-top:1px dashed #dee2e6;font-size:0.72rem;">
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
