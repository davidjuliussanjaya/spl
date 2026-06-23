{{-- Tampilkan Navbar HANYA jika user sudah login --}}
@auth
<header class='mb-3'>
    <nav class="navbar navbar-expand navbar-light px-3" style="background:rgba(255,255,255,0.92);backdrop-filter:blur(10px);border-bottom:1px solid #f0e8ea;min-height:60px;">
        <div class="container-fluid px-0">

            {{-- Burger / toggle sidebar --}}
            <a href="#" class="burger-btn d-block me-3" style="color:#8B1A2A;line-height:1;">
                <i class="bi bi-list" style="font-size:1.4rem;"></i>
            </a>

            {{-- Breadcrumb area (optional slot) --}}
            <div class="flex-grow-1"></div>

            <div class="d-flex align-items-center gap-3">

                {{-- Notifikasi --}}
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown"
                       class="d-flex align-items-center justify-content-center bg-white"
                       style="width:36px;height:36px;border-radius:10px;border:1px solid #f0e8ea;color:#6b7280;text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.borderColor='#8B1A2A';this.style.color='#8B1A2A';"
                       onmouseout="this.style.borderColor='#f0e8ea';this.style.color='#6b7280';">
                        <i class="bi bi-bell" style="font-size:0.9rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:220px;border-radius:12px;">
                        <li><h6 class="dropdown-header" style="color:#8B1A2A;font-size:0.7rem;letter-spacing:.5px;text-transform:uppercase;">Notifikasi</h6></li>
                        <li><span class="dropdown-item-text text-muted" style="font-size:0.8rem;">Tidak ada notifikasi baru.</span></li>
                    </ul>
                </div>

                {{-- User Menu --}}
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none d-flex align-items-center gap-2"
                       style="background:#fff;border:1px solid #f0e8ea;border-radius:50px;padding:5px 12px 5px 5px;transition:all .2s;"
                       onmouseover="this.style.borderColor='#e8c5cb';"
                       onmouseout="this.style.borderColor='#f0e8ea';">

                        {{-- Avatar inisial --}}
                        <div class="d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                             style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#8B1A2A,#B91C3A);font-size:0.72rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>

                        <div class="d-none d-sm-block" style="line-height:1.3;">
                            <div class="fw-semibold text-dark" style="font-size:0.8rem;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->name }}</div>
                            <div style="font-size:0.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#8B1A2A;">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                        </div>

                        <i class="bi bi-chevron-down d-none d-sm-block" style="font-size:0.65rem;color:#9ca3af;"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1" style="min-width:180px;border-radius:12px;">
                        <li>
                            <div class="px-3 py-2" style="border-bottom:1px solid #f5e8ea;">
                                <div class="fw-semibold text-dark" style="font-size:0.82rem;">{{ Auth::user()->name }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">{{ Auth::user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.edit') }}" style="font-size:0.82rem;">
                                <i class="bi bi-person-circle" style="color:#8B1A2A;"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1" style="border-color:#f5e8ea;"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2" style="font-size:0.82rem;color:#dc2626;">
                                    <i class="bi bi-box-arrow-left"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>
</header>
@endauth
