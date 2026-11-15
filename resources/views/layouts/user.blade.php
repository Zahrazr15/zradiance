
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.head')
    <title>@yield('title', 'Beauty Store')</title>
</head>
<body>
    <x-navbar mode="user" />

    <main class="user-main">
        @yield('content')
    </main>

    <x-footer mode="user" />

    <x-chatbot />
    <x-flash-popup />

    {{-- Memuat script dari halaman yang menggunakan @push('scripts') --}}
    @stack('scripts')
</body>
</html>
