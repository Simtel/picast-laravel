<!DOCTYPE html>
<html lang="ru" class="landing-page">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'A&S Tech')</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="landing-page auth-page">
@include('partials.tech-background')

<header class="site-nav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">A<span>&amp;</span>S</span>
            <span class="brand-name">Tech</span>
        </a>

        <a href="{{ route('home') }}" class="nav-cta">
            <i class="fa-solid fa-arrow-left"></i> На главную
        </a>
    </div>
</header>

<main class="auth-main">
    <div class="auth-shell @yield('shell-class')">
        @yield('content')
    </div>
</main>

<footer class="auth-footer">
    <p>&copy; {{ date('Y') }} A&amp;S Tech · Домены · Медиа · Турниры · AI · Инструменты</p>
</footer>
</body>
</html>
