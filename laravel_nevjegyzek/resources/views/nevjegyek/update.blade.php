@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Névjegy szerkesztése</h1>

        {{-- Hibák megjelenítése --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Névjegy szerkesztési űrlap --}}
        <form action="{{ route('admin.nevjegyek.update', $nevjegy->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Név mező --}}
            <div class="form-group">
                <label for="nev">Név</label>
                <input type="text" name="nev" id="nev" class="form-control" value="{{ old('nev', $nevjegy->nev) }}" required>
            </div>

            {{-- Cégnév mező --}}
            <div class="form-group">
                <label for="cegnev">Cégnév</label>
                <input type="text" name="cegnev" id="cegnev" class="form-control" value="{{ old('cegnev', $nevjegy->cegnev) }}">
            </div>

            {{-- Mobil mező --}}
            <div class="form-group">
                <label for="mobil">Mobil</label>
                <input type="text" name="mobil" id="mobil" class="form-control" value="{{ old('mobil', $nevjegy->mobil) }}">
            </div>

            {{-- E-mail mező --}}
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $nevjegy->email) }}">
            </div>

            {{-- Kép megjelenítése és feltöltési mező --}}
            <div class="form-group">
                <label>Jelenlegi Fotó</label>
                <div>
                    <img src="{{ asset('storage/kepek/' . $nevjegy->foto) }}" alt="Fotó" width="150">
                </div>
            </div>

            <div class="form-group">
                <label for="foto">Új Fotó (ha módosítani szeretnéd)</label>
                <input type="file" name="foto" id="foto" class="form-control">
            </div>

            {{-- Mentés gomb --}}
            <button type="submit" class="btn btn-primary">Mentés</button>
        </form>
    </div>
@endsection
