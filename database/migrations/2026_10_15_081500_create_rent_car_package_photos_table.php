<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rent_car_package_photos')) {
            Schema::create('rent_car_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rent_car_package_id')
                    ->constrained('rent_car_packages')
                    ->cascadeOnDelete();
                $table->string('file_path');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_car_package_photos');
    }
};
