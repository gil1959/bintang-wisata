<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new columns to hotel_packages
        Schema::table('hotel_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_packages', 'property_type')) {
                $table->string('property_type')->default('Hotel')->after('title');
            }
            if (!Schema::hasColumn('hotel_packages', 'address')) {
                $table->text('address')->nullable()->after('price_per_night');
            }
            if (!Schema::hasColumn('hotel_packages', 'maps_url')) {
                $table->text('maps_url')->nullable()->after('address');
            }
            if (!Schema::hasColumn('hotel_packages', 'nearby_places')) {
                $table->json('nearby_places')->nullable()->after('maps_url');
            }
            if (!Schema::hasColumn('hotel_packages', 'facilities')) {
                $table->json('facilities')->nullable()->after('features');
            }
            if (!Schema::hasColumn('hotel_packages', 'keunggulan')) {
                $table->json('keunggulan')->nullable()->after('facilities');
            }
            if (!Schema::hasColumn('hotel_packages', 'note')) {
                $table->text('note')->nullable()->after('keunggulan');
            }
            if (!Schema::hasColumn('hotel_packages', 'cs_contact')) {
                $table->string('cs_contact')->nullable()->after('note');
            }
            if (!Schema::hasColumn('hotel_packages', 'address_en')) {
                $table->text('address_en')->nullable();
            }
            if (!Schema::hasColumn('hotel_packages', 'note_en')) {
                $table->text('note_en')->nullable();
            }
            if (!Schema::hasColumn('hotel_packages', 'facilities_en')) {
                $table->json('facilities_en')->nullable();
            }
            if (!Schema::hasColumn('hotel_packages', 'keunggulan_en')) {
                $table->json('keunggulan_en')->nullable();
            }
        });

        // 2. Create hotel_package_photos table
        if (!Schema::hasTable('hotel_package_photos')) {
            Schema::create('hotel_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_package_id')->constrained('hotel_packages')->onDelete('cascade');
                $table->string('file_path');
                $table->timestamps();
            });
        }

        // 3. Create hotel_rooms table
        if (!Schema::hasTable('hotel_rooms')) {
            Schema::create('hotel_rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_package_id')->constrained('hotel_packages')->onDelete('cascade');
                $table->string('name');
                $table->string('room_size')->nullable(); // e.g. "24.0 m²"
                $table->string('bed_type')->nullable(); // e.g. "1 queen bed"
                $table->integer('max_guests')->default(2);
                $table->boolean('has_shower')->default(true);
                $table->boolean('has_wifi')->default(true);
                $table->boolean('has_breakfast')->default(false);
                $table->decimal('price', 12, 2)->default(0); // harga tanpa sarapan / harga dasar
                $table->decimal('price_with_breakfast', 12, 2)->nullable(); // harga sarapan untuk 2 orang
                $table->decimal('original_price', 12, 2)->nullable(); // harga coret promo
                $table->integer('available_rooms')->default(1); // sisa kamar
                $table->json('facilities')->nullable(); // fasilitas spesifik kamar
                $table->string('photo_path')->nullable(); // foto kamar
                $table->boolean('is_ready')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotel_package_photos');

        Schema::table('hotel_packages', function (Blueprint $table) {
            $cols = [
                'property_type', 'address', 'maps_url', 'nearby_places',
                'facilities', 'keunggulan', 'note', 'cs_contact',
                'address_en', 'note_en', 'facilities_en', 'keunggulan_en'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('hotel_packages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
