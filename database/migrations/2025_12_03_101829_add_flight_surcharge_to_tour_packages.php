<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'flight_surcharge_per_pax')) {
                $table->decimal('flight_surcharge_per_pax', 15, 2)
                    ->nullable()
                    ->after('short_description');
            }
        });
    }


    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('flight_surcharge_per_pax');
        });
    }
};
