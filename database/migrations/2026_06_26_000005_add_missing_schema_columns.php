<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. booking_items table
        if (!Schema::hasTable('booking_items')) {
            Schema::create('booking_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained()->onDelete('cascade');
                $table->enum('item_type', ['tour', 'rental']);
                $table->unsignedBigInteger('item_id');
                $table->enum('audience_type', ['domestic', 'wna'])->nullable();
                $table->unsignedInteger('qty')->default(1);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();
                $table->index(['item_type', 'item_id']);
            });
        }

        // 2. documentations
        Schema::table('documentations', function (Blueprint $table) {
            if (!Schema::hasColumn('documentations', 'category')) {
                $table->string('category')->default('tour')->after('id')->index();
            }
            if (!Schema::hasColumn('documentations', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }
        });

        // 3. articles
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'title_en')) {
                $table->string('title_en')->nullable();
            }
            if (!Schema::hasColumn('articles', 'excerpt_en')) {
                $table->text('excerpt_en')->nullable();
            }
            if (!Schema::hasColumn('articles', 'content_en')) {
                $table->longText('content_en')->nullable();
            }
            if (!Schema::hasColumn('articles', 'seo_title_en')) {
                $table->string('seo_title_en')->nullable();
            }
            if (!Schema::hasColumn('articles', 'seo_description_en')) {
                $table->text('seo_description_en')->nullable();
            }
        });

        // 4. destination_inspirations
        Schema::table('destination_inspirations', function (Blueprint $table) {
            if (!Schema::hasColumn('destination_inspirations', 'icon')) {
                $table->string('icon')->nullable();
            }
            if (!Schema::hasColumn('destination_inspirations', 'tour_subcategory_id')) {
                $table->unsignedBigInteger('tour_subcategory_id')->nullable();
            }
            if (!Schema::hasColumn('destination_inspirations', 'title_en')) {
                $table->string('title_en')->nullable();
            }
        });

        // 5. hotel_packages
        Schema::table('hotel_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_packages', 'seo_image_path')) {
                $table->string('seo_image_path')->nullable();
            }
            if (!Schema::hasColumn('hotel_packages', 'social_title')) {
                $table->string('social_title')->nullable();
            }
            if (!Schema::hasColumn('hotel_packages', 'social_description')) {
                $table->text('social_description')->nullable();
            }
        });

        // 6. restoran_packages
        Schema::table('restoran_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('restoran_packages', 'seo_image_path')) {
                $table->string('seo_image_path')->nullable();
            }
            if (!Schema::hasColumn('restoran_packages', 'social_title')) {
                $table->string('social_title')->nullable();
            }
            if (!Schema::hasColumn('restoran_packages', 'social_description')) {
                $table->text('social_description')->nullable();
            }
        });

        // 7. mice_categories
        Schema::table('mice_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('mice_categories', 'name_en')) {
                $table->string('name_en')->nullable();
            }
        });

        // 8. mice_packages
        Schema::table('mice_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('mice_packages', 'destination_en')) {
                $table->string('destination_en')->nullable();
            }
            if (!Schema::hasColumn('mice_packages', 'itinerary_en')) {
                $table->longText('itinerary_en')->nullable();
            }
            if (!Schema::hasColumn('mice_packages', 'include_text_en')) {
                $table->text('include_text_en')->nullable();
            }
            if (!Schema::hasColumn('mice_packages', 'exclude_text_en')) {
                $table->text('exclude_text_en')->nullable();
            }
        });

        // 9. orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'promo_id')) {
                $table->unsignedBigInteger('promo_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'promo_code')) {
                $table->string('promo_code')->nullable();
            }
            if (!Schema::hasColumn('orders', 'total_hours')) {
                $table->integer('total_hours')->nullable();
            }
            if (!Schema::hasColumn('orders', 'affiliate_user_id')) {
                $table->unsignedBigInteger('affiliate_user_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'affiliate_link_id')) {
                $table->unsignedBigInteger('affiliate_link_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'affiliate_ref')) {
                $table->string('affiliate_ref')->nullable();
            }
            if (!Schema::hasColumn('orders', 'affiliate_commission_type')) {
                $table->string('affiliate_commission_type')->nullable();
            }
            if (!Schema::hasColumn('orders', 'affiliate_commission_value')) {
                $table->decimal('affiliate_commission_value', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'affiliate_commission_amount')) {
                $table->decimal('affiliate_commission_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'affiliate_commission_status')) {
                $table->string('affiliate_commission_status')->default('none');
            }
            if (!Schema::hasColumn('orders', 'affiliate_commission_set_by')) {
                $table->unsignedBigInteger('affiliate_commission_set_by')->nullable();
            }
            if (!Schema::hasColumn('orders', 'affiliate_commission_set_at')) {
                $table->dateTime('affiliate_commission_set_at')->nullable();
            }
        });

        // 10. promos
        Schema::table('promos', function (Blueprint $table) {
            if (!Schema::hasColumn('promos', 'min_price')) {
                $table->decimal('min_price', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('promos', 'start_date')) {
                $table->dateTime('start_date')->nullable();
            }
            if (!Schema::hasColumn('promos', 'end_date')) {
                $table->dateTime('end_date')->nullable();
            }
        });

        // 11. rent_car_categories
        Schema::table('rent_car_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_car_categories', 'created_by_partner_id')) {
                $table->unsignedBigInteger('created_by_partner_id')->nullable();
            }
        });

        // 12. rent_car_packages
        Schema::table('rent_car_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('rent_car_packages', 'price_per_hour')) {
                $table->decimal('price_per_hour', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'price_per_12_hours')) {
                $table->decimal('price_per_12_hours', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'price_per_24_hours')) {
                $table->decimal('price_per_24_hours', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'long_description')) {
                $table->longText('long_description')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'seo_title')) {
                $table->string('seo_title')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'seo_keywords')) {
                $table->string('seo_keywords')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'seo_description')) {
                $table->text('seo_description')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'created_by_partner_id')) {
                $table->unsignedBigInteger('created_by_partner_id')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'partner_review_status')) {
                $table->string('partner_review_status')->default('approved');
            }
            if (!Schema::hasColumn('rent_car_packages', 'partner_review_note')) {
                $table->text('partner_review_note')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'partner_reviewed_by')) {
                $table->unsignedBigInteger('partner_reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'partner_reviewed_at')) {
                $table->dateTime('partner_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'title_en')) {
                $table->string('title_en')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'label_en')) {
                $table->string('label_en')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'long_description_en')) {
                $table->longText('long_description_en')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'features_en')) {
                $table->json('features_en')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'seo_title_en')) {
                $table->string('seo_title_en')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'seo_keywords_en')) {
                $table->string('seo_keywords_en')->nullable();
            }
            if (!Schema::hasColumn('rent_car_packages', 'seo_description_en')) {
                $table->text('seo_description_en')->nullable();
            }
        });

        // 13. ship_categories
        Schema::table('ship_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('ship_categories', 'name_en')) {
                $table->string('name_en')->nullable();
            }
            if (!Schema::hasColumn('ship_categories', 'created_by_partner_id')) {
                $table->unsignedBigInteger('created_by_partner_id')->nullable();
            }
        });

        // 14. tour_itineraries
        Schema::table('tour_itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_itineraries', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('tour_itineraries', 'title_en')) {
                $table->string('title_en')->nullable();
            }
        });

        // 15. tour_packages
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'rating_value')) {
                $table->decimal('rating_value', 3, 2)->default(5.0);
            }
            if (!Schema::hasColumn('tour_packages', 'rating_count')) {
                $table->integer('rating_count')->default(0);
            }
            if (!Schema::hasColumn('tour_packages', 'seo_title')) {
                $table->string('seo_title')->nullable();
            }
            if (!Schema::hasColumn('tour_packages', 'seo_description')) {
                $table->text('seo_description')->nullable();
            }
            if (!Schema::hasColumn('tour_packages', 'seo_keywords')) {
                $table->string('seo_keywords')->nullable();
            }
        });

        // 16. tour_package_tiers
        Schema::table('tour_package_tiers', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_package_tiers', 'label_text')) {
                $table->string('label_text')->nullable();
            }
        });

        // 17. tour_price_tiers
        Schema::table('tour_price_tiers', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_price_tiers', 'label_text')) {
                $table->string('label_text')->nullable();
            }
        });

        // 18. umrah_categories
        Schema::table('umrah_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('umrah_categories', 'name_en')) {
                $table->string('name_en')->nullable();
            }
        });

        // 19. users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_affiliate')) {
                $table->boolean('is_affiliate')->default(false);
            }
            if (!Schema::hasColumn('users', 'affiliate_status')) {
                $table->string('affiliate_status')->default('none');
            }
            if (!Schema::hasColumn('users', 'affiliate_requested_at')) {
                $table->dateTime('affiliate_requested_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'affiliate_reviewed_at')) {
                $table->dateTime('affiliate_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'affiliate_reviewed_by')) {
                $table->unsignedBigInteger('affiliate_reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('users', 'affiliate_review_note')) {
                $table->text('affiliate_review_note')->nullable();
            }
            if (!Schema::hasColumn('users', 'affiliate_commission_type')) {
                $table->string('affiliate_commission_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'affiliate_commission_value')) {
                $table->decimal('affiliate_commission_value', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false);
            }
            if (!Schema::hasColumn('users', 'suspended_at')) {
                $table->dateTime('suspended_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'partner_tax_percent')) {
                $table->decimal('partner_tax_percent', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'partner_type')) {
                $table->string('partner_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'partner_bank_name')) {
                $table->string('partner_bank_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'partner_bank_account_number')) {
                $table->string('partner_bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'partner_bank_account_holder')) {
                $table->string('partner_bank_account_holder')->nullable();
            }
        });
    }

    public function down(): void
    {
    }
};
