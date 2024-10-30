
@extends('layouts.app')

@section('content')
    <h1>Üdvözlünk a Dashboardon, {{ Auth::user()->name }}!</h1>

    @if (Auth::user()->is_admin)
        <div class="admin-menu">
            <h2>Admin Műveletek</h2>
            <ul>
                <li><a href="{{ route('admin.nevjegyek.create') }}">Új névjegy hozzáadása</a></li>
                <li><a href="{{ route('admin.nevjegyek.index') }}">Névjegyek kezelése</a></li>
            </ul>
        </div>
    @endif
@endsection
