<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'HOUSEHOLD BUDGET' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    @include('components.header-app')

    <main>
        {{ $slot }}
    </main>

</body>
</html>