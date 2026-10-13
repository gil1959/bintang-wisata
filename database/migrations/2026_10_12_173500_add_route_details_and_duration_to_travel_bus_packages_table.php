<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRouteDetailsAndDurationToTravelBusPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travel_bus_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_bus_packages', 'route_details')) {
                $table->json('route_details')->nullable()->after('arrival_location');
            }
            if (!Schema::hasColumn('travel_bus_packages', 'arrival_days_offset')) {
                $table->smallInteger('arrival_days_offset')->default(0)->after('arrival_time');
            }
            if (!Schema::hasColumn('travel_bus_packages', 'duration_text')) {
                $table->string('duration_text')->nullable()->after('duration_minutes');
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
        Schema::table('travel_bus_packages', function (Blueprint $table) {
            if (Schema::hasColumn('travel_bus_packages', 'route_details')) {
                $table->dropColumn('route_details');
            }
            if (Schema::hasColumn('travel_bus_packages', 'arrival_days_offset')) {
                $table->dropColumn('arrival_days_offset');
            }
            if (Schema::hasColumn('travel_bus_packages', 'duration_text')) {
                $table->dropColumn('duration_text');
            }
        });
    }
}
