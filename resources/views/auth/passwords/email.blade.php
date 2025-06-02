@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="content" id="content">
        <h1>Mot de passe oublié</h1>
        <div class="form-container forgotten-password-form with-deco">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">Envoyer le lien de réinitialisation</button>
            </form>
        </div>
    </div>
@endsection