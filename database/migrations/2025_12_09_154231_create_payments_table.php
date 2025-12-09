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

            // Gateway transaction ID (Xendit/Midtrans)
            $table->string('gateway_reference')->nullable();

            $table->enum('status', [
                'waiting_verification',
                'paid',
                'failed'
            ]);

            $table->timestamps();

            // Relasi
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
