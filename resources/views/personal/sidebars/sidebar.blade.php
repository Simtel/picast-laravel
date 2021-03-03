<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('personal') ? 'active' : ''}}" href="{{route('personal')}}">
                    <span data-feather="home"></span>
                    Участники <span class="sr-only">(current)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('personal\domains') ? 'active' : ''}}" href="{{route('personal\domains')}}">
                    <span data-feather="file"></span>
                    Домены
                </a>
            </li>
        </ul>


    </div>
</nav>