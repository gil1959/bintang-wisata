<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {

            if (!Schema::hasColumn('payment_gateways', 'label')) {
                $table->string('label')->nullable()->after('name');
            }

            if (!Schema::hasColumn('payment_gateways', 'channels')) {
                $table->json('channels')->nullable()->after('credentials');
            }

            if (!Schema::hasColumn('payment_gateways', 'channels_synced_at')) {
                $table->timestamp('channels_synced_at')->nullable()->after('channels');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            if (Schema::hasColumn('payment_gateways', 'channels_synced_at')) {
                $table->dropColumn('channels_synced_at');
            }
            if (Schema::hasColumn('payment_gateways', 'channels')) {
                $table->dropColumn('channels');
            }
            if (Schema::hasColumn('payment_gateways', 'label')) {
                $table->dropColumn('label');
            }
        });
    }
};
