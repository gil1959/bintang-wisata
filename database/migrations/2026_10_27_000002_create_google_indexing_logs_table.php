<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleIndexingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('google_indexing_logs')) {
            Schema::create('google_indexing_logs', function (Blueprint $table) {
                $table->id();
                $table->text('url');
                $table->string('action_type')->default('URL_UPDATED');
                $table->integer('status_code')->nullable();
                $table->text('response_message')->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_indexing_logs');
    }
}
