<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_price_tiers', function (Blueprint $table) {
            // harga per orang kalau user pilih "Dengan tiket pesawat"
            $table->decimal('price_with_flight_per_pax', 15, 2)
                ->nullable()
                ->after('price_per_pax');
        });
    }

    public function down(): void
    {
        Schema::table('tour_price_tiers', function (Blueprint $table) {
            $table->dropColumn('price_with_flight_per_pax');
        });
    }
};
