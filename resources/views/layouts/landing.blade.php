<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.head')
    <title>@yield('title', 'Beauty Store')</title>
</head>
<body>
    <x-navbar mode="landing" />

    @yield('content')

    <x-footer mode="landing" />

    @stack('scripts')
</body>
</html>
