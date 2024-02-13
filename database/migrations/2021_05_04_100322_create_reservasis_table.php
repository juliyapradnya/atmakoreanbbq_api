<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->integer('id_customer');
            $table->integer('id_meja');
            $table->date('tgl_reservasi');
            $table->string('jam_reservasi');
            $table->string('status_reservasi');
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
        Schema::dropIfExists('reservasis');
    }
}

