<nav id="sidebarMenu" class="sidebar">
    <div class="sidebar-header">
        <a href="/personal" class="sidebar-brand">
            <i class="fa fa-cube"></i>
            <span>Picast</span>
        </a>
    </div>
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            @can('invite user')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('personal') ? 'active' : ''}}" href="{{route('personal')}}">
                        <i class="fa fa-home nav-icon"></i>
                        <span>Участники</span>
                    </a>
                </li>
            @endcan
            @can('domains')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('domains.*') ? 'active' : ''}}"
                       href="{{route('domains.index')}}">
                        <i class="fa fa-globe nav-icon"></i>
                        <span>Домены</span>
                    </a>
                </li>
            @endcan
            @can('edit prices')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('prices.*') ? 'active' : ''}}"
                       href="{{route('prices.index')}}">
                        <i class="fa fa-tags nav-icon"></i>
                        <span>Мониторинг цен</span>
                    </a>
                </li>
            @endcan
            @can('edit images')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('images.*') ? 'active' : ''}}"
                       href="{{route('images.index')}}">
                        <i class="fa fa-image nav-icon"></i>
                        <span>Изображения</span>
                    </a>
                </li>
            @endcan
            @can('edit youtube')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('youtube.*') ? 'active' : ''}}"
                       href="{{route('youtube.index')}}">
                        <i class="fa fa-youtube nav-icon"></i>
                        <span>YouTube Videos</span>
                    </a>
                </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('chadgpt.*') ? 'active' : ''}}"
                   href="{{route('chadgpt.index')}}">
                    <i class="fa fa-comments nav-icon"></i>
                    <span>ChadGPT Chat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{request()->routeIs('settings') ? 'active' : ''}}"
                   href="{{route('settings')}}">
                    <i class="fa fa-cog nav-icon"></i>
                    <span>Настройки</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fa fa-user-circle user-avatar"></i>
            <span class="user-name">{{ auth()->user()->name }}</span>
        </div>
    </div>
</nav>