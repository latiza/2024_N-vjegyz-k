@extends('layouts.app')

@section('content')
<h2>Új névjegy hozzáadása</h2>

<form action="{{ route('admin.nevjegyek.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="nev">Név:</label>
        <input type="text" id="nev" name="nev" required>
    </div>
    <div>
        <label for="cegnev">Cégnév:</label>
        <input type="text" id="cegnev" name="cegnev">
    </div>
    <div>
        <label for="foglalkozas">Foglalkozás:</label>
        <input type="text" id="foglalkozas" name="foglalkozas">
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="mobil">Mobil:</label>
        <input type="text" id="mobil" name="mobil">
    </div>
    <div>
        <label for="foto">Kép feltöltése:</label>
        <input type="file" id="foto" name="foto">
    </div>
    <button type="submit">Mentés</button>
</form>
@endsection
