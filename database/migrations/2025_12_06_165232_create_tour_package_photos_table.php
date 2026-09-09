<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_package_photos')) {
            Schema::create('tour_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_package_id')
                    ->constrained('tour_packages')
                    ->cascadeOnDelete();

                $table->string('file_path');
                $table->timestamps();
            });
        } else {
            Schema::table('tour_package_photos', function (Blueprint $table) {
                if (!Schema::hasColumn('tour_package_photos', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_photos');
    }
};
