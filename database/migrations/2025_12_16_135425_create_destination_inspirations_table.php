<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_inspirations', function (Blueprint $table) {
            $table->id();

            // Judul card (contoh: JOGJA / SOLO / SEMARANG)
            $table->string('title', 120);

            // Path gambar (disimpan di storage/app/public/...)
            // contoh value: inspirations/jogja.jpg
            $table->string('image_path')->nullable();

            // Kategori tour yang akan dituju saat card diklik
            // nullable = kalau kosong, link ke semua paket tour
            $table->foreignId('tour_category_id')
                ->nullable()
                ->constrained('tour_categories')
                ->nullOnDelete();

            // Urutan tampil di home
            $table->unsignedInteger('sort_order')->default(0);

            // Aktif/nonaktif (biar admin bisa hide tanpa hapus)
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index tambahan biar query home cepat
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_inspirations');
    }
};
