@extends('layouts.app')

@section('title', 'Le Mans Ultimate Stats App - Login')

@section('content')
    <div class="content" id="content">
        <h1>Se connecter</h1>

        <div class="form-container login-form with-deco">
            <form action="{{ url('login') }}" method="POST">
                @csrf
                <input class="no-margin-top" type="email" name="email" placeholder="Email" required
                    value="{{ old('email') }}">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>

                @if($errors->any())
                    <div>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection