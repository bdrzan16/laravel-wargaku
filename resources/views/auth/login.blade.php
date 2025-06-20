<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Login</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link href="{{ asset('assets2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('assets2/css/auth.css') }}">
    </head>

    <body class="login-page">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">

                    <div class="text-center mb-4">
                        <img src="{{ asset('assets2/img/wargaku_green_logo.png') }}" alt="Logo" style="max-width: 180px;">
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                            <span toggle="#password" class="fas fa-eye toggle-password position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-wargaku fw-bold">Login</button>
                        </div>

                    </form>

                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none text-wargaku">Forgot Password</a> | 
                        <a href="{{ route('register') }}" class="text-decoration-none text-wargaku">Create New Account</a>
                    </div>

                </div>
            </div>
        </div>

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

    </body>
</html>