<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tabungan_umrah_accounts')) {
            // Ubah enum ke varchar agar mendukung tabungan_liburan, tabungan_umrah, dsb tanpa batasan enum MySQL
            DB::statement("ALTER TABLE tabungan_umrah_accounts MODIFY COLUMN saving_type VARCHAR(50) NOT NULL DEFAULT 'tabungan_liburan'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tabungan_umrah_accounts')) {
            DB::statement("ALTER TABLE tabungan_umrah_accounts MODIFY COLUMN saving_type ENUM('umroh_reguler','haji_furoda') NOT NULL DEFAULT 'umroh_reguler'");
        }
    }
};
