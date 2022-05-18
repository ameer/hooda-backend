<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDeviceRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_device_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->foreignUuid('device_uuid');
            $table->tinyInteger('role')->nullable();
            $table->timestamps();


            $table->foreign('owner_id')->references('id')->on('users');
            // $table->foreign('device_id')->references('uuid')->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_device_relations');
    }
}
