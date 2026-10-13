<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelBusPackagePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_bus_package_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_bus_package_id')->index();
            $table->string('photo_path');
            $table->timestamps();

            $table->foreign('travel_bus_package_id')
                ->references('id')
                ->on('travel_bus_packages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_bus_package_photos');
    }
}
