<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name'); // Nama yang tampil di frontend
            $table->string('slug')->unique(); // manual-bca, manual-bni, gateway-xendit
            $table->enum('type', ['manual', 'gateway']);

            // Manual Transfer
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();

            // Gateway (misal Xendit/Midtrans)
            $table->string('gateway_name')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
