<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');

            $table->string('method'); // manual / gateway
            $table->integer('amount'); // final_price

            // Bukti transaksi manual transfer
            $table->string('proof_image')->nullable();

            // Gateway metadata
            $table->string('gateway_name')->nullable();      // doku/tripay/midtrans
            $table->string('channel_code')->nullable();      // VA, QRIS, etc
            $table->string('gateway_reference')->nullable(); // id transaksi/invoice dari gateway
            $table->string('payment_url')->nullable();
            $table->json('gateway_payload')->nullable();

            $table->enum('status', [
                'waiting_payment',       // gateway pending sebelum bayar
                'waiting_verification',  // manual setelah upload bukti
                'paid',
                'failed'
            ]);

            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
