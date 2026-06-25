<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('hotel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('label')->nullable();
            $table->string('slug')->unique();
            $table->decimal('price_per_night', 12, 2);
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();

            $table->text('long_description')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
            
            $table->unsignedBigInteger('created_by_partner_id')->nullable();
            $table->string('partner_review_status')->default('pending');
            $table->text('partner_review_note')->nullable();
            $table->unsignedBigInteger('partner_reviewed_by')->nullable();
            $table->timestamp('partner_reviewed_at')->nullable();

            $table->string('title_en')->nullable();
            $table->string('label_en')->nullable();
            $table->text('long_description_en')->nullable();
            $table->json('features_en')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->string('seo_keywords_en')->nullable();
            $table->text('seo_description_en')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_packages');
    }
}
