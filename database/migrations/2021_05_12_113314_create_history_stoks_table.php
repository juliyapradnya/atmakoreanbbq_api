<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryStoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_stoks', function (Blueprint $table) {
            $table->id();
            $table->integer('id_bahan');
            $table->integer('id_karyawan');
            $table->integer('sisa_stok');
            $table->integer('jumlah_stok_keluar');
            $table->integer('jumlah_stok_terbuang');
            $table->date('tanggal_history');
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
        Schema::dropIfExists('history_stoks');
    }
}
