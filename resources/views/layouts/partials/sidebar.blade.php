{{-- Tampilkan Sidebar HANYA jika user sudah login --}}
@auth
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header pt-3 pb-3" style="border-bottom:1px solid #f0e8ea;">
            <div class="d-flex justify-content-between align-items-center px-3">
                <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center" style="gap:10px;min-width:0;">
                    {{-- Logo kotak bertumpuk ala identitas Dinamika --}}
                    <div class="flex-shrink-0" style="position:relative;width:36px;height:36px;">
                        <div style="position:absolute;top:0;left:0;width:28px;height:28px;background:#8B1A2A;border-radius:6px;"></div>
                        <div style="position:absolute;bottom:0;right:0;width:22px;height:22px;background:#C9A227;border-radius:5px;border:2px solid #fff;"></div>
                    </div>
                    <div style="min-width:0;line-height:1.25;">
                        <div class="fw-bold text-dark" style="font-size:0.8rem;letter-spacing:-0.2px;white-space:nowrap;">Undika</div>
                        <div style="font-size:0.6rem;font-weight:700;letter-spacing:0.6px;text-transform:uppercase;color:#8B1A2A;">Tracer Study</div>
                    </div>
                </a>
                <div class="toggler flex-shrink-0">
                    <a href="#" class="sidebar-hide d-xl-none d-block text-secondary"><i class="bi bi-x fs-4"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(auth()->user()->hasRole('admin'))
                    <li class="sidebar-item {{ request()->routeIs('survey') ? 'active' : '' }}">
                        <a href="{{ route('survey') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Survey</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('lulusan') ? 'active' : '' }}">
                        <a href="{{ route('lulusan') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Lulusan</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('penggunalulusan') ? 'active' : '' }}">
                        <a href="{{ route('penggunalulusan') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Pengguna Lulusan</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('pertanyaan') ? 'active' : '' }}">
                        <a href="{{ route('pertanyaan') }}" class='sidebar-link'>
                            <i class="bi bi-patch-question-fill"></i>
                            <span>Pertanyaan</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('kategori.index') || request()->routeIs('kategori.create') || request()->routeIs('kategori.edit') ? 'active' : '' }}">
                        <a href="{{ route('kategori.index') }}" class='sidebar-link'>
                            <i class="bi bi-tags-fill"></i>
                            <span>Aspek Evaluasi</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('report') ? 'active' : '' }}">
                        <a href="{{ route('report') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-excel-fill"></i>
                            <span>Cetak Laporan</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('report.arsip') || request()->routeIs('report.arsip.detail') ? 'active' : '' }}">
                        <a href="{{ route('report.arsip') }}" class='sidebar-link'>
                            <i class="bi bi-archive-fill"></i>
                            <span>Arsip Survey</span>
                        </a>
                    </li>
                @endif              
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

@endauth

<style>
/* Perbaikan warna item sidebar agar rapi dan modern minimalis */
.sidebar-menu .sidebar-item.active .sidebar-link {
    background: linear-gradient(to right, #8B1A2A, #B91C3A) !important;
    color: #fff !important;
    border-radius: 0.75rem;
    box-shadow: 0 4px 10px rgba(139, 26, 42, 0.25);
}
.sidebar-menu .sidebar-item.active .sidebar-link i,
.sidebar-menu .sidebar-item.active .sidebar-link span {
    color: #fff !important;
}
.sidebar-menu .sidebar-link {
    color: #475569 !important;
    font-weight: 500;
    font-size: 0.83rem !important;
    transition: all 0.25s ease;
    border-radius: 0.75rem;
}
.sidebar-menu .sidebar-item:not(.active) .sidebar-link:hover {
    background-color: #FDE8EC !important;
    color: #8B1A2A !important;
}
.sidebar-menu .sidebar-item:not(.active) .sidebar-link:hover i {
    color: #8B1A2A !important;
}
</style>