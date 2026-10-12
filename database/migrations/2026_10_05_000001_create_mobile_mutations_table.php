<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateMobileMutationsTable extends Migration
{
    public function up(){Schema::create('mobile_mutations',function(Blueprint $table){$table->id();$table->unsignedBigInteger('user_id');$table->uuid('request_id');$table->string('fingerprint',64);$table->text('response');$table->timestamps();$table->unique(['user_id','request_id']);});}
    public function down(){Schema::dropIfExists('mobile_mutations');}
}
