<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('unique_code')->nullable()->after('final_price');
            $table->unsignedBigInteger('payable_amount')->nullable()->after('unique_code');
        });

        // backfill buat order lama
        DB::table('orders')->whereNull('payable_amount')->update([
            'payable_amount' => DB::raw('final_price')
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['unique_code', 'payable_amount']);
        });
    }
};
