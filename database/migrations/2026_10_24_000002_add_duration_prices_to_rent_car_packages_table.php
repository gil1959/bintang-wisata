<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDurationPricesToRentCarPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('rent_car_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_car_packages', 'price_per_12_hours')) {
                $table->decimal('price_per_12_hours', 15, 2)->nullable()->after('price_per_hour');
            }
            if (!Schema::hasColumn('rent_car_packages', 'price_per_24_hours')) {
                $table->decimal('price_per_24_hours', 15, 2)->nullable()->after('price_per_12_hours');
            }
        });

        // Isi data eksisting bila belum memiliki harga 12 & 24 jam
        DB::table('rent_car_packages')
            ->where(function ($query) {
                $query->whereNull('price_per_12_hours')
                      ->orWhere('price_per_12_hours', '<=', 0);
            })
            ->update([
                'price_per_12_hours' => DB::raw("CASE WHEN price_per_hour > 0 THEN price_per_hour ELSE 350000 END"),
                'price_per_24_hours' => DB::raw("CASE WHEN price_per_hour > 0 THEN price_per_hour * 2 ELSE 700000 END"),
            ]);
    }

    public function down()
    {
        Schema::table('rent_car_packages', function (Blueprint $table) {
            if (Schema::hasColumn('rent_car_packages', 'price_per_24_hours')) {
                $table->dropColumn('price_per_24_hours');
            }
            if (Schema::hasColumn('rent_car_packages', 'price_per_12_hours')) {
                $table->dropColumn('price_per_12_hours');
            }
        });
    }
}
