<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-2">
        <i class="fa fa-bars sidebar-toggle-icon"></i>
    </button>

    <h2 class="navbar-title mt-2 text-wargaku font-weight-bold">@yield('text-welcome')</h2>


    @if (isset($page) && $page === 'data-penduduk')
        @role('admin')
        <div class="search-wrapper-rw w-100">
            <form class="navbar-search w-100" method="GET" action="{{ route('penduduk.index') }}">
                {{-- Hidden input untuk tetap membawa filter wilayah --}}
                <input type="hidden" name="daerah_id" value="{{ request('daerah_id') }}">
                <input type="hidden" name="rw_id" value="{{ request('rw_id') }}">
                <input type="hidden" name="rt_id" value="{{ request('rt_id') }}">

                <div class="input-group input-group-sm search-group">
                    <input type="text" name="search" class="form-control bg-light border-0 small"
                        placeholder="Cari nama penduduk..." value="{{ request('search') }}"
                        {{ (!request('daerah_id') || !request('rw_id') || !request('rt_id')) ? 'disabled' : '' }}>
                    <div class="input-group-append">
                        <button class="btn btn-wargaku" type="submit" {{ (!request('daerah_id') || !request('rw_id') || !request('rt_id')) ? 'disabled' : '' }}>
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            @if (!request('daerah_id') || !request('rw_id') || !request('rt_id'))
                <small class="text-danger search-warning d-block mt-1">* Pilih wilayah terlebih dahulu untuk menggunakan pencarian.</small>
            @endif
        </div>
        @endrole

        @role('rw')
            <div class="search-wrapper-rw w-100">
                <form class="navbar-search w-100" method="GET" action="{{ route('penduduk.index') }}">
                    <input type="hidden" name="daerah_id" value="{{ auth()->user()->daerah_id }}">
                    <input type="hidden" name="rw_id" value="{{ auth()->user()->rw_id }}">
                    <input type="hidden" name="rt_id" value="{{ request('rt_id') }}">

                    <div class="input-group input-group-sm search-group">
                        <input type="text" name="search" class="form-control bg-light border-0 small"
                            placeholder="Cari nama penduduk..." value="{{ request('search') }}"
                            {{ !request('rt_id') ? 'disabled' : '' }}>
                        <div class="input-group-append">
                            <button class="btn btn-wargaku" type="submit" {{ !request('rt_id') ? 'disabled' : '' }}>
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

                @if (!request('rt_id'))
                    <small class="text-danger search-warning d-block mt-1">* Pilih RT terlebih dahulu untuk menggunakan pencarian.</small>
                @endif
            </div>
        @endrole

        @role('rt')
            <div class="search-wrapper-rw w-100">
                <form class="navbar-search w-100" method="GET" action="{{ route('penduduk.index') }}">
                    {{-- Hidden input dari data user --}}
                    <input type="hidden" name="daerah_id" value="{{ auth()->user()->daerah_id }}">
                    <input type="hidden" name="rw_id" value="{{ auth()->user()->rw_id }}">
                    <input type="hidden" name="rt_id" value="{{ auth()->user()->rt_id }}">

                    <div class="input-group input-group-sm search-group">
                        <input type="text" name="search" class="form-control bg-light border-0 small"
                            placeholder="Cari nama penduduk..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-wargaku" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endrole
    @endif

    <!-- Topbar Settings -->
    @if (isset($page) && $page === 'pengaturan')
        <div class="py-3  text-wargaku">
            <h5 class="navbar-title m-0 font-weight-bold">Ubah Pengaturan Profil</h5>
        </div>
    @endif

    @if (isset($page) && $page === 'catatan aktivitas')
        <div class="py-3  text-wargaku">
            <h5 class="navbar-title m-0 font-weight-bold">Catatan Aktivitas</h5>
        </div>
    @endif

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Alerts -->
        {{-- <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 12, 2019</div>
                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-donate text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 7, 2019</div>
                        $290.29 has been deposited into your account!
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 2, 2019</div>
                        Spending Alert: We've noticed unusually high spending for your account.
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
            </div>
        </li> --}}

        <!-- Nav Item - Messages -->
        {{-- <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                    Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{{ asset('assets2/img/undraw_profile_1.svg') }}"
                            alt="...">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                            problem I've been having.</div>
                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{{ asset('assets2/img/undraw_profile_1.svg') }}"
                            alt="...">
                        <div class="status-indicator"></div>
                    </div>
                    <div>
                        <div class="text-truncate">I have the photos that you ordered last month, how
                            would you like them sent to you?</div>
                        <div class="small text-gray-500">Jae Chun · 1d</div>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{{ asset('assets2/img/undraw_profile_1.svg') }}"
                            alt="...">
                        <div class="status-indicator bg-warning"></div>
                    </div>
                    <div>
                        <div class="text-truncate">Last month's report looks great, I am very happy with
                            the progress so far, keep up the good work!</div>
                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                            alt="...">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div>
                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                            told me that people say this to all dogs, even if they aren't good...</div>
                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
            </div>
        </li> --}}

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        {{-- <li class="nav-item dropdown no-arrow"> --}}
        <li class="navbar-user-profile ml-auto">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                <img class="img-profile rounded-circle"
                    src="{{ asset('assets2/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>

            <!-- Logout Modal-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Apakah kamu yakin ingin keluar?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Pilih “Logout” di bawah ini jika ingin mengakhiri kerja kamu hari ini.
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            
                            <!-- Form logout langsung di dalam modal -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-wargaku">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </li>

    </ul>

</nav>