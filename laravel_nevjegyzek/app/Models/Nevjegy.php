<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nevjegy extends Model
{
    public $timestamps = false; // Időbélyegek kikapcsolása

    // A tábla neve
    protected $table = 'nevjegyek';

    // A mezők, amelyeket tömegesen ki lehet tölteni
    protected $fillable = ['foto', 'nev', 'cegnev', 'foglalkozas', 'email', 'mobil'];

}
