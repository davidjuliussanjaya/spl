@extends('layouts.app')

@section('title', 'Survei')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Survei</span>
            </nav>
            <h3>Manajemen Survei</h3>
            <p>Kelola pengiriman, status respons, dan akses survei perusahaan.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('survey.bulk') }}" class="btn btn-outline-primary"><i class="bi bi-collection"></i> Buat massal</a>
            <a href="{{ route('addsurvey') }}" class="btn btn-primary d-inline-flex align-items-center gap-1"><i class="bi bi-file-earmark-plus-fill" aria-hidden="true"></i><span>Buat survei</span></a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
    @endif

    @if(! $selectedPeriode)
        <section class="card spl-period-selector">
            <div class="card-header spl-period-selector-header">
                <div class="spl-period-heading">
                    <span class="spl-period-heading-icon" aria-hidden="true"><i class="bi bi-calendar3"></i></span>
                    <div>
                        <h4 class="spl-toolbar-title">Pilih periode survei</h4>
                        <p class="spl-toolbar-subtitle">Pilih periode untuk melihat daftar sesi, status respons, dan kode akses.</p>
                    </div>
                </div>
                <span class="spl-period-count">{{ $periodeList->total() }} periode tersedia</span>
            </div>
            <div class="card-body spl-period-selector-body">
                <div class="spl-period-grid">
                    @forelse($periodeList as $periode)
                        <a href="{{ route('survey', ['periode_id' => $periode->id]) }}" class="spl-period-option" aria-label="Buka survei periode {{ $periode->nama_periode }}">
                            <span class="spl-period-option-icon" aria-hidden="true"><i class="bi bi-calendar3"></i></span>
                            <span class="spl-period-option-copy">
                                <span class="spl-period-option-label">Periode survei</span>
                                <strong>{{ $periode->nama_periode }}</strong>
                                <span class="spl-row-meta">{{ $periode->kode_periode }} · {{ $periode->tanggal_mulai->format('d M Y') }}–{{ $periode->tanggal_berakhir->format('d M Y') }}</span>
                                <span class="spl-period-option-action">Lihat daftar sesi <i class="bi bi-arrow-right"></i></span>
                            </span>
                            <span class="spl-period-option-arrow" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                        </a>
                    @empty
                        <div class="spl-empty"><i class="bi bi-inbox"></i>Belum ada periode survei. Buat survei baru untuk memulai.</div>
                    @endforelse
                </div>
            </div>
            @if($periodeList->hasPages())
                <div class="spl-pagination border-top">
                    <span>Menampilkan {{ $periodeList->firstItem() }}â€“{{ $periodeList->lastItem() }} dari {{ $periodeList->total() }} periode</span>
                    {{ $periodeList->links() }}
                </div>
            @endif
        </section>
    @else
    <section class="card">
        <div class="card-header spl-toolbar">
            <div>
                <h4 class="spl-toolbar-title">Daftar sesi {{ $selectedPeriode->nama_periode }} <span class="spl-filter-count">{{ $surveys->total() }} hasil</span></h4>
                <p class="spl-toolbar-subtitle">Cari berdasarkan judul, lulusan, perusahaan, atau kode akses.</p>
            </div>
            <a href="{{ route('survey') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Pilih periode lain</a>
        </div>

        <form action="{{ route('survey') }}" method="GET" class="spl-filter-panel">
            <input type="hidden" name="periode_id" value="{{ $selectedPeriode->id }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-7">
                    <label for="cari" class="form-label">Cari survei</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input id="cari" type="search" name="cari" class="form-control border-start-0" value="{{ request('cari') }}" placeholder="Judul, lulusan, perusahaan, atau kode">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="belum" @selected(request('status') === 'belum')>Belum diisi</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    </select>
                </div>
                <div class="col-12 col-lg-3 spl-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
                    <a href="{{ route('survey', ['periode_id' => $selectedPeriode->id]) }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table spl-table" id="table1">
                <thead>
                    <tr>
                        <th>Periode</th><th>Survei</th><th>Perusahaan</th><th>Lulusan</th><th>Kode akses</th><th>Status</th><th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surveys as $survey)
                        <tr>
                            <td><span class="badge bg-primary">{{ $survey->periode?->kode_periode ?? '-' }}</span><span class="spl-row-meta">{{ $survey->periode?->nama_periode ?? 'Tidak ada' }}</span></td>
                            <td><span class="spl-row-title">{{ $survey->judul }}</span></td>
                            <td>{{ $survey->penggunalulusan->nama_perusahaan ?? '-' }}</td>
                            <td>
                                <span class="spl-row-title">{{ $survey->lulusan->nama ?? '-' }}</span>
                                @if($survey->lulusan?->nim)<span class="spl-row-meta">{{ $survey->lulusan->nim }}</span>@endif
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="spl-copy-code"
                                    data-copy-code="{{ $survey->access_code }}"
                                    title="Salin kode akses {{ $survey->access_code }}"
                                    aria-label="Salin kode akses {{ $survey->access_code }} ke clipboard">
                                    <code>{{ $survey->access_code }}</code>
                                    <i class="bi bi-copy" aria-hidden="true"></i>
                                    <span class="visually-hidden">Salin kode akses</span>
                                </button>
                            </td>
                            <td>
                                @if($survey->is_completed)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Belum diisi</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    @if($survey->is_completed)
                                        <a href="{{ route('survey.edit', $survey->id) }}" class="spl-icon-action" title="Lihat detail arsip survei" aria-label="Lihat detail arsip survei {{ $survey->judul }}"><i class="bi bi-eye"></i></a>
                                    @else
                                        <a href="{{ route('survey.edit', $survey->id) }}" class="spl-icon-action" title="Lihat atau ubah survei" aria-label="Lihat atau ubah survei {{ $survey->judul }}"><i class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('survey.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Hapus survei ini? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="spl-icon-action text-danger" title="Hapus survei" aria-label="Hapus survei {{ $survey->judul }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="spl-empty"><i class="bi bi-inbox"></i>Belum ada survei yang sesuai. Ubah filter atau buat survei baru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($surveys->hasPages())
            <div class="spl-pagination">
                <span>Menampilkan {{ $surveys->firstItem() }}–{{ $surveys->lastItem() }} dari {{ $surveys->total() }} data</span>
                {{ $surveys->links() }}
            </div>
        @endif
    </section>
    @endif
</div>
@endsection

@push('styles')
<style>
    .spl-copy-code {
        align-items: center;
        background: #f1f5f9;
        border: 1px solid transparent;
        border-radius: 7px;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        gap: .42rem;
        max-width: 100%;
        padding: .22rem .42rem;
        transition: background-color .16s ease, border-color .16s ease, color .16s ease;
    }
    .spl-copy-code:hover, .spl-copy-code:focus-visible { background: #eff6ff; border-color: #93c5fd; color: #2563eb; outline: none; }
    .spl-copy-code:focus-visible { box-shadow: 0 0 0 3px rgba(37, 99, 235, .16); }
    .spl-copy-code code { margin: 0; }
    .spl-copy-code i { font-size: .78rem; }
    .spl-copy-feedback {
        background: #0f172a;
        border-radius: 8px;
        bottom: 1.25rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .2);
        color: #fff;
        font-size: .78rem;
        left: 50%;
        opacity: 0;
        padding: .6rem .8rem;
        pointer-events: none;
        position: fixed;
        transform: translate(-50%, 14px);
        transition: opacity .18s ease, transform .18s ease;
        z-index: 1100;
    }
    .spl-copy-feedback.is-visible { opacity: 1; transform: translate(-50%, 0); }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const feedback = document.createElement('div');
        feedback.className = 'spl-copy-feedback';
        feedback.setAttribute('role', 'status');
        feedback.setAttribute('aria-live', 'polite');
        document.body.appendChild(feedback);

        let feedbackTimer;
        const showFeedback = (message) => {
            feedback.textContent = message;
            feedback.classList.add('is-visible');
            clearTimeout(feedbackTimer);
            feedbackTimer = setTimeout(() => feedback.classList.remove('is-visible'), 2200);
        };

        const fallbackCopyText = (value) => {
            const temporaryInput = document.createElement('textarea');
            temporaryInput.value = value;
            temporaryInput.setAttribute('readonly', '');
            temporaryInput.style.position = 'fixed';
            temporaryInput.style.opacity = '0';
            document.body.appendChild(temporaryInput);
            temporaryInput.select();
            const copied = document.execCommand('copy');
            temporaryInput.remove();
            if (!copied) throw new Error('Clipboard tidak tersedia');
        };

        const copyText = async (value) => {
            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(value);
                    return;
                } catch (error) {
                    // Browser dapat menolak izin clipboard; gunakan fallback di bawah.
                }
            }

            fallbackCopyText(value);
        };

        document.querySelectorAll('[data-copy-code]').forEach((button) => {
            button.addEventListener('click', async () => {
                try {
                    await copyText(button.dataset.copyCode);
                    const icon = button.querySelector('i');
                    icon.className = 'bi bi-check2';
                    showFeedback('Kode survei berhasil disalin.');
                    setTimeout(() => { icon.className = 'bi bi-copy'; }, 1600);
                } catch (error) {
                    showFeedback('Kode belum dapat disalin. Silakan salin secara manual.');
                }
            });
        });
    })();
</script>
@endpush
