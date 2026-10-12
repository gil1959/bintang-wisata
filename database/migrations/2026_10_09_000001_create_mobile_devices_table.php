<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateMobileDevicesTable extends Migration
{
    public function up(){Schema::create('mobile_devices',function(Blueprint $t){$t->id();$t->unsignedBigInteger('user_id')->index();$t->unsignedBigInteger('access_token_id')->index();$t->string('token_hash',64)->unique();$t->text('token');$t->timestamps();});}
    public function down(){Schema::dropIfExists('mobile_devices');}
}
