<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rent_car_packages', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('slug')
                ->constrained('rent_car_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rent_car_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
