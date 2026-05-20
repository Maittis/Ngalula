<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url={{ route('register.customer') }}">
    <title>Redirecting...</title>
</head>
<body>
    <p>Redirecting to <a href="{{ route('register.customer') }}">Customer Registration</a>...</p>
    <script>window.location.href = "{{ route('register.customer') }}";</script>
</body>
</html>
