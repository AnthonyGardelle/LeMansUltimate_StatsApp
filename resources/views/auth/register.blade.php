@extends('layouts.app')

@section('title', __('message.title.register'))

@section('content')
    <div class="content" id="content">
        <h1>@lang('message.h1.register')</h1>

        <div class="form-container register-form with-deco">
            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="names_fields">
                    <input type="text" name="first_name" placeholder="Prénom" required value="{{ old('first_name') }}">
                    <input type="text" name="name" placeholder="Nom" required value="{{ old('name') }}">
                </div>
                <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <div id="register-file-upload-container">
                    <label for="file-upload">
                        Sélectionner une photo de profil
                    </label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
                        id="file-upload">
                    <span id="file-name">Aucun fichier choisi</span>
                </div>
                <button type="submit">S'inscrire</button>
            </form>
        </div>
    </div>
@endsection