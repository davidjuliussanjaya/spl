{{-- Tampilkan Sidebar HANYA jika user sudah login --}}
@auth
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header pt-3 pb-3 spl-sidebar-header">
            <div class="d-flex justify-content-between align-items-center px-3">
                <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center spl-brand">
                    <div class="spl-brand-copy">
                        <div class="spl-brand-name">SPL</div>
                        <div class="spl-brand-product">Sistem Pelacakan Lulusan</div>
                    </div>
                </a>
                <div class="toggler flex-shrink-0">
                    <a href="#" class="sidebar-hide d-xl-none d-block text-secondary"><i class="bi bi-x fs-4"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu spl-nav-menu">

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(auth()->user()->hasRole('admin'))
                    <li class="sidebar-title">Administrasi sistem</li>
                    <li class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class='sidebar-link'>
                            <i class="bi bi-people-fill"></i>
                            <span>Pengguna Sistem</span>
                        </a>
                    </li>

                    <li class="sidebar-title">Data master</li>
                    <li class="sidebar-item {{ request()->routeIs('lulusan*', 'addgrad') ? 'active' : '' }}">
                        <a href="{{ route('lulusan') }}" class='sidebar-link'>
                            <i class="bi bi-person-badge-fill"></i>
                            <span>Lulusan</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('penggunalulusan*', 'create', 'pengguna.*') ? 'active' : '' }}">
                        <a href="{{ route('penggunalulusan') }}" class='sidebar-link'>
                            <i class="bi bi-building"></i>
                            <span>Perusahaan</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('periode.*') ? 'active' : '' }}">
                        <a href="{{ route('periode.index') }}" class='sidebar-link'>
                            <i class="bi bi-calendar-range-fill"></i>
                            <span>Periode</span>
                        </a>
                    </li>

                    <li class="sidebar-title">Survei & instrumen</li>
                    <li class="sidebar-item {{ request()->routeIs('survey*', 'addsurvey') ? 'active' : '' }}">
                        <a href="{{ route('survey') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Survei</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('pertanyaan*', 'addquestion', 'savequestion') ? 'active' : '' }}">
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

                    <li class="sidebar-title">Laporan</li>
                    <li class="sidebar-item {{ request()->routeIs('report') ? 'active' : '' }}">
                        <a href="{{ route('report') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-excel-fill"></i>
                            <span>Buat Laporan</span>
                        </a>
                    </li>
                @endif              
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

@endauth
