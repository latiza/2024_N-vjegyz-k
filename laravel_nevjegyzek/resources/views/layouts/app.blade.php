<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Névjegyek App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <h1>Laravel Névjegyek App</h1>
        <nav>
            <a href="{{ route('nevjegyek.index') }}">Névjegyek</a>
            <!-- Egyéb navigációs linkek -->
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Névjegyek App</p>
    </footer>
</body>
</html>
