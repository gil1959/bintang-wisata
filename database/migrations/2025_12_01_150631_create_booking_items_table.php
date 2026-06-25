<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');

            // tipe item
            $table->enum('item_type', ['tour', 'rental']);
            $table->unsignedBigInteger('item_id'); // id di tour_packages / rental_units

            // untuk tour
            $table->enum('audience_type', ['domestic', 'wna'])->nullable();

            // qty: jumlah orang (tour) / jumlah hari (rental)
            $table->unsignedInteger('qty')->default(1);

            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            // tanggal perjalanan / sewa
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); // khusus rental

            $table->timestamps();

            $table->index(['item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_items');
    }
}
