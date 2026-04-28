{{-- Tampilkan Sidebar HANYA jika user sudah login --}}
@auth
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
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
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Pertanyaan</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>


@endauth