<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="{{ asset('js/script.js') }}"></script>
</head>

<body>
    <header>
        @include('partials.navbar')
    </header>

    <main>
        @yield('content')
        <div id="logout-popup" class="popup" onclick="closePopupOnClickOutside(event)">
            <div class="popup-content">
                <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
                <button onclick="confirmLogout()">Oui</button>
                <button onclick="closePopup()">Non</button>
            </div>
        </div>
    </main>
</body>

</html>