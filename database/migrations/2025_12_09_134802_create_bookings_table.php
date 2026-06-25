<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // INVOICE
            $table->string('invoice')->unique();

            // BOOKABLE: tour / rentcar
            $table->string('booking_type'); // 'tour' atau 'rentcar'
            $table->unsignedBigInteger('booking_id'); // id paket

            // USER INFO
            $table->string('name');
            $table->string('email');
            $table->string('phone');

            // RENT CAR DATE
            $table->date('pickup_date')->nullable();
            $table->date('return_date')->nullable();

            // TOUR EXTRA
            $table->integer('people_count')->nullable();

            // HARGA
            $table->integer('base_price');
            $table->integer('discount')->default(0);
            $table->integer('final_price');

            // PROMO
            $table->unsignedBigInteger('promo_id')->nullable();

            // PAYMENT
            $table->enum('payment_type', ['manual', 'gateway'])->default('manual');
            $table->string('payment_code')->nullable();

            // STATUS
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            // BUKTI TRANSFER
            $table->string('proof_path')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
