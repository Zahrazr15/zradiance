<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.head')
    @stack('styles')
    <title>@yield('title', 'ZRADIANCE')</title>
</head>
<body>
    <x-navbar mode="user" />

    <main class="user-main">
        @yield('content')
    </main>

    <x-footer mode="user" />

    <x-chatbot />
    <x-flash-popup />

    @stack('scripts')
</body>
</html>
