<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateNotificationsIfMissing extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('notifications')) Schema::create('notifications',function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        // The website may already own this shared table. Preserve its notifications on rollback.
    }
}
