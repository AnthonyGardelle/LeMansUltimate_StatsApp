@extends('layouts.app')

@section('title', __('message.title.home'))

@section('content')
    <div class="content" id="content">
        <h1>@lang('message.h1.home')</h1>
        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
    </div>
@endsection