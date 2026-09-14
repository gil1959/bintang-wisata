<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->nullable(); // car, hiace, bus, dll
            $table->unsignedInteger('capacity')->nullable();
            $table->string('transmission')->nullable(); // manual, automatic
            $table->decimal('price_per_day', 15, 2)->default(0);
            $table->decimal('price_with_driver_per_day', 15, 2)->nullable();
            $table->boolean('include_fuel')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail_path')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('rental_units');
    }
}
