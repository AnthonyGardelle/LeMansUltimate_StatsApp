@extends('layouts.app')

@section('title', 'Le Mans Ultimate Stats App - Results')

@section('content')
    <h1>Results</h1>
    <table>
        <tr>
            <th>Type</th>
            <th>Track</th>
            <th>Starting At</th>
            <th>Duration</th>
        </tr>
        @foreach ($results as $result)
            <tr>
                <td>{{ $result->type }}</td>
                <td>{{ $result->track }}</td>
                <td>{{ $result->starting_at }}</td>
                <td>{{ $result->duration }}</td>
            </tr>
        @endforeach
    </table>
@endsection