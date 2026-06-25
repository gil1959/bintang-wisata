<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')
                ->constrained()
                ->cascadeOnDelete();

            // Format waktu wajib HH:MM
            $table->time('time');

            $table->string('description');

            $table->unsignedInteger('sort_order')->default(0);

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
        Schema::dropIfExists('tour_itineraries');
    }
}
