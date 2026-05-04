{{-- Tampilkan Navbar HANYA jika user sudah login --}}
@auth
<header class='mb-3'>
    <nav class="navbar navbar-expand navbar-light">
        <div class="container-fluid">
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="ms-auto d-flex align-items-center">

                    {{-- Dropdown Notifikasi --}}
                    <div class="dropdown me-4">
                        <a class="nav-link bg-white text-dark d-flex align-items-center justify-content-center shadow-sm" href="#" data-bs-toggle="dropdown" style="width: 45px; height: 45px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s ease;">
                            <i class='bi bi-bell fs-5'></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <li><a class="dropdown-item">No new notifications</a></li>
                        </ul>
                    </div>

                    {{-- User Menu --}}
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none">
                            <div class="user-menu d-flex align-items-center bg-white px-3 py-2 shadow-sm" style="border-radius: 50px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                <div class="user-name text-end me-3 d-none d-sm-block">
                                    {{-- Nama User dari Auth --}}
                                    <h6 class="mb-0 text-dark fw-bold fs-6">{{ Auth::user()->name }}</h6>
                                    {{-- Menampilkan Role User --}}
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        {{ Auth::user()->roles->first()->name ?? 'User' }}
                                    </p>
                                </div>
                                <div class="avatar avatar-md border border-2 border-white shadow-sm">
                                    <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Face" style="object-fit: cover;">
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </nav>
</header>
@endauth