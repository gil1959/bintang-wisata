<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hotel_rooms') && !Schema::hasColumn('hotel_rooms', 'description')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                $table->text('description')->nullable()->after('facilities');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hotel_rooms') && Schema::hasColumn('hotel_rooms', 'description')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
