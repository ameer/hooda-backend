<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->string('uuid')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('owner_id');
            $table->string('imei', 16)->unique();
            $table->string('sim_number', 11)->unique();
            $table->string('pn2', 11)->unique();
            $table->string('pn3', 11)->unique();
            $table->string('location', 250);

            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
