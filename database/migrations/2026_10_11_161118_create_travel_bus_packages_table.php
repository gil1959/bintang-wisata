<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelBusPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_bus_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_partner_id')->nullable()->index();
            $table->string('slug')->unique();
            $table->string('armada_name');
            $table->string('armada_type');
            $table->string('travel_type')->default('BUSINESS');
            $table->string('departure_location');
            $table->string('arrival_location');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->integer('duration_minutes')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->json('facilities')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->text('description')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('partner_review_status')->default('approved'); // approved, pending, rejected
            $table->text('partner_review_note')->nullable();
            $table->unsignedBigInteger('partner_reviewed_by')->nullable();
            $table->timestamp('partner_reviewed_at')->nullable();
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
        Schema::dropIfExists('travel_bus_packages');
    }
}
