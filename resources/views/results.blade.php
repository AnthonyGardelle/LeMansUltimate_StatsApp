@extends('layouts.app')

@section('title', __('message.title.result'))

@section('content')
    <div class="content" id="content">
        <h1>@lang('message.h1.result')</h1>
        @if(isset($results) && $results->isNotEmpty())
            <table id="results-table" class="with-deco">
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
                        <td>
                            @if ($results->currentPage() > 10)
                                <a href="{{ $results->url($results->currentPage() - 10) }}#content" class="page-link">
                                    <img class="reverseY" src="{{ Vite::asset('resources/images/icons8-double-right-96.png') }}"
                                        alt="">
                                </a>
                            @endif
                        </td>
                        <td>
                            @if (!$results->onFirstPage())
                                <a href="{{ $results->previousPageUrl() }}#content"><img class="reverseY"
                                        src="{{ Vite::asset('resources/images/icons8-right-arrow-96.png') }}" alt=""></a>
                            @endif
                        </td>
                        <td>
                            <div class="pagination-links">
                                <p>Page {{ $results->currentPage() }} sur {{ $results->lastPage() }}</p>
                                @php
                                    $start = max(1, $results->currentPage() - 2);
                                    $end = min($results->lastPage(), $results->currentPage() + 2);
                                @endphp
                                @if ($start > 1)
                                    <a href="{{ $results->url(1) }}#content" class="page-link">1</a>
                                    @if ($start > 2)
                                        <span class="dots">...</span>
                                    @endif
                                @endif
                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $results->currentPage())
                                        <span class="current-page">{{ $page }}</span>
                                    @else
                                        <a href="{{ $results->url($page) }}#content" class="page-link">{{ $page }}</a>
                                    @endif
                                @endfor
                                @if ($end < $results->lastPage())
                                    @if ($end < $results->lastPage() - 1)
                                        <span class="dots">...</span>
                                    @endif
                                    <a href="{{ $results->url($results->lastPage()) }}#content"
                                        class="page-link">{{ $results->lastPage() }}</a>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if (!$results->onLastPage())
                                <a href="{{ $results->nextPageUrl() }}#content"><img
                                        src="{{ Vite::asset('resources/images/icons8-right-arrow-96.png') }}" alt=""></a>
                            @endif
                        </td>
                        <td>
                            @if ($results->currentPage() + 10 <= $results->lastPage())
                                <a href="{{ $results->url($results->currentPage() + 10) }}#content" class="page-link">
                                    <img src="{{ Vite::asset('resources/images/icons8-double-right-96.png') }}" alt="">
                                </a>
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