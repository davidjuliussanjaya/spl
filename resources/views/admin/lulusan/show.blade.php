@extends('layouts.app')

@section('title', 'Detail Lulusan')

@section('content')
@php
    $dariArsip = fn (string $field, ?string $fallback = null) => $surveyArsip?->{$field} ?: $fallback ?: '-';
    $persentaseIndeks = $indeksKepuasan ? min(100, round(($indeksKepuasan['skor_akhir'] / 4) * 100)) : 0;
@endphp
<div class="page-heading lulusan-detail-page">
    <div class="profile-header">
        <div><h4 class="profile-title"><i class="bi bi-person-badge-fill"></i>Profil Lulusan</h4>
            @if($hasCompletedSurvey)
                <span class="survey-status badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Survei sudah diisi</span>
            @endif
        </div>
        <a href="{{ route('lulusan') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
    <section class="section">
        @if(! $hasCompletedSurvey)
            <div class="card detail-card mahasiswa-edit-card"><div class="card-body">
                <div class="detail-card-heading">
                    <div><h6 class="detail-card-title mb-1"><i class="bi bi-person-vcard me-2"></i>Data Mahasiswa</h6><p class="mb-0 text-muted small">Perbarui data lulusan sebelum perusahaan mengisi survei.</p></div>
                    <span class="badge bg-light-primary">Dapat diedit</span>
                </div>
                <form action="{{ route('lulusan.update-mahasiswa', $lulusan->id) }}" method="POST" class="mahasiswa-edit-form">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6"><label for="nama" class="form-label">Nama lengkap</label><input id="nama" name="nama" type="text" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $lulusan->nama) }}" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label for="nim" class="form-label">NIM</label><input id="nim" name="nim" type="text" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim', $lulusan->nim) }}" required>@error('nim')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label for="fakultas_id" class="form-label">Fakultas</label><select id="fakultas_id" name="fakultas_id" class="form-select @error('fakultas_id') is-invalid @enderror" required><option value="" disabled>Pilih fakultas</option>@foreach($fakultasList as $fakultas)<option value="{{ $fakultas->id }}" @selected((string) old('fakultas_id', $lulusan->fakultas_id) === (string) $fakultas->id)>{{ $fakultas->nama }} ({{ $fakultas->kode }})</option>@endforeach</select>@error('fakultas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label for="program_studi_id" class="form-label">Program studi</label><select id="program_studi_id" name="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror" required><option value="" disabled>Pilih program studi</option>@foreach($fakultasList as $fakultas) @foreach($fakultas->programStudis as $prodi)<option value="{{ $prodi->id }}" data-fakultas-id="{{ $fakultas->id }}" @selected((string) old('program_studi_id', $lulusan->program_studi_id) === (string) $prodi->id)>{{ $prodi->nama }}</option>@endforeach @endforeach</select>@error('program_studi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label for="tahun_lulus" class="form-label">Tahun lulus</label><input id="tahun_lulus" name="tahun_lulus" type="date" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $lulusan->tahun_lulus?->format('Y-m-d')) }}" required>@error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>
                    <div class="d-flex justify-content-end mt-4"><button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan perubahan</button></div>
                </form>
            </div></div>
        @else
        <div class="lulusan-detail-layout">
            <div class="detail-info-stack">
                <div class="card detail-card"><div class="card-body">
                    <h6 class="detail-card-title"><i class="bi bi-person-vcard me-2"></i>Data Mahasiswa</h6>
                    <dl class="detail-list mb-0">
                        <div><dt>Nama lengkap</dt><dd>{{ $lulusan->nama }}</dd></div><div><dt>NIM</dt><dd>{{ $lulusan->nim }}</dd></div>
                        <div><dt>Fakultas</dt><dd>{{ $lulusan->fakultasMaster?->nama ?? $lulusan->fakultas ?? '-' }}</dd></div><div><dt>Program studi</dt><dd>{{ $lulusan->programStudi?->nama ?? $lulusan->program_studi ?? '-' }}</dd></div>
                        <div><dt>Tahun lulus</dt><dd>{{ $lulusan->tahun_lulus?->format('Y') ?? '-' }}</dd></div>
                    </dl>
                </div></div>
                <div class="card detail-card"><div class="card-body">
                    <h6 class="detail-card-title"><i class="bi bi-buildings me-2"></i>Data Perusahaan</h6>
                    <dl class="detail-list mb-0">
                        <div><dt>Nama perusahaan</dt><dd>{{ $dariArsip('perusahaan_nama', $lulusan->pengguna?->nama_perusahaan) }}</dd></div><div><dt>Jenis perusahaan</dt><dd>{{ $dariArsip('perusahaan_jenis', $lulusan->pengguna?->jenis_perusahaan) }}</dd></div>
                        <div><dt>Alamat</dt><dd>{{ $dariArsip('perusahaan_alamat', $lulusan->pengguna?->alamat_perusahaan) }}</dd></div><div><dt>Kontak perusahaan</dt><dd>{{ $dariArsip('perusahaan_kontak', $lulusan->pengguna?->kontak_perusahaan) }}</dd></div>
                        <div><dt>Nomor badan hukum</dt><dd>{{ $dariArsip('perusahaan_nomor_badan_hukum', $lulusan->pengguna?->nomor_badan_hukum) }}</dd></div>
                    </dl>
                </div></div>
                <div class="card detail-card"><div class="card-body">
                    <h6 class="detail-card-title"><i class="bi bi-person-workspace me-2"></i>Data Penyelia</h6>
                    <dl class="detail-list mb-0">
                        <div><dt>Nama penyelia</dt><dd>{{ $dariArsip('penyelia_nama', $lulusan->pengguna?->nama_penyelia) }}</dd></div><div><dt>Jabatan</dt><dd>{{ $dariArsip('penyelia_jabatan', $lulusan->pengguna?->jabatan_penyelia) }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $dariArsip('penyelia_email', $lulusan->pengguna?->email_penyelia) }}</dd></div><div><dt>Kontak</dt><dd>{{ $dariArsip('penyelia_kontak', $lulusan->pengguna?->kontak_penyelia) }}</dd></div>
                    </dl>
                </div></div>
            </div>
            <div class="card detail-card indeks-card"><div class="card-body">
                <h6 class="detail-card-title"><i class="bi bi-bar-chart-line me-2"></i>Indeks Kepuasan Lulusan</h6>
                @if($indeksKepuasan)
                    <div class="d-flex align-items-center gap-4 mb-3"><div class="score-circle" style="--score: {{ $persentaseIndeks }}%"><span>{{ number_format($indeksKepuasan['skor_akhir'], 2) }}</span><small>/ 4.00</small></div><div><div class="fw-bold">{{ $indeksKepuasan['skor_murni'] >= 3.5 ? 'Sangat baik' : ($indeksKepuasan['skor_murni'] >= 2.5 ? 'Baik' : 'Perlu perhatian') }}</div><div class="small text-muted">Berdasarkan {{ array_sum($indeksKepuasan['counts']) }} jawaban penilaian pada survei ini.</div></div></div>
                    <div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Kategori</th><th class="text-end">Indeks</th></tr></thead><tbody>@foreach($ratingKategori as $rating)<tr><td>{{ $rating['kategori'] }}</td><td class="text-end fw-bold">{{ number_format($rating['indeks'], 2) }} / 4.00</td></tr>@endforeach</tbody></table></div>
                @elseif($hasCompletedSurvey)
                    <p class="text-muted mb-0">Survei telah diisi, tetapi belum ada jawaban penilaian yang dapat dihitung.</p>
                @else
                    <p class="text-muted mb-0">Indeks kepuasan akan ditampilkan setelah survei lulusan ini diisi.</p>
                @endif
            </div></div>
        </div>
        @endif
    </section>
