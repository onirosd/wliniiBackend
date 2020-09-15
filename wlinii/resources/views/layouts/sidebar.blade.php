<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <a href="{{ url('/dashboard') }}">
            ADMINISTRADOR DE WLINII
            <!-- <img src="images/icon/logo.png" alt="Cool Admin" /> -->
        </a>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <li class="{{ (request()->is('dashboard')) ? 'active' : '' }}">
                    <a class="js-arrow" href="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Solicitudes
                    </a>
                </li>
                <li class="{{ (request()->is('news*')) ? 'active' : '' }}">
                    <a href="news">
                        <i class="fas fa-chart-bar"></i>
                        Noticias
                    </a>
                </li>

                <li class="{{ (request()->is('users*')) ? 'active' : '' }}">
                    <a href="users">
                        <i class="fas fa-chart-bar"></i>
                        Usuarios
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>