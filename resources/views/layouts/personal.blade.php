<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Local CSS Files -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/sort-arrows.css">
    <link rel="stylesheet" href="/css/gallery.css">
</head>
<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow navbar-custom">
    <button class="navbar-toggler position-absolute d-lg-none collapsed toggle-sidebar" type="button">
        <i class="fas fa-bars"></i>
    </button>
    <a class="navbar-brand mr-0 px-3" href="/personal">Picast</a>

    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="{{route('logout')}}">
                <i class="fas fa-sign-out-alt mr-1"></i>Выйти
            </a>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        @include('personal.sidebars.sidebar')
        <main class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-md-4 py-4">
            @yield('content')
        </main>
    </div>
</div>

<!-- Local JS Files -->
<script src="/js/jquery.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/marked.min.js"></script>
<script src="/js/app.js"></script>
<script>
document.querySelector('.toggle-sidebar')?.addEventListener('click', function() {
    document.getElementById('sidebarMenu')?.classList.toggle('show');
});
</script>
@stack('scripts')
@yield('scripts')
</body>
</html>