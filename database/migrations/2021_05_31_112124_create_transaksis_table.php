<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pesanan');
            $table->integer('id_karyawan');
            $table->string('jenis_pembayaran');
            $table->string('jenis_kartu');
            $table->string('no_kartu');
            $table->string('nama_pemilik_kartu');
            $table->string('kode_verifikasi');
            $table->date('tgl_transaksi');
            $table->double('total_sub_total');
            $table->double('service');
            $table->double('tax');
            $table->double('total_harga');
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
        Schema::dropIfExists('transaksis');
    }
}
