@extends('layouts.app')

@section('title', 'Le Mans Ultimate Stats App - Results')

@section('content')
    <div class="content" id="content">
        <h1>Les Resultats</h1>
        <!-- Affichage du message d'erreur -->
        @if(session('error'))
            <div style="color: red; font-weight: bold;">
                {{ session('error') }}
            </div>
        @elseif(isset($results) && $results->isNotEmpty())
            <table class="with-deco">
                <thead>
                    <tr>
                        <th>
                            <p>Sesion</p>
                        </th>
                        <th>
                            <p>Circuit</p>
                        </th>
                        <th>
                            <p>Date de Début</p>
                        </th>
                        <th>
                            <p>Nombre de pilotes</p>
                        </th>
                        <th>
                            <p>Durée (minutes)</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $result)
                        <tr>
                            <td>{{ $result->type }}</td>
                            <td>{{ $result->track }}</td>
                            <td>{{ $result->starting_at }}</td>
                            <td>{{ $result->nb_drivers }}</td>
                            <td>{{ $result->duration }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            @if (!$results->onFirstPage())
                                <a href="{{ $results->previousPageUrl() }}#content"><img class="reverseY"
                                        src="{{ asset('images/icons8-right-arrow-96.png') }}" alt=""></a>
                            @endif
                        </td>
                        <td>
                            Page {{ $results->currentPage() }} sur {{ $results->lastPage() }}
                        </td>
                        <td colspan="2">
                            @if (!$results->onLastPage())
                                <a href="{{ $results->nextPageUrl() }}#content"><img
                                        src="{{ asset('images/icons8-right-arrow-96.png') }}" alt=""></a>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p>Aucun résultat disponible.</p>
        @endif
    </div>
@endsection