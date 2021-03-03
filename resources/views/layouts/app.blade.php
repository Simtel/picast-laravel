<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Хостинг изображений A&S Tech - @yield('title')</title>
    <meta name="description"
          content="Быстрый легкий хостинг картинок, с подержкой загрузки выделенной области рабочего стола, и самое главное все это без рекламы!">
    <meta name="viewport" content="width=device-width">
    <link rel="Shortcut Icon" href="/Hopstarter-Soft-Scraps-Image-JPEG.ico" type="image/x-icon"/>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Хостинг изображений</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?=(URL::current() === URL::route('home')) ? 'active' : '';?>">
                <a class="nav-link" href="<?=URL::route('home');?>">Главная</a>
            </li>
            <li class="nav-item <?=(URL::current() === URL::route('gallery')) ? 'active' : '';?>">
                <a class="nav-link" href="<?=URL::route('gallery');?>">Последние изображения</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">В базе {{App\Models\Images::getCountAvaliable()}}</a>
            </li>
            @if (Auth::check())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{Auth::user()->name}}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{route('personal')}}">Кабинет</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{route('logout')}}">Выйти</a>
                    </div>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{route('login')}}">Войти</a>
                </li>
            @endif
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Поиск" aria-label="Поиск">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Поиск</button>
        </form>
    </div>
</nav>
{{----}}
<div class="container">
    @yield('content')
</div>
</body>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
<link rel="stylesheet" href="/css/app.css">
<!-- jQuery and JS bundle w/ Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<script type="text/javascript" src="/js/app.js"></script>
</html>