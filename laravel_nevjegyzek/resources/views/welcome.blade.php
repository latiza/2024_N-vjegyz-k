<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Névjegykártyák</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <h1>Névjegykártyák</h1>
        <nav>
            <a href="{{ route('login') }}">Bejelentkezés</a>
            <a href="{{ route('register') }}">Regisztráció</a>
        </nav>
    </header>
    @extends('layouts.app')

    @section('content')
    <h1>Névjegykártyák</h1>
    
    <form method="GET" action="{{ route('welcome') }}">
        <input type="search" id="kifejezes" name="kifejezes" placeholder="Keresés..." value="{{ $kifejezes }}">
        <button type="submit">Keresés</button>
    </form>
    
    @if ($nevjegyek->isEmpty())
        <article>
            <h2>Nincs találat a rendszerben!</h2>
        </article>
    @else
        <div class="container">
            @foreach ($nevjegyek as $nevjegy)
                <article>
                    <img src="{{ asset('kepek/' . $nevjegy->foto) }}" alt="{{ $nevjegy->nev }}">
                    <h2>{{ $nevjegy->nev }}</h2>
                    <h3>{{ $nevjegy->cegnev }}</h3>
                    <p>Mobil: <a href="tel:{{ $nevjegy->mobil }}">{{ $nevjegy->mobil }}</a></p>
                    <p>E-mail: <a href="mailto:{{ $nevjegy->email }}">{{ $nevjegy->email }}</a></p>
                </article>
            @endforeach
        </div>
    
        {{ $nevjegyek->links() }} <!-- Lapozó linkek -->
    @endif
    @endsection
    