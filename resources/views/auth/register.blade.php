<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Register</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link href="{{ asset('assets2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('assets2/css/auth.css') }}">
    </head>

    <body class="register-page">
        <div class="container py-4 mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-7 col-sm-10">

                    <div class="text-center py-4 py-md-5 mt-md-5">
                        <img src="{{ asset('assets2/img/wargaku_green_logo.png') }}" alt="Logo" class="img-fluid w-100" style="max-width: 140px; height: auto;">
                    </div>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                            <span toggle="#password" class="fas fa-eye toggle-password position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password" required>
                            <span toggle="#password_confirmation" class="fas fa-eye toggle-password position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
                            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option selected disabled value="">Pilih Role</option>
                                <option value="rw">Ketua RW</option>
                                <option value="rt">Ketua RT</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3" id="form-rt">
                            <input type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" placeholder="RT (Contoh: 01)">
                            @error('rt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3" id="form-rw">
                            <input type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" placeholder="RW (Contoh: 07)" required>
                            @error('rw') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <input type="text" name="daerah" class="form-control @error('daerah') is-invalid @enderror" placeholder="Daerah (contoh: Cikudapateuh Dalam)" required>
                            @error('daerah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-wargaku fw-bold py-2">Buat Akun</button>
                        </div>
                    </form>

                    <div class="text-center mt-3 mb-4">
                        <a href="{{ route('login') }}" class="text-decoration-none text-wargaku">Sudah punya akun? Masuk</a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Script Toggle Password -->
        <script>
            document.querySelectorAll('.toggle-password').forEach(function(el) {
                el.addEventListener('click', function() {
                    const input = document.querySelector(this.getAttribute('toggle'));
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            });
        </script>

        <!-- Script Tampilkan/Sembunyikan Form RT -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const roleSelect = document.querySelector('select[name="role"]');
                const rtField = document.getElementById("form-rt");

                function toggleRTField() {
                    if (roleSelect.value === "rw") {
                        rtField.style.display = "none";
                        rtField.querySelector('input').value = "";
                    } else {
                        rtField.style.display = "block";
                    }
                }

                toggleRTField();
                roleSelect.addEventListener("change", toggleRTField);
            });
        </script>
    </body>

</html>