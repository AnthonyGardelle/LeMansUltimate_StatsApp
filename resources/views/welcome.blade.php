@extends('layouts.app')

@section('title', 'Le Mans Ultimate Stats App - Accueil')

@section('content')
    <h1>Bienvenue sur Le Mans Ultimate Stats App</h1>
    <form action="{{ route('updatePath') }}" method="POST">
        @csrf
        <label for="results_path">Chemin des résultats :</label>
        <input type="text" name="results_path" value="{{ session('results_path', config('app.results_path')) }}" required>
        <button type="submit">Enregistrer</button>
    </form>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
@endsection