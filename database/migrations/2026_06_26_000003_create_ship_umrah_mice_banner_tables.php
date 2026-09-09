<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // tour_categories missing columns
        Schema::table('tour_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('slug')->constrained('tour_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('tour_categories', 'created_by_partner_id')) {
                $table->unsignedBigInteger('created_by_partner_id')->nullable()->after('parent_id');
            }
        });

        // tour_packages missing columns
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'subcategory_id')) {
                $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained('tour_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('tour_packages', 'created_by_partner_id')) {
                $table->unsignedBigInteger('created_by_partner_id')->nullable();
                $table->string('partner_review_status')->default('pending');
                $table->text('partner_review_note')->nullable();
                $table->unsignedBigInteger('partner_reviewed_by')->nullable();
                $table->timestamp('partner_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('tour_packages', 'title_en')) {
                $table->string('title_en')->nullable();
                $table->string('label_en')->nullable();
                $table->string('destination_en')->nullable();
                $table->string('duration_text_en')->nullable();
                $table->longText('long_description_en')->nullable();
                $table->json('includes_en')->nullable();
                $table->json('excludes_en')->nullable();
                $table->string('flight_info_en')->nullable();
                $table->string('seo_title_en')->nullable();
                $table->text('seo_description_en')->nullable();
                $table->string('seo_keywords_en')->nullable();
            }
        });

        // ship_categories
        if (!Schema::hasTable('ship_categories')) {
            Schema::create('ship_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // ship_packages
        if (!Schema::hasTable('ship_packages')) {
            Schema::create('ship_packages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('label')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('ship_categories')->nullOnDelete();
                $table->string('thumbnail_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('features')->nullable();
                $table->longText('long_description')->nullable();
                $table->string('seo_title')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->string('seo_image_path')->nullable();
                $table->string('social_title')->nullable();
                $table->text('social_description')->nullable();
                $table->text('seo_description')->nullable();
                $table->float('rating_value')->default(0);
                $table->integer('rating_count')->default(0);
                $table->unsignedBigInteger('created_by_partner_id')->nullable();
                $table->string('partner_review_status')->default('pending');
                $table->text('partner_review_note')->nullable();
                $table->unsignedBigInteger('partner_reviewed_by')->nullable();
                $table->timestamp('partner_reviewed_at')->nullable();
                $table->string('title_en')->nullable();
                $table->string('label_en')->nullable();
                $table->string('duration_text_en')->nullable();
                $table->longText('long_description_en')->nullable();
                $table->string('seo_title_en')->nullable();
                $table->string('seo_keywords_en')->nullable();
                $table->text('seo_description_en')->nullable();
                $table->timestamps();
            });
        }

        // ship_package_tiers
        if (!Schema::hasTable('ship_package_tiers')) {
            Schema::create('ship_package_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ship_package_id')->constrained('ship_packages')->cascadeOnDelete();
                $table->string('type')->nullable();
                $table->string('label_text')->nullable();
                $table->unsignedBigInteger('price')->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // ship_package_photos
        if (!Schema::hasTable('ship_package_photos')) {
            Schema::create('ship_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ship_package_id')->constrained('ship_packages')->cascadeOnDelete();
                $table->string('file_path');
                $table->timestamps();
            });
        }

        // umrah_categories
        if (!Schema::hasTable('umrah_categories')) {
            Schema::create('umrah_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // umrah_packages
        if (!Schema::hasTable('umrah_packages')) {
            Schema::create('umrah_packages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('label')->nullable();
                $table->float('rating_value')->default(0);
                $table->integer('rating_count')->default(0);
                $table->string('slug')->unique();
                $table->foreignId('category_id')->nullable()->constrained('umrah_categories')->nullOnDelete();
                $table->string('destination')->nullable();
                $table->string('duration_text')->nullable();
                $table->longText('long_description')->nullable();
                $table->text('itinerary')->nullable();
                $table->text('include_text')->nullable();
                $table->text('exclude_text')->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->string('seo_image_path')->nullable();
                $table->string('social_title')->nullable();
                $table->text('social_description')->nullable();
                $table->string('title_en')->nullable();
                $table->string('label_en')->nullable();
                $table->string('duration_text_en')->nullable();
                $table->longText('long_description_en')->nullable();
                $table->string('seo_title_en')->nullable();
                $table->string('seo_keywords_en')->nullable();
                $table->text('seo_description_en')->nullable();
                $table->text('itinerary_en')->nullable();
                $table->text('include_text_en')->nullable();
                $table->text('exclude_text_en')->nullable();
                $table->timestamps();
            });
        }

        // umrah_package_tiers
        if (!Schema::hasTable('umrah_package_tiers')) {
            Schema::create('umrah_package_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('umrah_package_id')->constrained('umrah_packages')->cascadeOnDelete();
                $table->string('label_text')->nullable();
                $table->unsignedBigInteger('price')->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // umrah_package_photos
        if (!Schema::hasTable('umrah_package_photos')) {
            Schema::create('umrah_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('umrah_package_id')->constrained('umrah_packages')->cascadeOnDelete();
                $table->string('file_path');
                $table->timestamps();
            });
        }

        // mice_categories
        if (!Schema::hasTable('mice_categories')) {
            Schema::create('mice_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // mice_packages
        if (!Schema::hasTable('mice_packages')) {
            Schema::create('mice_packages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('label')->nullable();
                $table->float('rating_value')->default(0);
                $table->integer('rating_count')->default(0);
                $table->string('slug')->unique();
                $table->foreignId('category_id')->nullable()->constrained('mice_categories')->nullOnDelete();
                $table->string('destination')->nullable();
                $table->string('duration_text')->nullable();
                $table->longText('long_description')->nullable();
                $table->text('itinerary')->nullable();
                $table->text('include_text')->nullable();
                $table->text('exclude_text')->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->string('seo_image_path')->nullable();
                $table->string('social_title')->nullable();
                $table->text('social_description')->nullable();
                $table->string('title_en')->nullable();
                $table->string('label_en')->nullable();
                $table->string('duration_text_en')->nullable();
                $table->longText('long_description_en')->nullable();
                $table->string('seo_title_en')->nullable();
                $table->string('seo_keywords_en')->nullable();
                $table->text('seo_description_en')->nullable();
                $table->timestamps();
            });
        }

        // mice_package_tiers
        if (!Schema::hasTable('mice_package_tiers')) {
            Schema::create('mice_package_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mice_package_id')->constrained('mice_packages')->cascadeOnDelete();
                $table->string('type')->nullable();
                $table->string('label_text')->nullable();
                $table->unsignedBigInteger('price')->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // mice_package_photos
        if (!Schema::hasTable('mice_package_photos')) {
            Schema::create('mice_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mice_package_id')->constrained('mice_packages')->cascadeOnDelete();
                $table->string('file_path');
                $table->timestamps();
            });
        }

        // home_promo_banners
        if (!Schema::hasTable('home_promo_banners')) {
            Schema::create('home_promo_banners', function (Blueprint $table) {
                $table->id();
                $table->string('section')->default('discount');
                $table->string('thumbnail_path')->nullable();
                $table->string('link_url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('home_promo_banners');
        Schema::dropIfExists('mice_package_photos');
        Schema::dropIfExists('mice_package_tiers');
        Schema::dropIfExists('mice_packages');
        Schema::dropIfExists('mice_categories');
        Schema::dropIfExists('umrah_package_photos');
        Schema::dropIfExists('umrah_package_tiers');
        Schema::dropIfExists('umrah_packages');
        Schema::dropIfExists('umrah_categories');
        Schema::dropIfExists('ship_package_photos');
        Schema::dropIfExists('ship_package_tiers');
        Schema::dropIfExists('ship_packages');
        Schema::dropIfExists('ship_categories');
    }
};
