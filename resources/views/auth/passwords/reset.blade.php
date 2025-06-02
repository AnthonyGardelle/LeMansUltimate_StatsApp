@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
    <div class="content" id="content">
        <h1>Réinitialiser le mot de passe</h1>
        <div class="form-container reset-password-form with-deco">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ old('email', $email) }}">

                <input type="password" name="password" placeholder="Nouveau mot de passe" required>
                <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required>

                <button type="submit">Réinitialiser</button>
            </form>
        </div>
    </div>
@endsection