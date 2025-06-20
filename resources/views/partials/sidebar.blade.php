<ul class="navbar-nav bg-wargaku sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="d-flex align-items-center justify-content-center" href="index.html">
        <div class="my-3">
            <img src="{{ asset('assets2/img/wargaku_white_logo.png') }}" alt="Logo" class="img-fluid sidebar-logo" width="100">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    @php
        $userRole = Auth::user()->getRoleNames()->first() ?? 'guest';

        $dashboardRoute = match($userRole) {
            'admin' => route('admin.dashboard'),
            'rw'    => route('rw.dashboard'),
            'rt'    => route('rt.dashboard'),
            default => '#',
        };

        // $userRole = Auth::user()->role->name;
        // $dashboardRoute = match($userRole) {
        //     'admin' => route('admin.dashboard'),
        //     'rw'    => route('rw.dashboard'),
        //     'rt'    => route('rt.dashboard'),
        //     default => '#',
        // };
    @endphp

    <li class="nav-item">
        <a class="nav-link {{ Route::is('admin.dashboard') || Route::is('rw.dashboard') || Route::is('rt.dashboard') ? 'active' : '' }}" href="{{ $dashboardRoute }}">
            <i class="fas fa-fw fa-house {{ Route::is('admin.dashboard') || Route::is('rw.dashboard') || Route::is('rt.dashboard') ? 'text-white fw-bold' : '' }}"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    {{-- <div class="sidebar-heading">
        Data Charts
    </div> --}}

    <!-- Nav Item - Charts -->
    <li class="nav-item {{ Route::is('penduduk.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('penduduk.index') }}">
            <i class="fas fa-fw fa-chart-pie"></i>
            <span>Data Penduduk</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Charts -->
    <li class="nav-item {{ Route::is('settings.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('settings.index') }}">
            <i class="fas fa-fw fa-gear"></i>
            <span>Pengaturan</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Tables -->
    <li class="nav-item  {{ Route::is('activity.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('activity.index') }}">
            <i class="fas fa-fw fa-address-book"></i>
            <span>Catatan Aktivitas</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>