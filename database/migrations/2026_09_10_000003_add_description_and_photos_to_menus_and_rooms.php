<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. restoran_menus: add description and photos
        if (Schema::hasTable('restoran_menus')) {
            Schema::table('restoran_menus', function (Blueprint $table) {
                if (!Schema::hasColumn('restoran_menus', 'description')) {
                    $table->text('description')->nullable()->after('price');
                }
                if (!Schema::hasColumn('restoran_menus', 'photos')) {
                    $table->json('photos')->nullable()->after('thumbnail_path');
                }
            });
        }

        // 2. hotel_rooms: add photos (and description if not yet present)
        if (Schema::hasTable('hotel_rooms')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('hotel_rooms', 'description')) {
                    $table->text('description')->nullable()->after('facilities');
                }
                if (!Schema::hasColumn('hotel_rooms', 'photos')) {
                    $table->json('photos')->nullable()->after('photo_path');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('restoran_menus')) {
            Schema::table('restoran_menus', function (Blueprint $table) {
                if (Schema::hasColumn('restoran_menus', 'description')) {
                    $table->dropColumn('description');
                }
                if (Schema::hasColumn('restoran_menus', 'photos')) {
                    $table->dropColumn('photos');
                }
            });
        }

        if (Schema::hasTable('hotel_rooms')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                if (Schema::hasColumn('hotel_rooms', 'photos')) {
                    $table->dropColumn('photos');
                }
            });
        }
    }
};
