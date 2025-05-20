@extends('layouts.app')

@section('title', __('message.title.home'))

@section('content')
    <div class="content" id="content">
        <h1>@lang('message.h1.home')</h1>
        <p id="prevent">This website is an unofficial application, not affiliated with Motorsport Games or Le Mans Ultimate. All
            trademarks
            mentioned are the property of their respective owners.</p>
    </div>
@endsection