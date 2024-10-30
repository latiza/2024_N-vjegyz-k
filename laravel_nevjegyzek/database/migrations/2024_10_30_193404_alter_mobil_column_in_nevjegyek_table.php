<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMobilColumnInNevjegyekTable extends Migration
{
    public function up()
    {
        Schema::table('nevjegyek', function (Blueprint $table) {
            $table->string('mobil', 20)->change(); // Növeljük a hosszát 20-ra
        });
    }

    public function down()
    {
        Schema::table('nevjegyek', function (Blueprint $table) {
            $table->string('mobil', 13)->change(); // Visszaállítjuk az eredeti hosszra
        });
    }
}
