<nav>
    <ul>
        <li><a class="nav-button" href="{{ url('/') }}">Accueil</a></li>
        <li><a class="nav-button" href="{{ url('results') }}">Les Résultats</a></li>
        @guest
            <li><a class="nav-button" href="{{ route('show.register') }}">S'inscrire</a></li>
            <li><a class="nav-button" href="{{ route('show.login') }}">Se Connecter</a></li>
        @else
            <li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                    onsubmit="return showLogoutPopup(event)">
                    @csrf

                    <button class="nav-button" type="submit">Se Déconnecter</button>
                </form>
            </li>
        @endguest
    </ul>
</nav>