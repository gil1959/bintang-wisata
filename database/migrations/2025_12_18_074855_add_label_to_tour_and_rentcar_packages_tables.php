<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->string('label', 30)->nullable()->after('title');
        });

        Schema::table('rent_car_packages', function (Blueprint $table) {
            $table->string('label', 30)->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('label');
        });

        Schema::table('rent_car_packages', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
};
