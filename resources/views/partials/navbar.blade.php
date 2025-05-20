<nav>
    <ul>
        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">@lang('message.accueil')</a></li>
        <li><a href="{{ url('results') }}" class="{{ request()->is('results') ? 'active' : '' }}">@lang('message.result')</a></li>
        @guest
            <li><a href="{{ route('show.register') }}"
                    class="{{ request()->is('register') ? 'active' : '' }}">@lang('message.register')</a></li>
            <li><a href="{{ route('show.login') }}" class="{{ request()->is('login') ? 'active' : '' }}">@lang('message.login')</a>
            </li>
        @else
            <li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="return showLogoutPopup(event);">@lang('message.logout')</a>
            </li>
            <li><a id="profil-link" href="{{ route('show.profile') }}"
                    class="{{ request()->is('profile') ? 'active' : '' }}">{{ Auth::user()->first_name }}
                    {{ Auth::user()->name }}<img src="{{ asset('storage/' . Auth::user()->image)}}" alt="">
                </a>
            </li>
        @endguest
    </ul>
</nav>