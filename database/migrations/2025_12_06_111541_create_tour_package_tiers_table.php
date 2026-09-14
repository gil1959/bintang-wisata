<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPackageTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_package_tiers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_package_id')
                ->constrained()
                ->cascadeOnDelete();

            // domestic / wna
            $table->enum('audience', ['domestic', 'wna']);

            // apakah tier ini custom?
            $table->boolean('is_custom')->default(false);

            // untuk tier normal
            $table->unsignedInteger('min_people')->nullable();
            $table->unsignedInteger('max_people')->nullable();

            // label bebas (contoh: "Private Trip")
            $table->string('label')->nullable();

            // harga per orang
            $table->unsignedBigInteger('price_per_person');

            // urutan tampil
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_package_tiers');
    }
}