</div>
<style>
    .lulusan-detail-page { max-width: 1440px; margin: 0 auto; padding-bottom: 2rem; }
    .profile-header { align-items: center; display: flex; justify-content: space-between; margin: .35rem 0 1.5rem; }
    .profile-title { color: #173b74; font-size: 1.35rem; font-weight: 700; letter-spacing: -.02em; margin: 0 0 .4rem; }.profile-title i { color: #2563eb; margin-right: .45rem; }.survey-status { font-size: .76rem; font-weight: 600; padding: .38rem .62rem; }
    .lulusan-detail-layout { align-items: start; display: grid; gap: 1rem; grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); }
    .detail-info-stack { display: grid; gap: 1rem; }
    .detail-card { border: 1px solid #dfe7f2; border-radius: 14px; box-shadow: 0 8px 20px rgba(38, 66, 110, .06); overflow: hidden; }.detail-card .card-body { padding: 1.25rem 1.35rem; }
    .detail-card-title { color: #627eb2; font-size: .78rem; font-weight: 700; letter-spacing: .01em; margin: 0 0 .9rem; text-transform: uppercase; }.detail-card-title i { color: #2563eb; }
    .detail-card-heading { align-items: flex-start; display: flex; gap: 1rem; justify-content: space-between; margin-bottom: 1.35rem; }.detail-card-heading .detail-card-title { margin-bottom: .3rem; }.mahasiswa-edit-card { margin: 0 auto; max-width: 900px; }.mahasiswa-edit-card .card-body { padding: 1.5rem; }.mahasiswa-edit-form .form-label { color: #475569; font-size: .82rem; font-weight: 600; margin-bottom: .38rem; }.mahasiswa-edit-form .form-control, .mahasiswa-edit-form .form-select { border-color: #d8e2ef; min-height: 40px; }
    .detail-list { display: grid; gap: .56rem; }.detail-list > div { align-items: baseline; display: grid; gap: .9rem; grid-template-columns: minmax(125px, 36%) 1fr; }.detail-list dt { color: #64748b; font-size: .8rem; font-weight: 600; }.detail-list dd { color: #12213d; font-size: .9rem; font-weight: 500; line-height: 1.38; margin: 0; overflow-wrap: anywhere; }
    .score-circle { --score: 0%; align-items: center; background: conic-gradient(#2563eb var(--score), #e8eef8 0); border-radius: 50%; display: flex; flex-direction: column; height: 104px; justify-content: center; position: relative; width: 104px; z-index: 0; }.score-circle::before { background: #fff; border-radius: 50%; content: ''; inset: 8px; position: absolute; z-index: -1; }.score-circle span { color: #173b74; font-size: 1.35rem; font-weight: 700; }.score-circle small { color: #64748b; font-size: .68rem; }
    .detail-card .table { font-size: .84rem; }.detail-card .table th { color: #64748b; font-size: .73rem; font-weight: 700; text-transform: uppercase; }.detail-card .table td { color: #334155; padding: .6rem .4rem; }
    @media (max-width: 991.98px) { .lulusan-detail-layout { grid-template-columns: 1fr; } }
    @media (max-width: 575.98px) { .profile-header { align-items: flex-start; gap: 1rem; }.detail-card .card-body, .mahasiswa-edit-card .card-body { padding: 1.1rem; }.detail-card-heading { align-items: flex-start; flex-direction: column; gap: .55rem; }.detail-list > div { gap: .18rem; grid-template-columns: 1fr; } }
</style>
@if(! $hasCompletedSurvey)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fakultas = document.getElementById('fakultas_id');
    const prodi = document.getElementById('program_studi_id');
    const filterProdi = () => {
        [...prodi.options].forEach((option) => {
            if (!option.value) return;
            option.hidden = fakultas.value && option.dataset.fakultasId !== fakultas.value;
        });
        if (prodi.selectedOptions[0]?.hidden) prodi.value = '';
    };
    fakultas.addEventListener('change', filterProdi);
    filterProdi();
});
</script>
@endif
@endsection
