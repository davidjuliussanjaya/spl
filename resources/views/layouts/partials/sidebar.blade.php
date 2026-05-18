{{-- Tampilkan Sidebar HANYA jika user sudah login --}}
@auth
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header pt-4 pb-2">
            <div class="d-flex justify-content-between align-items-center px-2">
                <div class="logo d-flex align-items-center" style="gap: 10px;">
                    <div class="text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(to right, #435ebe, #5777df);">
                        <i class="bi bi-layers-fill fs-5"></i>
                    </div>
                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                        <span class="fs-4 fw-bold text-dark" style="letter-spacing: -0.5px;">E-Lulusan</span>
                    </a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block text-secondary"><i class="bi bi-x bi-middle fs-3"></i></a>
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
    background: linear-gradient(to right, #435ebe, #5777df) !important;
    color: #fff !important;
    border-radius: 0.75rem;
    box-shadow: 0 4px 10px rgba(67, 94, 190, 0.2);
}
.sidebar-menu .sidebar-item.active .sidebar-link i,
.sidebar-menu .sidebar-item.active .sidebar-link span {
    color: #fff !important;
}
.sidebar-menu .sidebar-link {
    color: #475569 !important;
    font-weight: 600;
    transition: all 0.25s ease;
    border-radius: 0.75rem;
}
.sidebar-menu .sidebar-item:not(.active) .sidebar-link:hover {
    background-color: #f8f9fa !important;
    color: #435ebe !important;
}
.sidebar-menu .sidebar-item:not(.active) .sidebar-link:hover i {
    color: #435ebe !important;
}
</style>