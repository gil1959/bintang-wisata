<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_flight_ticket_price_to_tour_packages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->decimal('flight_ticket_price', 15, 2)->nullable()->after('include_flight_option');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('flight_ticket_price');
        });
    }
};
