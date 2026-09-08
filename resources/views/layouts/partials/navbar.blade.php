{{-- Navbar hanya untuk pengguna yang sudah masuk --}}
@auth
<header class="spl-topbar">
    <nav class="navbar navbar-expand">
        <div class="container-fluid px-3">
            <a href="#" class="burger-btn spl-icon-button" aria-label="Buka atau tutup navigasi"><i class="bi bi-list"></i></a>
            <div class="spl-topbar-context d-none d-md-block">Sistem Pelacakan Lulusan</div>
            <div class="ms-auto dropdown">
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="spl-user-menu" aria-label="Menu profil pengguna">
                    <span class="spl-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    <span class="d-none d-sm-block spl-user-copy">
                        <span class="spl-user-name">{{ Auth::user()->name }}</span>
                        <span class="spl-user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
                    </span>
                    <i class="bi bi-chevron-down d-none d-sm-block spl-user-chevron"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end spl-user-dropdown">
                    <li><div class="spl-dropdown-identity"><div class="fw-semibold text-dark">{{ Auth::user()->name }}</div><div class="text-muted">{{ Auth::user()->email }}</div></div></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> Profil saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item dropdown-item-danger d-flex align-items-center gap-2"><i class="bi bi-box-arrow-left"></i> Keluar</button></form></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
@endauth
