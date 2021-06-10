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
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('domains.*') ? 'active' : ''}}"
                   href="{{route('domains.index')}}">
                    <span data-feather="file"></span>
                    Домены
                </a>
            </li>
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