<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{User,Order,Payment};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema,Hash,Mail,Storage};
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class MobileCheckoutTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','cache.default'=>'array','session.driver'=>'array','mail.default'=>'array','app.key'=>'base64:'.base64_encode(str_repeat('a',32))]);
        DB::purge('sqlite');$this->assertSame(':memory:',DB::connection()->getDatabaseName());
        Schema::create('mobile_mutations',function(Blueprint $t){$t->id();$t->unsignedBigInteger('user_id');$t->uuid('request_id');$t->string('fingerprint');$t->text('response');$t->timestamps();$t->unique(['user_id','request_id']);});
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->string('password');$t->timestamp('email_verified_at')->nullable();$t->boolean('is_suspended')->default(false);$t->timestamps();});
        Schema::create('roles',function(Blueprint $t){$t->id();$t->string('name');$t->string('guard_name');$t->timestamps();});
        Schema::create('model_has_roles',function(Blueprint $t){$t->unsignedBigInteger('role_id');$t->string('model_type');$t->unsignedBigInteger('model_id');});
        Schema::create('settings',function(Blueprint $t){$t->id();$t->string('key');$t->text('value')->nullable();});
        Schema::create('tour_packages',function(Blueprint $t){$t->id();$t->string('title');$t->string('slug');$t->boolean('is_active')->default(true);$t->unsignedBigInteger('created_by_partner_id')->nullable();$t->string('partner_review_status')->nullable();});
        Schema::create('tour_package_tiers',function(Blueprint $t){$t->id();$t->unsignedBigInteger('tour_package_id');$t->integer('min_people');$t->integer('max_people')->nullable();$t->integer('price');});
        foreach(['rent_car','ship','hotel','restoran','umrah','mice'] as $type) {
            Schema::create($type.'_packages',function(Blueprint $t){$t->id();$t->string('title');$t->string('slug');$t->boolean('is_active')->default(true);$t->unsignedBigInteger('created_by_partner_id')->nullable();$t->string('partner_review_status')->nullable();foreach(['price_per_12_hours','price_per_24_hours','price_per_hour','price_per_night','price_per_pax'] as $field)$t->integer($field)->default(100000);});
        }
        foreach(['ship','umrah','mice'] as $type) Schema::create($type.'_package_tiers',function(Blueprint $t)use($type){$t->id();$t->unsignedBigInteger($type.'_package_id');$t->string('type')->default('domestic');$t->string('label_text')->default('Pilihan');$t->integer('sort_order')->default(1);$t->integer('price')->default(125000);});
        Schema::create('hotel_rooms',function(Blueprint $t){$t->id();$t->unsignedBigInteger('hotel_package_id');$t->string('name');$t->integer('sort_order')->default(1);$t->integer('price')->default(100000);$t->integer('price_with_breakfast')->default(120000);});
        Schema::create('restoran_menus',function(Blueprint $t){$t->id();$t->unsignedBigInteger('restoran_package_id');$t->string('name');$t->integer('price')->default(50000);$t->boolean('is_ready')->default(true);});
        Schema::create('orders',function(Blueprint $t){
            $t->id();$t->unsignedBigInteger('user_id')->nullable();$t->unsignedBigInteger('product_id')->nullable();
            $t->integer('promo_id')->nullable();$t->string('promo_code')->nullable();$t->string('pickup_date')->nullable();$t->string('return_date')->nullable();$t->integer('total_hours')->nullable();$t->text('order_items')->nullable();
            foreach(['invoice_number','type','product_name','customer_name','customer_email','customer_phone','departure_date','payment_method','payment_status','order_status','affiliate_ref','affiliate_commission_type','affiliate_commission_status','billing_first_name','billing_last_name','billing_country','billing_address','billing_city','billing_state','billing_postal','billing_phone'] as $field)$t->string($field)->nullable();
            foreach(['participants','total_days','subtotal','discount','final_price','unique_code','payable_amount','affiliate_user_id','affiliate_link_id','affiliate_commission_value','affiliate_commission_amount'] as $field)$t->integer($field)->nullable();
            $t->timestamps();
        });
        Schema::create('payments',function(Blueprint $t){$t->id();$t->unsignedBigInteger('order_id');$t->integer('amount');foreach(['method','status','proof_image','gateway_reference','gateway_name','channel_code','payment_url','return_token','gateway_payload'] as $field)$t->text($field)->nullable();$t->timestamps();});
        Schema::create('payment_methods',function(Blueprint $t){$t->id();foreach(['method_name','type','bank_name','account_number','account_holder'] as $field)$t->string($field)->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
        Schema::create('payment_gateways',function(Blueprint $t){$t->id();$t->string('name');$t->string('label')->nullable();$t->text('credentials')->nullable();$t->text('channels')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
        Role::create(['name'=>'user','guard_name'=>'web']);Role::create(['name'=>'partner','guard_name'=>'web']);
        Mail::fake();Storage::fake('public');
    }
    private function user($role='user')
    {
        $user=User::create(['name'=>'Buyer','email'=>'buyer@example.test','password'=>Hash::make('Password123'),'email_verified_at'=>now()]);$user->assignRole($role);Sanctum::actingAs($user);return $user;
    }
    private function order($user)
    {
        return Order::create(['user_id'=>$user->id,'invoice_number'=>'TEST1','type'=>'tour','product_name'=>'Trip','customer_name'=>'Buyer','customer_email'=>$user->email,'customer_phone'=>'08123456789','subtotal'=>100000,'discount'=>0,'final_price'=>100000,'payable_amount'=>100123,'payment_method'=>'manual:1','payment_status'=>'waiting_payment','order_status'=>'pending']);
    }
    public function test_paypal_uses_original_usd_amount_and_does_not_settle_twice()
    {
        $order=$this->order($this->user());
        \App\Models\PaymentGateway::create(['name'=>'paypal','is_active'=>true,'credentials'=>['webhook_id'=>'test-hook']]);
        $payment=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'paypal','gateway_reference'=>'PP1','amount'=>100123,'status'=>'waiting_payment','gateway_payload'=>['paypal_amount_usd'=>'6.25']]);
        $service=\Mockery::mock(\App\Services\Payments\PayPalService::class);$service->shouldReceive('verifyWebhookSignature')->andReturn(false,true,true,true,true);$this->app->instance(\App\Services\Payments\PayPalService::class,$service);
        $notifications=\Mockery::mock(\App\Services\OrderNotificationService::class);$notifications->shouldReceive('sendVerificationEmail')->once();$this->app->instance(\App\Services\OrderNotificationService::class,$notifications);
        $event=['id'=>'EV1','event_type'=>'PAYMENT.CAPTURE.COMPLETED','resource'=>['status'=>'COMPLETED','amount'=>['currency_code'=>'USD','value'=>'6.25'],'supplementary_data'=>['related_ids'=>['order_id'=>'PP1']]]];
        $this->postJson('/api/webhooks/paypal',$event)->assertForbidden();
        $bad=$event;$bad['resource']['amount']['value']='1.00';$this->postJson('/api/webhooks/paypal',$bad)->assertUnprocessable();
        $bad=$event;$bad['resource']['amount']['currency_code']='EUR';$this->postJson('/api/webhooks/paypal',$bad)->assertUnprocessable();
        $this->postJson('/api/webhooks/paypal',$event)->assertOk();$this->postJson('/api/webhooks/paypal',$event)->assertOk();
        $this->assertSame('paid',$payment->fresh()->status);$this->assertSame('paid',$order->fresh()->payment_status);
    }
    public function test_xendit_validates_invoice_amount_currency_and_handles_duplicate_callbacks()
    {
        $order=$this->order($this->user());
        \App\Models\PaymentGateway::create(['name'=>'xendit','is_active'=>true,'credentials'=>['callback_token'=>'test-callback']]);
        $payment=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'xendit','gateway_reference'=>'X-1','amount'=>100123,'status'=>'waiting_payment']);
        $notifications=\Mockery::mock(\App\Services\OrderNotificationService::class);$notifications->shouldReceive('sendVerificationEmail')->once();$this->app->instance(\App\Services\OrderNotificationService::class,$notifications);
        $data=['id'=>'X-1','external_id'=>'TEST1','status'=>'PAID','amount'=>100123,'paid_amount'=>100123,'currency'=>'IDR'];
        $headers=['x-callback-token'=>'test-callback'];
        $this->postJson('/api/webhooks/xendit',$data)->assertForbidden();
        foreach(['external_id'=>'WRONG','amount'=>10,'paid_amount'=>10,'currency'=>'USD'] as $key=>$value) {
            $bad=$data;$bad[$key]=$value;
            $this->postJson('/api/webhooks/xendit',$bad,$headers)->assertUnprocessable();
        }
        $this->assertSame('waiting_payment',$payment->fresh()->status);
        $this->postJson('/api/webhooks/xendit',$data,$headers)->assertOk();
        $this->postJson('/api/webhooks/xendit',$data,$headers)->assertOk();
        $data['status']='EXPIRED';$this->postJson('/api/webhooks/xendit',$data,$headers)->assertOk();
        $this->assertSame('paid',$payment->fresh()->status);$this->assertSame('paid',$order->fresh()->payment_status);
    }
    public function test_doku_callback_authenticates_amount_and_does_not_repeat_or_reverse_settlement()
    {
        $user=$this->user();$order=$this->order($user);
        \App\Models\PaymentGateway::create(['name'=>'doku','is_active'=>true,'credentials'=>['client_id'=>'TEST-MERCHANT','secret_key'=>'test-secret']]);
        $payment=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'doku','gateway_reference'=>'TEST1','amount'=>100123,'status'=>'waiting_payment']);
        $notifications=\Mockery::mock(\App\Services\OrderNotificationService::class);$notifications->shouldReceive('sendVerificationEmail')->once();$this->app->instance(\App\Services\OrderNotificationService::class,$notifications);
        $data=['order'=>['invoice_number'=>'TEST1','amount'=>100123],'transaction'=>['status'=>'SUCCESS']];
        $headers=function($payload){$raw=json_encode($payload);$canonical="Client-Id:TEST-MERCHANT\nRequest-Id:test-event\nRequest-Timestamp:2026-10-08T00:00:00Z\nRequest-Target:/api/webhooks/doku\nDigest:".base64_encode(hash('sha256',$raw,true));return ['Client-Id'=>'TEST-MERCHANT','Request-Id'=>'test-event','Request-Timestamp'=>'2026-10-08T00:00:00Z','Signature'=>'HMACSHA256='.base64_encode(hash_hmac('sha256',$canonical,'test-secret',true))];};
        $this->postJson('/api/webhooks/doku',$data)->assertUnauthorized();
        $wrong=$data;$wrong['order']['amount']=1;
        $this->postJson('/api/webhooks/doku',$wrong,$headers($data))->assertUnauthorized();
        $this->postJson('/api/webhooks/doku',$wrong,$headers($wrong))->assertUnprocessable();
        $this->assertSame('waiting_payment',$payment->fresh()->status);
        $this->postJson('/api/webhooks/doku',$data,$headers($data))->assertOk();
        $this->postJson('/api/webhooks/doku',$data,$headers($data))->assertOk();
        $this->assertSame('paid',$order->fresh()->payment_status);$this->assertSame('approved',$order->fresh()->order_status);
        $data['transaction']['status']='FAILED';
        $this->postJson('/api/webhooks/doku',$data,$headers($data))->assertOk();
        $this->assertSame('paid',$payment->fresh()->status);$this->assertSame('paid',$order->fresh()->payment_status);
    }
    public function test_ipaymu_callback_checks_gateway_transaction_instead_of_trusting_callback_status()
    {
        $user=$this->user();$order=$this->order($user);
        \App\Models\PaymentGateway::create(['name'=>'ipaymu','is_active'=>true,'credentials'=>['va'=>'TEST-VA','api_key'=>'test-api','mode'=>'sandbox']]);
        $payment=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'ipaymu','gateway_reference'=>'SESSION-1','amount'=>100123,'status'=>'waiting_payment']);
        $notifications=\Mockery::mock(\App\Services\OrderNotificationService::class);$notifications->shouldReceive('sendVerificationEmail')->once();$this->app->instance(\App\Services\OrderNotificationService::class,$notifications);
        $data=['trx_id'=>4719,'sid'=>'SESSION-1','status_code'=>1,'amount'=>'100123','additional_info'=>[]];ksort($data,SORT_STRING);
        $headers=['X-Signature'=>hash_hmac('sha256',json_encode($data),'TEST-VA')];
        $verified=[];
        \Illuminate\Support\Facades\Http::fake(function($request)use(&$verified){return \Illuminate\Support\Facades\Http::response(['Status'=>200,'Data'=>$verified]);});
        $this->postJson('/api/webhooks/ipaymu',$data)->assertUnauthorized();
        \Illuminate\Support\Facades\Http::assertNothingSent();
        $verified=['TransactionId'=>4719,'SessionId'=>'SESSION-1','Amount'=>100123,'Status'=>0];

        $this->postJson('/api/webhooks/ipaymu',$data,$headers)->assertOk();
        $this->assertSame('waiting_payment',$payment->fresh()->status);
        $verified['Status']=1;$verified['Amount']=1;

        $this->postJson('/api/webhooks/ipaymu',$data,$headers)->assertUnprocessable();
        $verified['Amount']=100123;$verified['SessionId']='OTHER-SESSION';

        $this->postJson('/api/webhooks/ipaymu',$data,$headers)->assertUnprocessable();
        $verified['SessionId']='SESSION-1';

        $this->postJson('/api/webhooks/ipaymu',$data,$headers)->assertOk();
        $this->postJson('/api/webhooks/ipaymu',$data,$headers)->assertOk();
        $this->assertSame('paid',$payment->fresh()->status);$this->assertSame('approved',$order->fresh()->order_status);
    }
    public function test_tripay_flat_callback_checks_fees_and_preserves_paid_status()
    {
        $user=$this->user();$order=$this->order($user);
        \App\Models\PaymentGateway::create(['name'=>'tripay','is_active'=>true,'credentials'=>['private_key'=>'test-private']]);
        $payment=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'tripay','gateway_reference'=>'T-1','amount'=>100123,'status'=>'waiting_payment']);
        $notifications=\Mockery::mock(\App\Services\OrderNotificationService::class);$notifications->shouldReceive('sendVerificationEmail')->once();$this->app->instance(\App\Services\OrderNotificationService::class,$notifications);
        $data=['reference'=>'T-1','merchant_ref'=>'TEST1','total_amount'=>104373,'fee_customer'=>4250,'is_closed_payment'=>1,'status'=>'PAID'];
        $headers=fn($body)=>['X-Callback-Signature'=>hash_hmac('sha256',json_encode($body),'test-private'),'X-Callback-Event'=>'payment_status'];
        $this->postJson('/api/webhooks/tripay',$data)->assertForbidden();
        $bad=$data;$bad['total_amount']=5000;
        $this->postJson('/api/webhooks/tripay',$bad,$headers($bad))->assertUnprocessable();
        $this->postJson('/api/webhooks/tripay',$data,$headers($data))->assertOk()->assertJsonPath('success',true);
        $this->postJson('/api/webhooks/tripay',$data,$headers($data))->assertOk();
        $data['status']='EXPIRED';$this->postJson('/api/webhooks/tripay',$data,$headers($data))->assertOk();
        $this->assertSame('paid',$payment->fresh()->status);$this->assertSame('approved',$order->fresh()->order_status);
    }
    public function test_midtrans_checks_gateway_scope_amount_and_fraud_status()
    {
        $user=$this->user();$order=$this->order($user);
        \App\Models\PaymentGateway::create(['name'=>'midtrans','is_active'=>true,'credentials'=>['server_key'=>'test-server']]);
        $payment=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'midtrans','gateway_reference'=>'TEST1','amount'=>100123,'status'=>'waiting_payment']);
        $unrelated=Payment::create(['order_id'=>$order->id,'method'=>'gateway','gateway_name'=>'doku','gateway_reference'=>'TEST1','amount'=>100123,'status'=>'waiting_payment']);
        $notifications=\Mockery::mock(\App\Services\OrderNotificationService::class);$notifications->shouldReceive('sendVerificationEmail')->once();$this->app->instance(\App\Services\OrderNotificationService::class,$notifications);
        $data=['order_id'=>'TEST1','gross_amount'=>'100123.00','status_code'=>'200','transaction_status'=>'capture','fraud_status'=>'challenge','currency'=>'IDR'];
        $sign=fn($body)=>array_merge($body,['signature_key'=>hash('sha512',$body['order_id'].$body['status_code'].$body['gross_amount'].'test-server')]);
        $this->postJson('/api/webhooks/midtrans',$data)->assertUnauthorized();
        $this->postJson('/api/webhooks/midtrans',$sign($data))->assertOk();$this->assertSame('waiting_payment',$payment->fresh()->status);
        $data['fraud_status']='accept';$wrong=$data;$wrong['gross_amount']='1.00';
        $this->postJson('/api/webhooks/midtrans',$sign($wrong))->assertUnprocessable();
        $this->postJson('/api/webhooks/midtrans',$sign($data))->assertOk();
        $this->assertSame('paid',$payment->fresh()->status);$this->assertSame('waiting_payment',$unrelated->fresh()->status);
        $data['transaction_status']='expire';$this->postJson('/api/webhooks/midtrans',$sign($data))->assertOk();
        $this->assertSame('paid',$order->fresh()->payment_status);
    }
    public function test_booking_uses_server_prices_and_deduplicates_retries()
    {
        $user=$this->user();
        DB::table('tour_packages')->insert(['id'=>1,'title'=>'Trip','slug'=>'trip']);
        DB::table('tour_package_tiers')->insert(['tour_package_id'=>1,'min_people'=>1,'max_people'=>10,'price'=>125000]);
        $data=['name'=>'Buyer','phone'=>'08123456789','email'=>'spoof@example.test','departure_date'=>now()->addDay()->toDateString(),'participants'=>2,'final_price'=>1,'idempotency_key'=>(string)\Illuminate\Support\Str::uuid()];
        $this->postJson('/api/v1/bookings/tour/trip',$data)->assertCreated()->assertJsonPath('data.final_price',250000);
        \Illuminate\Support\Facades\Cache::flush();
        $this->postJson('/api/v1/bookings/tour/trip',$data)->assertCreated();
        $this->assertSame(1,Order::count());$this->assertSame($user->email,Order::first()->customer_email);
        $data['participants']=3;$this->postJson('/api/v1/bookings/tour/trip',$data)->assertStatus(409);
    }
    public function test_guest_booking_capability_retry_checkout_and_proof_without_account_access()
    {
        Schema::create('mobile_guest_bookings',function(Blueprint $t){$t->id();$t->string('token_hash')->unique();$t->string('fingerprint');$t->unsignedBigInteger('order_id')->nullable();$t->timestamp('expires_at');$t->timestamps();});
        DB::table('tour_packages')->insert(['id'=>1,'title'=>'Trip','slug'=>'trip']);
        DB::table('tour_package_tiers')->insert(['tour_package_id'=>1,'min_people'=>1,'max_people'=>10,'price'=>125000]);
        $headers=['X-Guest-Token'=>str_repeat('a',64)];
        $data=['name'=>'Guest','phone'=>'08123456789','email'=>'guest@example.test','departure_date'=>now()->addDay()->toDateString(),'participants'=>2,'idempotency_key'=>(string)\Illuminate\Support\Str::uuid()];
        $this->postJson('/api/v1/guest/bookings/tour/trip',$data,$headers)->assertCreated()->assertJsonPath('data.final_price',250000);
        $this->postJson('/api/v1/guest/bookings/tour/trip',$data,$headers)->assertCreated();
        $this->assertSame(1,Order::count());$this->assertSame(0,User::count());$this->assertNull(Order::first()->user_id);
        $this->getJson('/api/v1/guest/resume',$headers)->assertOk()->assertJsonPath('data.id',1);
        $this->getJson('/api/v1/guest/checkout/1',['X-Guest-Token'=>str_repeat('b',64)])->assertForbidden();
        $this->getJson('/api/v1/guest/checkout/2',$headers)->assertForbidden();
        $this->getJson('/api/v1/guest/checkout/1',$headers)->assertOk();
        DB::table('payment_methods')->insert(['id'=>1,'type'=>'manual','method_name'=>'Bank','bank_name'=>'Bank']);
        $billing=['billing_first_name'=>'Guest','billing_last_name'=>'Buyer','billing_country'=>'Indonesia','billing_address'=>'Street','billing_city'=>'City','billing_state'=>'State','billing_postal'=>'12345','billing_phone'=>'08123456789','payment_method'=>'manual:1'];
        $this->postJson('/api/v1/guest/checkout/1/process',$billing,$headers)->assertOk();
        $this->assertSame(0,User::count());
        $this->post('/api/v1/guest/checkout/1/proof',['proof'=>UploadedFile::fake()->image('proof.jpg')],array_merge($headers,['Accept'=>'application/json']))->assertOk();
        $this->assertSame('waiting_verification',Order::first()->payment_status);
        DB::table('mobile_guest_bookings')->update(['expires_at'=>now()->subDay()]);
        $this->getJson('/api/v1/guest/resume',$headers)->assertForbidden();
    }
    public function test_checkout_rejects_wrong_owner_and_unverified_or_partner_accounts()
    {
        $user=$this->user();$order=$this->order($user);$order->update(['user_id'=>999]);
        $this->getJson('/api/v1/checkout/'.$order->id)->assertNotFound();
        $user->email_verified_at=null;$user->save();
        $this->getJson('/api/v1/checkout/'.$order->id)->assertForbidden();
        $user->email_verified_at=now();$user->save();$user->syncRoles('partner');
        $this->getJson('/api/v1/checkout/'.$order->id)->assertForbidden();
    }
    public function test_promo_codes_use_server_discount_and_cannot_be_reused_for_a_new_order()
    {
        $this->user();
        Schema::create('promos',function(Blueprint $t){$t->id();$t->string('code');$t->string('type');$t->integer('value');$t->integer('max_discount')->nullable();$t->integer('min_price')->nullable();$t->date('start_date')->nullable();$t->date('end_date')->nullable();$t->boolean('is_active');$t->timestamps();});
        \App\Models\Promo::create(['code'=>'HEMAT','type'=>'percentage','value'=>10,'is_active'=>true]);
        DB::table('tour_packages')->insert(['id'=>1,'title'=>'Trip','slug'=>'trip']);
        DB::table('tour_package_tiers')->insert(['tour_package_id'=>1,'min_people'=>1,'max_people'=>10,'price'=>125000]);
        $body=['name'=>'Buyer','phone'=>'08123456789','departure_date'=>now()->addDay()->toDateString(),'participants'=>2,'promo_code'=>'hemat','idempotency_key'=>(string)\Illuminate\Support\Str::uuid()];
        $this->postJson('/api/v1/bookings/tour/trip',$body)->assertCreated()->assertJsonPath('data.final_price',225000);
        $this->assertSame('HEMAT',Order::first()->promo_code);
        $this->postJson('/api/v1/bookings/tour/trip',$body)->assertCreated();
        $body['idempotency_key']=(string)\Illuminate\Support\Str::uuid();
        $this->postJson('/api/v1/bookings/tour/trip',$body)->assertUnprocessable();
        $this->assertSame(1,Order::count());
        $body['promo_code']='INVALID';
        $this->postJson('/api/v1/bookings/tour/trip',$body)->assertUnprocessable();
        $this->assertSame(1,DB::table('mobile_mutations')->count());
    }
    public function test_booking_all_other_product_types_uses_existing_web_pricing()
    {
        $this->user();
        $date=now()->addDays(2)->toDateString();$later=now()->addDays(4)->toDateString();
        foreach(['rent_car','ship','hotel','restoran','umrah','mice'] as $type) {
            DB::table($type.'_packages')->insert(['id'=>1,'title'=>'Trip '.$type,'slug'=>'sample']);
            if(in_array($type,['ship','umrah','mice']))DB::table($type.'_package_tiers')->insert(['id'=>1,$type.'_package_id'=>1]);
        }
        DB::table('restoran_menus')->insert(['id'=>1,'restoran_package_id'=>1,'name'=>'Menu']);
        $cases=[
            'rent_car'=>[['pickup_date'=>$date.' 09:00','return_date'=>$date.' 21:00','duration_type'=>'12_hours'],100000],
            'ship'=>[['departure_date'=>$date,'tier_id'=>1,'qty'=>2],250000],
            'hotel'=>[['checkin_date'=>$date,'checkout_date'=>$later,'room_count'=>2],400000],
            'restoran'=>[['departure_date'=>$date,'pickup_time'=>'12:00','participants'=>2,'menus'=>[['id'=>1,'qty'=>3]]],150000],
            'umrah'=>[['booking_date'=>$date,'tier_id'=>1,'participants'=>2],250000],
            'mice'=>[['departure_date'=>$date,'tier_id'=>1,'participants'=>2],250000],
        ];
        foreach($cases as $type=>[$fields,$amount]) {
            $data=$fields+['name'=>'Buyer','phone'=>'08123456789','idempotency_key'=>(string)\Illuminate\Support\Str::uuid()];
            $result=$this->postJson('/api/v1/bookings/'.$type.'/sample',$data)->assertCreated();
            $this->assertEquals($amount,$result->json('data.final_price'),$type);
        }
        $this->assertSame(6,Order::count());
    }
    public function test_manual_proof_remains_pending_and_cannot_be_submitted_twice()
    {
        $order=$this->order($this->user());
        $image=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+j9WQAAAAASUVORK5CYII=');
        $proof=UploadedFile::fake()->createWithContent('proof.png',$image);
        $this->post('/api/v1/checkout/'.$order->id.'/proof',['proof'=>$proof],['Accept'=>'application/json'])->assertOk()->assertJsonPath('data.payment_status','waiting_verification');
        $this->assertSame('pending',$order->fresh()->order_status);
        $this->assertSame('waiting_verification',Payment::first()->status);
        $this->assertSame(100123.0,Payment::first()->amount);
        $this->postJson('/api/v1/checkout/'.$order->id.'/proof')->assertStatus(409);
        $this->assertSame(1,Payment::count());
    }
    public function test_checkout_does_not_expose_gateway_credentials_and_reuses_payment_url()
    {
        $order=$this->order($this->user());
        $order->update(['payment_method'=>'gateway:tripay:BRIVA']);
        DB::table('payment_gateways')->insert(['name'=>'tripay','label'=>'Tripay','credentials'=>json_encode(['api_key'=>'private-secret']),'channels'=>json_encode([['channel_code'=>'BRIVA','name'=>'BRI','active'=>true]])]);
        Payment::create(['order_id'=>$order->id,'method'=>'gateway','status'=>'waiting_payment','amount'=>100123,'gateway_name'=>'tripay','channel_code'=>'BRIVA','payment_url'=>'https://payment.example.test/invoice','return_token'=>'private-token']);
        $response=$this->getJson('/api/v1/checkout/'.$order->id)->assertOk()->assertJsonCount(1,'data.methods');
        $this->assertStringNotContainsString('private-secret',$response->getContent());$this->assertStringNotContainsString('private-token',$response->getContent());
        $this->postJson('/api/v1/checkout/'.$order->id.'/gateway')->assertOk()->assertJsonPath('data.payment_url','https://payment.example.test/invoice');
        DB::table('payment_gateways')->update(['is_active'=>false]);
        $this->postJson('/api/v1/checkout/'.$order->id.'/gateway')->assertNotFound();
    }
    public function test_checkout_validates_method_and_billing_and_blocks_paid_orders()
    {
        $order=$this->order($this->user());
        DB::table('payment_methods')->insert(['id'=>1,'method_name'=>'Transfer BRI','type'=>'manual','bank_name'=>'BRI','account_number'=>'123','account_holder'=>'Travel']);
        $order->update(['unique_code'=>123]);
        $body=['billing_first_name'=>'Buyer','billing_last_name'=>'Test','billing_country'=>'Indonesia','billing_address'=>'Alamat','billing_city'=>'Jakarta','billing_state'=>'Jakarta','billing_postal'=>'12345','billing_phone'=>'08123456789','payment_method'=>'manual:1'];
        $this->postJson('/api/v1/checkout/'.$order->id,$body)->assertOk()->assertJsonPath('data.payment_method','manual:1')->assertJsonPath('data.payable_amount',100123);
        $body['payment_method']='manual:999';
        $this->postJson('/api/v1/checkout/'.$order->id,$body)->assertUnprocessable();
        $this->assertSame('manual:1',$order->fresh()->payment_method);
        $order->update(['payment_status'=>'paid']);
        $this->postJson('/api/v1/checkout/'.$order->id,$body)->assertStatus(409);
    }
    public function test_gateway_creation_uses_server_amount_and_persists_return_token()
    {
        $order=$this->order($this->user());$order->update(['payment_method'=>'gateway:tripay:BRIVA']);
        DB::table('payment_gateways')->insert(['name'=>'tripay','credentials'=>'{}','channels'=>json_encode([['channel_code'=>'BRIVA','name'=>'BRI','active'=>true]])]);
        $service=\Mockery::mock(\App\Services\Payments\TripayService::class);
        $service->shouldReceive('createTransaction')->once()->withArgs(function($credentials,$payload){return $payload['amount']===100123&&str_contains($payload['return_url'],'?token=')&&strlen((string)parse_url($payload['return_url'],PHP_URL_QUERY))>20;})->andReturn(['data'=>['reference'=>'REF1','checkout_url'=>'https://payment.example.test/new']]);
        $this->app->instance(\App\Services\Payments\TripayService::class,$service);
        $this->postJson('/api/v1/checkout/'.$order->id.'/gateway',['amount'=>1])->assertOk()->assertJsonPath('data.payment_url','https://payment.example.test/new');
        $this->assertNotEmpty(Payment::first()->return_token);
        $this->assertSame('waiting_payment',$order->fresh()->payment_status);
        $this->postJson('/api/v1/checkout/'.$order->id.'/gateway')->assertOk();
        $this->assertSame(1,Payment::count());
    }
}
