<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // doku/tripay/midtrans
            $table->string('label');
            $table->json('credentials')->nullable();
            $table->boolean('is_active')->default(false);

            // NEW: daftar channel/metode yang ditampilkan di checkout
            $table->json('channels')->nullable();
            $table->timestamp('channels_synced_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
