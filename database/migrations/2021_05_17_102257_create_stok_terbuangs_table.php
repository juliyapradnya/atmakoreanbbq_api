<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokTerbuangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_terbuangs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_bahan');
            $table->integer('id_karyawan');
            $table->integer('jumlah_stok_terbuang');
            $table->string('satuan');
            $table->date('tgl_terbuang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stok_terbuangs');
    }
}
