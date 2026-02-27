<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HOUSEHOLD BUDGET')</title>
</head>
<body>
    @include('components.header')

    <main>
        @yield('content')
    </main>
</body>
</html>