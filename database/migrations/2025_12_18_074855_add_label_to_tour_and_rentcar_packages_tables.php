<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelToTourAndRentcarPackagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'label')) {
                $table->string('label')->nullable()->after('title');
            }
        });

        Schema::table('rent_car_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_car_packages', 'label')) {
                $table->string('label')->nullable()->after('title');
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
        Schema::table('tour_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tour_packages', 'label')) {
                $table->dropColumn('label');
            }
        });

        Schema::table('rent_car_packages', function (Blueprint $table) {
            if (Schema::hasColumn('rent_car_packages', 'label')) {
                $table->dropColumn('label');
            }
        });
    }
}
