<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTourPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tour_packages', function (Blueprint $table) {

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('tour_categories')
                ->nullOnDelete();

            $table->json('includes')->nullable();
            $table->json('excludes')->nullable();

            $table->enum('flight_info', ['included', 'not_included'])
                ->default('not_included');
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
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'includes',
                'excludes',
                'flight_info',
            ]);
        });
    }
}
