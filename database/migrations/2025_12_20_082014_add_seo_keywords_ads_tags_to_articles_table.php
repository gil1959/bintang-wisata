<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // SEO tambahan
            $table->string('seo_keywords')->nullable()->after('seo_description');

            // Adsense / Ads code per artikel
            $table->longText('ads_code')->nullable()->after('seo_keywords');

            // Tags: simpan sebagai JSON array
            $table->json('tags')->nullable()->after('ads_code');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords', 'ads_code', 'tags']);
        });
    }
};
