<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPackagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['domestic', 'international']);
            $table->string('destination')->nullable(); // Nusa Penida, Bali, dll
            $table->string('duration_text')->nullable(); // 1 Hari, 3D2N, dst
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('include_flight_option')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail_path')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tour_packages');
    }
}
