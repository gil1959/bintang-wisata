<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'tour_packages',
            'rent_car_packages',
            'restoran_packages',
            'hotel_packages',
            'ship_packages',
            'umrah_packages',
            'mice_packages',
            'articles'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->string('seo_image_path')->nullable();
                    $tableBlueprint->string('social_title')->nullable();
                    $tableBlueprint->text('social_description')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'tour_packages',
            'rent_car_packages',
            'restoran_packages',
            'hotel_packages',
            'ship_packages',
            'umrah_packages',
            'mice_packages',
            'articles'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropColumn(['seo_image_path', 'social_title', 'social_description']);
                });
            }
        }
    }
};
