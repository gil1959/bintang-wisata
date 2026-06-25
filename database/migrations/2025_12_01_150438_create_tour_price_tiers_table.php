<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPriceTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->enum('audience', ['domestic', 'wna']); // dipisah
            $table->string('label')->nullable(); // contoh: "1-2 Orang", "3-6 Orang"
            $table->unsignedInteger('min_peserta')->nullable(); // null = custom
            $table->unsignedInteger('max_peserta')->nullable(); // null = custom
            $table->unsignedBigInteger('harga_per_orang'); // harga final
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_price_tiers');
    }
}
