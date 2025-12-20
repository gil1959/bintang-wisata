<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGatewayColumnsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'gateway_name')) {
                $table->string('gateway_name')->nullable()->after('proof_image');
            }
            if (!Schema::hasColumn('payments', 'channel_code')) {
                $table->string('channel_code')->nullable()->after('gateway_name');
            }
            if (!Schema::hasColumn('payments', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('channel_code');
            }
            if (!Schema::hasColumn('payments', 'payment_url')) {
                $table->string('payment_url')->nullable()->after('gateway_reference');
            }
            if (!Schema::hasColumn('payments', 'gateway_payload')) {
                $table->json('gateway_payload')->nullable()->after('payment_url');
            }
        });
    }

    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
