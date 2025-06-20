@extends('layouts.master')

@section('page', 'data-penduduk')

@section('title', 'Data Penduduk')

@section('content')

    <style>
        /* Untuk mobile: max-width 576px */
        @media (max-width: 575.98px) {
            h5 {
                font-size: 1rem;
            }

            .header-buttons-mobile-container {
                margin-top: 1rem;
            }

            .header-buttons-mobile {
                display: flex;
                justify-content: space-between;
                width: 100%;
                gap: 0.5rem;
            }

            .header-buttons-mobile .btn {
                flex: 1;
                font-size: 0.75rem;
                padding: 0.4rem 0.5rem;
            }

            .form-control,
            select,
            .btn {
                font-size: 0.7rem;
                padding: 0.375rem 0.5rem;
            }

            label {
                font-size: 0.8rem;
            }

            .filter-aktif-wrapper {
                font-size: 0.85rem;
            }

            .filter-aktif-header {
                flex-direction: row;
                justify-content: space-between;
            }

            .filter-aktif-header strong {
                font-size: 0.85rem;
            }

            .filter-aktif-header .btn {
                font-size: 0.7rem;
                padding: 0.3rem 0.5rem;
                margin: 0 !important;
                margin bottom: ;
            }

            .filter-aktif-content {
                display: flex;
                flex-wrap: wrap;
                gap: 0.4rem;
            }

            .filter-aktif-content .badge {
                font-size: 0.75rem;
                padding: 0.4rem 0.5rem;
                margin-top: 0.5rem;
            }

            .btn i {
                font-size: 0.75rem;
            }

            .table {
                font-size: 0.7rem;
            }

            .alert {
                font-size: 0.75rem;
            }

            .card-body {
                padding: 0.75rem;
            }

            .form-inline .form-control {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            .d-flex.bd-highlight > .bd-highlight {
                flex: 0 0 100%;
                text-align: left;
            }

            .btn + .btn {
                margin-left: 0.5rem;
            }

            .table-responsive {
                display: none; /* Sembunyikan tabel di mobile */
            }

            .mobile-card {
                display: block;
            }

            .mobile-card .card {
                margin-bottom: 1rem;
                padding: 1rem;
                border: 1px solid #ddd;
                border-radius: 10px;
                background-color: #f8f9fa;
                /* background-color: #4e73df; */
            }

            .mobile-card .card-title {
                font-weight: bold;
                font-size: 1rem;
            }

            .mobile-card .card-text small {
                display: block;
                color: #6c757d;
                /* color: #fff; */
            }

            .mobile-card .btn-edit-penduduk {
                background-color: #20c997;
                color: white;
                border: none;
            }

            .mobile-card .btn-edit-penduduk:hover {
                background-color: #17b48a;
            }

            .mobile-card .btn-delete-penduduk {
                background-color: #e74a3b;
                color: white;
                border: none;
            }

            .mobile-card .btn-delete-penduduk:hover {
                background-color: #c0392b;
            }

            .modal-dialog {
                max-width: 70%; /* modal lebih ramping dari lebar layar */
                margin: 1.5rem auto;
            }

            .modal-content {
                max-height: 90vh;
                overflow-y: auto;
                border-radius: 0.75rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                background-color: #29B6A5; /* warna hijau toska */
                color: white;
            }

            .modal-title {
                font-size: 1rem;
                font-weight: 600;
            }

            .close {
                font-size: 1.25rem;
                color: white;
            }

            .modal-body label {
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }

            .modal-body select.form-control {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                height: calc(1.5em + 0.75rem + 2px);
            }

            .modal-body .form-group {
                margin-bottom: 0.75rem;
            }

            body.modal-open {
                overflow: hidden;
            }
        }

        @media (min-width: 576px) {
            .filter-aktif-header {
                flex-direction: row;
            }

            .filter-aktif-content {
                display: inline;
            }

            .filter-aktif-content .badge {
                margin-right: 0.5rem;
            }

            .mobile-card {
                display: none;
            }
        }

        /* Untuk small screen: 576px – 768px */
        @media (min-width: 576px) and (max-width: 767.98px) {
            h5 {
                font-size: 1.1rem;
            }

            .form-control,
            select,
            .btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.75rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .alert {
                font-size: 0.8rem;
            }

            .form-inline .form-control {
                width: 48%;
                margin-bottom: 0.5rem;
            }
        }

        /* Untuk medium screen: 768px – 991.98px */
        @media (min-width: 768px) and (max-width: 991.98px) {
            h5 {
                font-size: 1.25rem;
            }

            .form-control,
            select,
            .btn {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }

            .table {
                font-size: 0.8rem;
            }

            .alert {
                font-size: 0.85rem;
            }

            .form-inline .form-control {
                width: 30%;
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-body">

                {{-- Header Berdasarkan Role --}}
                @hasanyrole('admin|super-admin')
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        {{-- Judul --}}
                        <h5 class="m-0 font-weight-bold text-wargaku">Data Penduduk</h5>

                        {{-- Tombol versi desktop --}}
                        <div class="d-none d-sm-flex">
                            <button id="btnFilterTambahan" type="button" class="btn btn-outline-wargaku mr-2 {{ request('daerah_id') && request('rw_id') && request('rt_id') ? '' : 'd-none' }}" data-toggle="modal" data-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button id="btnTambahPenduduk" class="btn btn-wargaku {{ request('daerah_id') && request('rw_id') && request('rt_id') ? '' : 'd-none' }}">
                                <i class="fas fa-plus"></i> Tambah Penduduk
                            </button>
                        </div>

                        {{-- Tombol versi mobile --}}
                        <div class="header-buttons-mobile-container d-block d-sm-none w-100">
                            @if(request('daerah_id') && request('rw_id') && request('rt_id'))
                            <div class="header-buttons-mobile">
                                <button id="btnFilterTambahanMobile" type="button" class="btn btn-outline-wargaku" data-toggle="modal" data-target="#filterModal">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button id="btnTambahPendudukMobile" class="btn btn-wargaku">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                @endhasanyrole

                @role('rw')
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        {{-- Judul --}}
                        <h5 class="m-0 font-weight-bold text-wargaku">Data Penduduk</h5>

                        {{-- Tombol versi desktop --}}
                        <div class="d-none d-sm-flex">
                            <button id="btnFilterTambahan" type="button" class="btn btn-outline-wargaku mr-2 {{ request('daerah_id') && request('rw_id') && request('rt_id') ? '' : 'd-none' }}" data-toggle="modal" data-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button id="btnTambahPenduduk" class="btn btn-wargaku {{ request('daerah_id') && request('rw_id') && request('rt_id') ? '' : 'd-none' }}">
                                <i class="fas fa-plus"></i> Tambah Penduduk
                            </button>
                        </div>

                        {{-- Tombol versi mobile --}}
                        <div class="header-buttons-mobile-container d-block d-sm-none w-100">
                            @if(request('rt_id'))
                            <div class="header-buttons-mobile">
                                <button id="btnFilterTambahanMobile" type="button" class="btn btn-outline-wargaku" data-toggle="modal" data-target="#filterModal">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button id="btnTambahPendudukMobile" class="btn btn-wargaku">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                @endrole

                @role('rt')
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        {{-- Judul --}}
                        <h5 class="m-0 font-weight-bold text-wargaku">Data Penduduk</h5>

                        {{-- Tombol versi desktop --}}
                        <div class="d-none d-sm-flex">
                            <button id="btnFilterTambahan" type="button" class="btn btn-outline-wargaku mr-2 {{ request('daerah_id') && request('rw_id') && request('rt_id') ? '' : 'd-none' }}" data-toggle="modal" data-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button id="btnTambahPenduduk" class="btn btn-wargaku {{ request('daerah_id') && request('rw_id') && request('rt_id') ? '' : 'd-none' }}">
                                <i class="fas fa-plus"></i> Tambah Penduduk
                            </button>
                        </div>

                        {{-- Tombol versi mobile --}}
                        <div class="header-buttons-mobile-container d-block d-sm-none w-100">
                            <div class="header-buttons-mobile">
                                <button id="btnFilterTambahanMobile" type="button" class="btn btn-outline-wargaku" data-toggle="modal" data-target="#filterModal">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button id="btnTambahPendudukMobile" class="btn btn-wargaku">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                @endrole

                {{-- Notifikasi Hasil Pencarian --}}
                @if(request('search'))
                    <div class="alert alert-info">
                        Menampilkan hasil pencarian untuk: <strong>{{ e(request('search')) }}</strong>
                    </div>
                @endif

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('penduduk.index') }}" id="filterForm" class="mt-0">
                    <div class="form-row">
                        @role('admin')
                            <div class="form-group col-md-3">
                                <label for="daerahInput">Daerah</label>
                                <select name="daerah_id" id="daerahInput" class="form-control">
                                    <option selected disabled value="">Pilih Daerah</option>
                                    @foreach ($daerahList as $daerah)
                                        <option value="{{ $daerah->id }}" {{ $filterAktif['daerah_id'] == $daerah->id ? 'selected' : '' }}>
                                            {{ $daerah->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rwSelect">RW</label>
                                <select name="rw_id" id="rwSelect" class="form-control" data-selected="{{ old('rw_id', request('rw_id')) }}">
                                    <option selected disabled value="">Pilih RW</option>
                                    @foreach($rwList as $rw)
                                        <option value="{{ $rw->id }}" {{ $filterAktif['rw_id'] == $rw->id ? 'selected' : '' }}>{{ $rw->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rtSelect">RT</label>
                                <select name="rt_id" id="rtSelect" class="form-control" data-selected="{{ old('rt_id', request('rt_id')) }}">
                                    <option selected disabled value="">Pilih RT</option>
                                    @foreach($rtList as $rt)
                                        <option value="{{ $rt->id }}" {{ $filterAktif['rt_id'] == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endrole

                        @role('rw')
                            <div class="form-group col-md-3">
                                <label for="rtSelect">RT</label>
                                <select name="rt_id" id="rtSelect" onchange="this.form.submit()" class="form-control" data-selected="{{ old('rt_id', request('rt_id')) }}">
                                    <option selected disabled value="">Pilih RT</option>
                                    @foreach($rtList as $rt)
                                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endrole
                    </div>
                </form>

                @if(request('daerah_id') || request('rw_id') || request('rt_id'))
                    @hasrole('rt')
                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('penduduk.index') }}" class="btn btn-sm btn-outline-danger">Reset Filter</a>
                        </div>
                    @else
                        <div class="alert alert-info filter-aktif-wrapper">
                            {{-- Baris atas --}}
                            <div class="filter-aktif-header d-flex align-items-center flex-wrap mb-2">
                                <strong class="m-0 mr-2">Filter Aktif:</strong>
                                <a href="{{ route('penduduk.index') }}" class="btn btn-sm btn-outline-danger ml-auto mt-2 mt-sm-0">Reset Filter</a>
                            </div>

                            {{-- Badge hasil filter --}}
                            <div class="filter-aktif-content mt-1">
                                @role('admin')
                                    @if(request('daerah_id'))
                                        <span class="badge badge-primary mb-1">Daerah: {{ $daerahList->firstWhere('id', request('daerah_id'))->name ?? 'Tidak ditemukan' }}</span>
                                    @endif
                                    @if(request('rw_id'))
                                        <span class="badge badge-success mb-1">RW: {{ $rwList->firstWhere('id', request('rw_id'))->name ?? 'Tidak ditemukan' }}</span>
                                    @endif
                                    @if(request('rt_id'))
                                        <span class="badge badge-warning mb-1">RT: {{ $rtList->firstWhere('id', request('rt_id'))->name ?? 'Tidak ditemukan' }}</span>
                                    @endif
                                @endrole

                                @role('rw')
                                    @if(request('rt_id'))
                                        <span class="badge badge-warning mb-1">RT: {{ $rtList->firstWhere('id', request('rt_id'))->name ?? 'Tidak ditemukan' }}</span>
                                    @endif
                                @endrole
                            </div>
                        </div>
                    @endhasrole
                @endif

                {{-- Tabel --}}
                @include('partials.table')

            </div>
        </div>
    </div>

    <!-- Modal Filter & Tambah/Edit & Hapus -->
    @include('partials.modal')

@endsection