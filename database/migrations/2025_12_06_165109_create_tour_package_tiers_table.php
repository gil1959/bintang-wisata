<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_package_tiers')) {
            Schema::create('tour_package_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();

                $table->enum('type', ['domestic', 'international']);
                $table->boolean('is_custom')->default(false);

                $table->unsignedInteger('min_people')->nullable();
                $table->unsignedInteger('max_people')->nullable();

                $table->unsignedBigInteger('price');

                $table->timestamps();
            });
        } else {
            Schema::table('tour_package_tiers', function (Blueprint $table) {
                if (!Schema::hasColumn('tour_package_tiers', 'type')) {
                    $table->enum('type', ['domestic', 'international'])->nullable();
                }
                if (!Schema::hasColumn('tour_package_tiers', 'price')) {
                    $table->unsignedBigInteger('price')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_tiers');
    }
};
