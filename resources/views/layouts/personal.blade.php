<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Local CSS Files -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/fontawesome.min.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/sort-arrows.css">
    <link rel="stylesheet" href="/css/gallery.css">
</head>
<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="/personal">Главная</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse"
            data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="{{route('logout')}}">Выйти</a>
        </li>
    </ul>
</nav>

{{----}}
<div class="container-fluid">
    <div class="row">
        @include('personal.sidebars.sidebar')
        @yield('content')
    </div>
</div>

<!-- Local JS Files -->
<script src="/js/jquery.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/marked.min.js"></script>
<script src="/js/app.js"></script>
@stack('scripts')
@yield('scripts')
</body>
</html>