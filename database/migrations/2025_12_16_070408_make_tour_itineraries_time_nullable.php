<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: jadikan kolom time nullable
        DB::statement("ALTER TABLE tour_itineraries MODIFY time TIME NULL");
    }

    public function down(): void
    {
        // HATI-HATI: kalau sudah ada NULL, rollback bisa gagal.
        DB::statement("ALTER TABLE tour_itineraries MODIFY time TIME NOT NULL");
    }
};
