@extends('layouts.app')

@section('content')
<h1>Névjegykártyák</h1>

<!-- Keresőmező -->
<form method="GET" action="{{ route('admin.nevjegyek.index') }}">
    <input type="search" id="kifejezes" name="kifejezes" value="{{ $kifejezes }}" placeholder="Keresés...">
    <button type="submit">Keresés</button>
</form>

<!-- Új névjegy hozzáadása és kilépés -->
<p>
    <a href="{{ route('admin.nevjegyek.create') }}">Új névjegy hozzáadása</a> | 
    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Kilépés</a>
</p>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Táblázat -->
<table>
    <thead>
        <tr>
            <th>Fotó</th>
            <th><a href="{{ route('admin.nevjegyek.index', ['rendez' => 'nev']) }}">Név</a></th>
            <th><a href="{{ route('admin.nevjegyek.index', ['rendez' => 'cegnev']) }}">Cégnév</a></th>
            <th><a href="{{ route('admin.nevjegyek.index', ['rendez' => 'mobil']) }}">Mobil</a></th>
            <th><a href="{{ route('admin.nevjegyek.index', ['rendez' => 'email']) }}">E-mail</a></th>
            <th>Műveletek</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($nevjegyek as $nevjegy)
            <tr>
                <td><img src="{{ asset('storage/kepek/' . ($nevjegy->foto ?? 'nincskep.png')) }}" alt="{{ $nevjegy->nev }}" width="50">

                </td>
                <td>{{ $nevjegy->nev }}</td>
                <td>{{ $nevjegy->cegnev }}</td>
                <td>{{ $nevjegy->mobil }}</td>
                <td>{{ $nevjegy->email }}</td>
                <td>
                    <a href="{{ route('admin.nevjegyek.edit', $nevjegy->id) }}">Módosítás</a> |
                    <form action="{{ route('admin.nevjegyek.destroy', $nevjegy->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Biztosan törölni szeretnéd?')">Törlés</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Nincs találat a rendszerben!</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Lapozó -->
{{ $nevjegyek->links() }}
@endsection
