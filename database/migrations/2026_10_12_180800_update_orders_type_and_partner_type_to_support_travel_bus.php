<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateOrdersTypeAndPartnerTypeToSupportTravelBus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ubah orders.type agar mendukung 'travel_bus'
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `type` VARCHAR(50) NOT NULL");

        // Ubah partner_type di users & partner_applications agar mendukung 'agency_travel_bus'
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `partner_type` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `partner_applications` MODIFY COLUMN `partner_type` VARCHAR(50) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `type` ENUM('tour','rent_car','ship','umrah','mice','restoran','hotel') NOT NULL");
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `partner_type` ENUM('agency_paket_tour','agency_rental_mobil','agency_kapal','agency_restoran','agency_hotel_vila') NULL");
        DB::statement("ALTER TABLE `partner_applications` MODIFY COLUMN `partner_type` ENUM('agency_paket_tour','agency_rental_mobil','agency_kapal','agency_restoran','agency_hotel_vila') NULL");
    }
}
