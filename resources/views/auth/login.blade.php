@extends('layouts.app')

@section('title', __('message.title.login'))

@section('content')
    <div class="content" id="content">
        <h1>@lang('message.h1.login')</h1>

        <div class="form-container login-form with-deco">
            <form action="{{ url('login') }}" method="POST">
                @csrf
                <input class="no-margin-top" type="email" name="email" placeholder="Email" required
                    value="{{ old('email') }}">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <div class="form-actions">
                    <button type="submit">Se connecter</button>
                    <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>
    </div>
@endsection