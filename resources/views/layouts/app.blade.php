<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" ng-app="OpenITService">
<head>
    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('layouts.styles')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body class="tm-background">
<nav class="tm-navbar uk-navbar uk-navbar-attached">
    <div class="uk-container uk-container-center">
        <a class="uk-navbar-brand uk-hidden-small" href="/">
            <img class="uk-margin uk-margin-remove" src="/img/logo_tr.png" title="{{config('app.name')}}"
                 alt="{{config('app.name')}}">
        </a>
        <ul class="uk-navbar-nav uk-hidden-small uk-float-right">
            @if (Auth::guest())
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Register</a></li>
            @else
                <li class="uk-parent" data-uk-dropdown="">
                    <a href="">{{ Auth::user()->name }}
                        <i class="uk-icon-caret-down"></i>
                    </a>
                    <div class="uk-dropdown uk-dropdown-navbar uk-dropdown-autoflip uk-dropdown-bottom">
                        <ul class="uk-nav uk-nav-navbar">
                            <li><a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>

                </li>
            @endif
        </ul>
        <a href="#tm-offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas=""></a>
        <div class="uk-navbar-brand uk-navbar-center uk-visible-small">
            <img src="/img/logo_tr.png" title="{{config('app.name')}}" alt="{{config('app.name')}}">
        </div>
    </div>
    @include('layouts.preload')
</nav>
<div class="tm-middle">
    <div class="uk-container uk-container-center">
        <div class="uk-grid" data-uk-grid-margin="">
            @if (!Auth::guest() && request()->path()!='/')
                @include('layouts.sidebar')
            @endif
            <div class="tm-main @if (!Auth::guest()) uk-width-medium-4-5 @else  uk-width-medium-1-1 @endif ">
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

</body>
@include('layouts.scripts')
</html>
