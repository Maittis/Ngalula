<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url={{ route('login.customer') }}">
    <title>Redirecting...</title>
</head>
<body>
    <p>Redirecting to <a href="{{ route('login.customer') }}">Customer Login</a>...</p>
    <script>window.location.href = "{{ route('login.customer') }}";</script>
</body>
</html>
