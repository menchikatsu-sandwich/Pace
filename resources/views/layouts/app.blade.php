<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div id="app">
        <nav class="bg-white shadow">
            <div class="container mx-auto px-6 py-3">
                <div class="flex justify-between items-center">
                    <div>
                        <a href="{{ url('/') }}" class="text-gray-800 text-xl font-bold">{{ config('app.name', 'Laravel') }}</a>
                    </div>
                    <div>
                        @guest
                            <a href="{{ route('login') }}" class="text-gray-800 text-sm font-semibold">Login</a>
                            <a href="{{ route('register') }}" class="ml-4 text-gray-800 text-sm font-semibold">Register</a>
                        @else
                            <a href="{{ route('home') }}" class="text-gray-800 text-sm font-semibold">{{ Auth::user()->name }}</a>
                            <a href="{{ route('logout') }}" class="ml-4 text-gray-800 text-sm font-semibold"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>