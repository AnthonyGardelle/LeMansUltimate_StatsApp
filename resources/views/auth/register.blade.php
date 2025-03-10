@extends('layouts.app')

@section('title', 'Le Mans Ultimate Stats App - Register')

@section('content')
    <div class="content" id="content">
        <h1>S'inscrire</h1>

        <div class="form-container register-form with-deco">
            <form action="{{ url('register') }}" method="POST">
                @csrf
                <div class="names_fields">
                    <input type="text" name="first_name" placeholder="Prénom" required>
                    <input type="text" name="name" placeholder="Nom" required value="{{ old('name') }}">
                </div>
                <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">S'inscrire</button>

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