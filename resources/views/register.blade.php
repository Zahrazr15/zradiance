<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Ecommerce</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>

<body>

    <div class="auth-page">

        <div class="auth-card">

        <div class="auth-logo"><x-logo :size="44" /></div>

        <h1>Register</h1>

            <p class="subtitle">
                Buat akun baru untuk melanjutkan
            </p>

            @if ($errors->any())
                <div class="error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">

                @csrf

                <div class="form-group">
                    <label for="name">Nama</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                    >
                </div>

                <button type="submit" class="auth-button">
                    Register
                </button>

            </form>

            <div class="auth-link">
                Sudah punya akun?
                <a href="{{ route('login') }}">Login</a>
            </div>

        </div>

    </div>

</body>

</html>