<nav id="sidebarMenu" class="sidebar">
    <div class="sidebar-header">
        <a href="/personal" class="sidebar-brand">
            <i class="fa fa-cube"></i>
            <span>Picast</span>
        </a>
    </div>
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            @can(\section_permission('dashboard'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('personal') ? 'active' : ''}}" href="{{route('personal')}}">
                        <i class="fa fa-home nav-icon"></i>
                        <span>Участники</span>
                    </a>
                </li>
            @endcan
            @can(\section_permission('domains'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('domains.*') ? 'active' : ''}}"
                       href="{{route('domains.index')}}">
                        <i class="fa fa-globe nav-icon"></i>
                        <span>Домены</span>
                    </a>
                </li>
            @endcan
            @can(\section_permission('images'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('images.*') ? 'active' : ''}}"
                       href="{{route('images.index')}}">
                        <i class="fa fa-image nav-icon"></i>
                        <span>Изображения</span>
                    </a>
                </li>
            @endcan
            @can(\section_permission('youtube'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('youtube.*') ? 'active' : ''}}"
                       href="{{route('youtube.index')}}">
                        <i class="fa fa-youtube nav-icon"></i>
                        <span>YouTube Videos</span>
                    </a>
                </li>
            @endcan
            @can(\section_permission('chadgpt'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('chadgpt.*') ? 'active' : ''}}"
                       href="{{route('chadgpt.index')}}">
                        <i class="fa fa-comments nav-icon"></i>
                        <span>ChadGPT Chat</span>
                    </a>
                </li>
            @endcan
            @can(\section_permission('tournaments'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('tournaments.index') ? 'active' : ''}}"
                       href="{{route('tournaments.index')}}">
                        <i class="fa fa-trophy nav-icon"></i>
                        <span>Турниры</span>
                    </a>
                </li>
            @endcan
            @can(\section_permission('tools'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('tools.index') ? 'active' : ''}}"
                       href="{{route('tools.index')}}">
                        <i class="fa fa-toolbox nav-icon"></i>
                        <span>Инструменты</span>
                    </a>
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <a class="nav-link {{request()->routeIs('tools.barcode.*') ? 'active' : ''}}"
                               href="{{route('tools.barcode.index')}}">
                                <i class="fa fa-qrcode nav-icon"></i>
                                <span>Штрих-коды</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            @can(\section_permission('settings'))
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('settings') ? 'active' : ''}}"
                       href="{{route('settings')}}">
                        <i class="fa fa-cog nav-icon"></i>
                        <span>Настройки</span>
                    </a>
                </li>
            @endcan
            @can('edit user')
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('roles.*') ? 'active' : ''}}"
                       href="{{route('roles.index')}}">
                        <i class="fa fa-user-shield nav-icon"></i>
                        <span>Роли и доступ</span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fa fa-user-circle user-avatar"></i>
            <span class="user-name">{{ auth()->user()->name }}</span>
        </div>
    </div>
</nav>