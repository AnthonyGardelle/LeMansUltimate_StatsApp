<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
</head>

<body>
    <header>
        @include('partials.navbar')
        @if($errors->any())
            <div id="error-popup">
                <div id="popup-header">
                    <h3>Erreur(s) détectée(s) 🛑</h3>
                    <span id="close-popup" onclick="closeErrorPopup()">&times;</span>
                </div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div id="success-popup">
                <div id="popup-header">
                    <h3>Succès ! 🎉</h3>
                    <span id="close-popup" onclick="closeSuccessPopup()">&times;</span>
                </div>
                <p>{{ session('success') }}</p>
            </div>
        @endif
    </header>

    <main>
        @yield('content')
        <div id="logout-popup" class="popup" onclick="closePopupOnClickOutside(event)">
            <div class="popup-content with-deco">
                <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
                <div class="pop-up-buttons">
                    <button onclick="confirmLogout()">Oui</button>
                    <button onclick="closePopup()">Non</button>
                </div>
            </div>
        </div>
        <div id="language-selector" onclick="toggleLanguageMenu()">
            <div class="selected-language">
                @if (App::getLocale() == 'fr')
                    <img src="{{ asset('images/fr.png') }}" alt="French Flag">
                @else
                    <img src="{{ asset('images/en.png') }}" alt="English Flag">
                @endif
                <span class="arrow">&#9662;</span>
            </div>
            <ul class="language-menu">
                <li>
                    <a href="locale/en">
                        <img src="{{ asset('images/en.png') }}" alt="English Flag">
                        @lang('message.en')
                    </a>
                </li>
                <li>
                    <a href="locale/fr">
                        <img src="{{ asset('images/fr.png') }}" alt="French Flag">
                        @lang('message.fr')
                    </a>
                </li>
            </ul>
        </div>
    </main>
</body>

</html>