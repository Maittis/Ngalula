<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ngalula Wellness Center')</title>
    @yield('styles')
</head>
<body>
    <main>
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
