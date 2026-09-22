<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', config('app.locale', 'en')) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
</head>
<body class="phpinfo-standalone">
    <main>
        @yield('content')
    </main>
</body>
</html>
