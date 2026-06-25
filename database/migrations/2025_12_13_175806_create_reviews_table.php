<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // polymorphic columns + index otomatis
            $table->nullableMorphs('reviewable');

            $table->string('name', 80);
            $table->string('email', 120);
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();

            $table->timestamps();

            // jangan bikin index reviewable lagi karena nullableMorphs sudah buat
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
