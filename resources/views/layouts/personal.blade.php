<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'])
    
    <!-- Additional CSS Files (copied to public/css) -->
    @if(file_exists(public_path('css/dashboard.css')))
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endif
    @if(file_exists(public_path('css/sort-arrows.css')))
        <link rel="stylesheet" href="{{ asset('css/sort-arrows.css') }}">
    @endif
    @if(file_exists(public_path('css/gallery.css')))
        <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    @endif
</head>
<body>
    @include('personal.sidebars.sidebar')

    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow navbar-custom">
        <button class="navbar-toggler d-lg-none collapsed toggle-sidebar" type="button">
            <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand mr-0 px-3" href="/personal">Picast</a>

        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="{{route('logout')}}">
                    <i class="fa fa-sign-out-alt mr-1"></i>Выйти
                </a>
            </li>
        </ul>
    </nav>

    <main class="content-wrapper">
        <div class="main-content">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
    @yield('scripts')
</body>
</html>