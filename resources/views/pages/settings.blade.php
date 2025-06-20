@extends('layouts.master')

@section('page', 'pengaturan')

@section('title', 'Pengaturan')

@section('content')
    <style>
        @media (max-width: 576px) {
            /* Ukuran font keseluruhan */
            .container,
            .card-body,
            label,
            .form-control,
            .input-group-text,
            .btn {
                font-size: 0.85rem;
            }

            /* Perkecil input dan button */
            .form-control,
            .input-group-text {
                padding: 0.4rem 0.5rem;
            }

            /* Tombol simpan/ganti password */
            .btn-wargaku {
                font-size: 0.85rem;
                padding: 0.4rem 0.8rem;
            }

            /* Margin antar form biar tidak terlalu renggang */
            .form-group {
                margin-bottom: 0.75rem;
            }

            /* Ukuran ikon mata toggle password */
            .input-group-text i.fas.fa-eye {
                font-size: 0.9rem;
            }

            /* Alert lebih kecil */
            .alert {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
            }

            label[for="new_password_confirmation"] {
                margin-top: 0.75rem; /* atau sesuaikan */
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
    </style>
    
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Menampilkan pesan sukses untuk update profil -->
                @if(session('profile_success'))
                    <div class="alert alert-success">
                        {{ session('profile_success') }}
                    </div>
                @endif

                <!-- Menampilkan pesan error untuk update profil -->
                @if($errors->has('name') || $errors->has('email'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-row">
                        <div class="form-group col-md-6 col-12">
                            <label for="name">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label for="role">Role</label>
                            <input type="text" class="form-control" id="role"
                                value="{{ strtoupper(auth()->user()->getRoleNames()->first()) }}@if(auth()->user()->hasRole('rw')) {{ auth()->user()->rwDetail->name }} @elseif(auth()->user()->hasRole('rt')) {{ auth()->user()->rtDetail->name }} @endif" readonly>
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label for="daerah">Nama Daerah</label>
                            <input type="text" class="form-control" id="daerah"
                                value="{{ auth()->user()->daerah->name ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group text-right mt-3">
                        <button type="submit" class="btn btn-wargaku btn-block btn-sm d-md-inline-block w-100 w-md-auto">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">

                @if(session('password_success'))
                    <div class="alert alert-success">
                        {{ session('password_success') }}
                    </div>
                @endif

                <!-- Menampilkan pesan error untuk ganti password -->
                @if($errors->has('current_password') || $errors->has('new_password'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('settings.updatePassword') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Password Lama -->
                    <div class="form-row">
                        <div class="form-group col-md-6 col-12">
                            <label for="current_password">Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye toggle-password" toggle="#current_password" style="cursor: pointer;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Baru -->
                        <div class="form-group col-md-6 col-12">
                            <label for="new_password">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye toggle-password" toggle="#new_password" style="cursor: pointer;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <label for="new_password_confirmation">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye toggle-password" toggle="#new_password_confirmation" style="cursor: pointer;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>

                    <div class="form-group text-right mt-3">
                        <button type="submit" class="btn btn-wargaku btn-block btn-sm d-md-inline-block w-100 w-md-auto">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection