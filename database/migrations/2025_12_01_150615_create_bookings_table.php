<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // kode booking untuk user
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            $table->enum('type', ['tour', 'rental']); // tipe utama booking

            $table->enum('status', ['pending', 'waiting_payment', 'paid', 'cancelled'])
                ->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])
                ->default('unpaid');
            $table->string('payment_method')->nullable(); // bank_transfer, gateway, dll

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2)->default(0);

            $table->foreignId('promo_id')->nullable()->constrained('promos')->nullOnDelete();
            $table->boolean('with_flight')->default(false); // khusus tour

            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable(); // kapan dibayar
            $table->timestamps(); // created_at dipakai buat auto-cancel 1x24 jam
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
