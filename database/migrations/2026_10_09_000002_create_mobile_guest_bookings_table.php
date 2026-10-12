<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateMobileGuestBookingsTable extends Migration
{
    public function up(){Schema::create('mobile_guest_bookings',function(Blueprint $t){$t->id();$t->string('token_hash',64)->unique();$t->string('fingerprint',64);$t->unsignedBigInteger('order_id')->nullable()->index();$t->timestamp('expires_at');$t->timestamps();});}
    public function down(){Schema::dropIfExists('mobile_guest_bookings');}
}
