<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            $table->foreignId('category_id')->constrained('tour_categories')->cascadeOnDelete();

            $table->string('destination')->nullable();
            $table->string('duration_text')->nullable();

            $table->longText('long_description')->nullable();

            $table->json('includes')->nullable();
            $table->json('excludes')->nullable();

            $table->enum('flight_info', ['included', 'not_included'])->default('not_included');

            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tour_packages');
    }
}
