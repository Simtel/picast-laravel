<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            @can('invite user')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('personal') ? 'active' : ''}}" href="{{route('personal')}}">
                        <span data-feather="home"></span>
                        Участники <span class="sr-only">(current)</span>
                    </a>
                </li>
            @endcan
            @can('domains')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('domains.*') ? 'active' : ''}}"
                       href="{{route('domains.index')}}">
                        <span data-feather="file"></span>
                        Домены
                    </a>
                </li>
            @endcan
            @can('edit prices')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('prices.*') ? 'active' : ''}}"
                       href="{{route('prices.index')}}">
                        <span data-feather="file"></span>
                        Мониторинг цен
                    </a>
                </li>
            @endcan
            @can('edit images')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('images.*') ? 'active' : ''}}"
                       href="{{route('images.index')}}">
                        <span data-feather="file"></span>
                        Изображения
                    </a>
                </li>
            @endcan
            @can('edit youtube')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('youtube.*') ? 'active' : ''}}"
                       href="{{route('youtube.index')}}">
                        <span data-feather="file"></span>
                        YouTube Videos
                    </a>
                </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('settings') ? 'active' : ''}}"
                   href="{{route('settings')}}">
                    <span data-feather="file"></span>
                    Настройки
                </a>
            </li>
        </ul>


    </div>
</nav>