<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restoran_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('restoran_packages', 'cs_contact')) {
                $table->string('cs_contact', 50)->nullable()->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restoran_packages', function (Blueprint $table) {
            if (Schema::hasColumn('restoran_packages', 'cs_contact')) {
                $table->dropColumn('cs_contact');
            }
        });
    }
};
