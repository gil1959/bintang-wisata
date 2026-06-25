<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentCarPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('rent_car_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->decimal('price_per_day', 12, 2);
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);

            // fitur dinamis berbentuk JSON
            $table->json('features')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rent_car_packages');
    }
}
