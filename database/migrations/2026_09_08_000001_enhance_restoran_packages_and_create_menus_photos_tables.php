<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new columns to restoran_packages
        Schema::table('restoran_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('restoran_packages', 'address')) {
                $table->text('address')->nullable()->after('price_per_pax');
            }
            if (!Schema::hasColumn('restoran_packages', 'maps_url')) {
                $table->text('maps_url')->nullable()->after('address');
            }
            if (!Schema::hasColumn('restoran_packages', 'nearby_places')) {
                $table->json('nearby_places')->nullable()->after('maps_url');
            }
            if (!Schema::hasColumn('restoran_packages', 'facilities')) {
                $table->json('facilities')->nullable()->after('features');
            }
            if (!Schema::hasColumn('restoran_packages', 'keunggulan')) {
                $table->json('keunggulan')->nullable()->after('facilities');
            }
            if (!Schema::hasColumn('restoran_packages', 'note')) {
                $table->text('note')->nullable()->after('keunggulan');
            }
            if (!Schema::hasColumn('restoran_packages', 'address_en')) {
                $table->text('address_en')->nullable();
            }
            if (!Schema::hasColumn('restoran_packages', 'note_en')) {
                $table->text('note_en')->nullable();
            }
            if (!Schema::hasColumn('restoran_packages', 'keunggulan_en')) {
                $table->json('keunggulan_en')->nullable();
            }
            if (!Schema::hasColumn('restoran_packages', 'facilities_en')) {
                $table->json('facilities_en')->nullable();
            }
        });

        // 2. Create restoran_package_photos table
        if (!Schema::hasTable('restoran_package_photos')) {
            Schema::create('restoran_package_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restoran_package_id')->constrained('restoran_packages')->onDelete('cascade');
                $table->string('file_path');
                $table->timestamps();
            });
        }

        // 3. Create restoran_menus table
        if (!Schema::hasTable('restoran_menus')) {
            Schema::create('restoran_menus', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restoran_package_id')->constrained('restoran_packages')->onDelete('cascade');
                $table->string('name');
                $table->string('category')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->string('thumbnail_path')->nullable();
                $table->boolean('is_ready')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('restoran_menus');
        Schema::dropIfExists('restoran_package_photos');

        Schema::table('restoran_packages', function (Blueprint $table) {
            $cols = [
                'address', 'maps_url', 'nearby_places', 'facilities',
                'keunggulan', 'note', 'address_en', 'note_en',
                'keunggulan_en', 'facilities_en'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('restoran_packages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
