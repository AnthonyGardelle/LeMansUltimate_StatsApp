@extends('layouts.app')

@section('title', 'Le Mans Ultimate Stats App - Accueil')

@section('content')
    <h1>Bienvenue sur Le Mans Ultimate Stats App</h1>
    <a href="{{ url('results') }}">Voir les résultats</a>
@endsection