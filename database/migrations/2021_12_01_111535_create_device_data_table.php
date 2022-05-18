<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('sd')->default(false);
            $table->boolean('pir')->default(false);
            $table->boolean('ss')->default(false);
            $table->integer('rn')->default(0);
            $table->boolean('ps')->default(false);
            $table->float('bv', 4, 2);
            $table->integer('aq')->default(1);
            $table->float('ntc', 3, 1);
            $table->string('psw', 4);
            $table->integer('cs');
            $table->integer('te')->default(0);
            $table->foreignUuid('device_uuid');

            // $table->foreign('device_uuid')->references('uuid')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_data');
    }
}
