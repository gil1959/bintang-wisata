<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number')->unique();

            // Tipe pemesanan
            $table->enum('type', ['tour', 'rent_car']);

            // Produk yang dipesan
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');

            // Data pop-up
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // Data spesifik tour/rent
            $table->date('departure_date')->nullable();   // Untuk tour
            $table->integer('total_days')->nullable();    // Untuk rent car
            $table->integer('participants')->nullable();  // Untuk tour

            // Billing address
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_country')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal')->nullable();
            $table->string('billing_phone')->nullable();

            // Harga final (computed di backend, bukan dari user)
            $table->integer('subtotal');
            $table->integer('discount');
            $table->integer('final_price');

            // Payment
            $table->string('payment_method')->nullable();
            // slug dari payment_methods table
            $table->enum('payment_status', [
                'waiting_payment',
                'waiting_verification',
                'paid',
                'failed'
            ]);

            // Admin order status
            $table->enum('order_status', ['pending', 'approved', 'rejected']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
