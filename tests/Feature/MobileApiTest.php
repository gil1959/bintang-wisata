<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\MobileVerifyEmail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema,Hash,Notification,URL};
use Spatie\Permission\Models\Role;

class MobileApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','cache.default'=>'array','mail.default'=>'array','app.key'=>'base64:'.base64_encode(str_repeat('a',32))]);
        DB::purge('sqlite');
        $this->assertSame(':memory:',DB::connection()->getDatabaseName());
        Schema::create('users',function(Blueprint $t){
            $t->id();$t->string('name');$t->string('email')->unique();$t->string('password');$t->rememberToken();$t->timestamp('email_verified_at')->nullable();
            $t->boolean('is_suspended')->default(false);$t->string('phone')->nullable();$t->string('address')->nullable();$t->string('partner_type')->nullable();$t->string('affiliate_status')->nullable();$t->timestamps();
        });
        Schema::create('roles',function(Blueprint $t){$t->id();$t->string('name');$t->string('guard_name');$t->timestamps();});
        Schema::create('permissions',function(Blueprint $t){$t->id();$t->string('name');$t->string('guard_name');$t->timestamps();});
        Schema::create('model_has_roles',function(Blueprint $t){$t->unsignedBigInteger('role_id');$t->string('model_type');$t->unsignedBigInteger('model_id');});
        Schema::create('model_has_permissions',function(Blueprint $t){$t->unsignedBigInteger('permission_id');$t->string('model_type');$t->unsignedBigInteger('model_id');});
        Schema::create('role_has_permissions',function(Blueprint $t){$t->unsignedBigInteger('role_id');$t->unsignedBigInteger('permission_id');});
        Schema::create('personal_access_tokens',function(Blueprint $t){$t->id();$t->morphs('tokenable');$t->string('name');$t->string('token',64)->unique();$t->text('abilities')->nullable();$t->timestamp('last_used_at')->nullable();$t->timestamps();});
        Schema::create('rent_car_packages',function(Blueprint $t){$t->id();$t->string('slug');$t->string('title');$t->boolean('is_active');$t->unsignedBigInteger('created_by_partner_id')->nullable();$t->string('partner_review_status')->nullable();$t->timestamps();});
        foreach(['tour_packages','ship_packages','hotel_packages','restoran_packages'] as $table) Schema::create($table,function(Blueprint $t){$t->id();$t->unsignedBigInteger('created_by_partner_id')->nullable();});
        Schema::create('orders',function(Blueprint $t){$t->id();$t->unsignedBigInteger('user_id')->nullable();$t->string('customer_email');$t->string('invoice_number');$t->string('product_name');$t->string('type');$t->unsignedBigInteger('product_id');$t->string('order_status')->default('pending');$t->string('payment_status')->default('unpaid');$t->integer('final_price')->default(100000);$t->timestamps();});
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        foreach(['user','partner','admin','site_moderator'] as $role) Role::create(['name'=>$role,'guard_name'=>'web']);
        Notification::fake();
    }
    public function test_registration_ignores_role_injection_and_verification_is_signed()
    {
        $this->postJson('/api/v1/auth/register',['name'=>'Test','email'=>'test@example.test','password'=>'Password123','password_confirmation'=>'Password123','roles'=>['admin']])
            ->assertCreated()->assertJsonPath('data.user.roles.0','user')->assertJsonPath('data.user.email_verified',false);
        $user=User::first();Notification::assertSentTo($user,MobileVerifyEmail::class);
        $url=URL::temporarySignedRoute('mobile.verify',now()->addMinutes(10),['id'=>$user->id,'hash'=>sha1($user->email)]);
        $this->getJson($url.'&tampered=1')->assertForbidden();
        $this->getJson($url)->assertOk();$this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
    public function test_login_reports_each_role_and_denies_suspended_accounts()
    {
        foreach(['user','partner','admin','site_moderator'] as $role){
            $user=User::create(['name'=>$role,'email'=>$role.'@example.test','password'=>Hash::make('Password123')]);$user->assignRole($role);
            $this->postJson('/api/v1/auth/login',['email'=>$user->email,'password'=>'Password123'])->assertOk()->assertJsonPath('data.user.roles.0',$role);
        }
        $user->update(['is_suspended'=>true]);
        $this->postJson('/api/v1/auth/login',['email'=>$user->email,'password'=>'Password123'])->assertForbidden();
    }
    public function test_catalog_hides_inactive_and_unapproved_partner_products()
    {
        DB::table('rent_car_packages')->insert([
            ['slug'=>'public','title'=>'Mobil Publik','is_active'=>1,'created_by_partner_id'=>null,'partner_review_status'=>null],
            ['slug'=>'inactive','title'=>'Mobil Nonaktif','is_active'=>0,'created_by_partner_id'=>null,'partner_review_status'=>null],
            ['slug'=>'pending','title'=>'Mobil Pending','is_active'=>1,'created_by_partner_id'=>12,'partner_review_status'=>'pending'],
        ]);
        $this->getJson('/api/v1/catalog/rent_car')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.slug','public');
        $this->getJson('/api/v1/catalog/rent_car/pending')->assertNotFound();
        $this->getJson('/api/v1/catalog/unknown')->assertNotFound();
        $this->getJson('/api/v1/catalog/types')->assertOk()->assertJsonCount(7,'data');
    }
    public function test_account_requires_authentication()
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }
    public function test_home_blocks_hide_inactive_rows_and_respect_popup_schedule()
    {
        Schema::create('settings',function(Blueprint $t){$t->id();$t->string('key');$t->text('value')->nullable();});
        Schema::create('home_promo_banners',function(Blueprint $t){$t->id();$t->string('section');$t->string('thumbnail_path');$t->string('link_url');$t->integer('sort_order')->default(0);$t->boolean('is_active');});
        Schema::create('destination_inspirations',function(Blueprint $t){$t->id();$t->string('title');$t->string('image_path')->nullable();$t->integer('sort_order')->default(0);$t->boolean('is_active');$t->integer('tour_category_id')->nullable();$t->integer('tour_subcategory_id')->nullable();});
        foreach(['client_logos','footer_logos'] as $table)Schema::create($table,function(Blueprint $t){$t->id();$t->string('name');$t->string('image_path');$t->string('url')->nullable();$t->integer('sort_order')->default(0);$t->boolean('is_active');});
        Schema::create('popup_widgets',function(Blueprint $t){$t->id();$t->boolean('is_enabled');$t->boolean('show_on_mobile');$t->timestamp('start_at')->nullable();$t->timestamp('end_at')->nullable();$t->text('include_paths')->nullable();$t->text('exclude_paths')->nullable();$t->string('title');});
        DB::table('home_promo_banners')->insert([['section'=>'discount','thumbnail_path'=>'ok.png','link_url'=>'https://bintangwisataholiday.com/paket-tour','is_active'=>true],['section'=>'discount','thumbnail_path'=>'hidden.png','link_url'=>'https://example.test','is_active'=>false]]);
        DB::table('settings')->insert(['key'=>'home_tabs','value'=>json_encode([['label'=>'Bad','url'=>'javascript:bad()']])]);
        DB::table('popup_widgets')->insert(['is_enabled'=>true,'show_on_mobile'=>true,'start_at'=>now()->addDay(),'title'=>'Future popup']);
        $response=$this->getJson('/api/v1/public/home/blocks')->assertOk()->assertJsonPath('data.popup',null)->assertDontSee('hidden.png')->assertDontSee('javascript:');
        DB::table('popup_widgets')->update(['start_at'=>now()->subDay()]);
        $this->getJson('/api/v1/public/home/blocks')->assertOk()->assertJsonPath('data.popup.title','Future popup');
        DB::table('popup_widgets')->update(['exclude_paths'=>'["/"]']);
        $this->getJson('/api/v1/public/home/blocks')->assertOk()->assertJsonPath('data.popup',null);
    }
    public function test_push_device_registration_requires_account_and_encrypts_device_token()
    {
        Schema::create('mobile_devices',function(Blueprint $t){$t->id();$t->unsignedBigInteger('user_id');$t->unsignedBigInteger('access_token_id');$t->string('token_hash')->unique();$t->text('token');$t->timestamps();});
        $body=['token'=>str_repeat('device-token',10)];
        $this->postJson('/api/v1/account/device',$body)->assertUnauthorized();
        $user=User::create(['name'=>'Push user','email'=>'push@example.test','password'=>Hash::make('Password123')]);$user->assignRole('user');
        $access=$user->createToken('android');$headers=['Authorization'=>'Bearer '.$access->plainTextToken];
        $this->postJson('/api/v1/account/device',$body,$headers)->assertOk();
        $this->postJson('/api/v1/account/device',$body,$headers)->assertOk();
        $this->assertSame(1,DB::table('mobile_devices')->count());
        $device=DB::table('mobile_devices')->first();$this->assertNotSame($body['token'],$device->token);$this->assertSame($body['token'],decrypt($device->token));
        $this->postJson('/api/v1/account/device/remove',$body,$headers)->assertOk();$this->assertSame(0,DB::table('mobile_devices')->count());
    }
    public function test_catalog_filters_category_search_and_price_without_exposing_pending_products()
    {
        Schema::create('rent_car_categories',function(Blueprint $t){$t->id();$t->string('name');});
        Schema::table('rent_car_packages',function(Blueprint $t){$t->unsignedBigInteger('category_id')->nullable();$t->integer('price_per_12_hours')->default(0);});
        DB::table('rent_car_categories')->insert([['id'=>1,'name'=>'Mobil'],['id'=>2,'name'=>'Bus']]);
        foreach([['Avanza',1,300,1,null,null],['Innova',1,500,1,null,null],['Bus',2,1000,1,null,null],['Hidden',1,1,1,99,'pending']] as $i=>$row)DB::table('rent_car_packages')->insert(['title'=>$row[0],'slug'=>strtolower($row[0]),'category_id'=>$row[1],'price_per_12_hours'=>$row[2],'is_active'=>$row[3],'created_by_partner_id'=>$row[4],'partner_review_status'=>$row[5]]);
        $this->getJson('/api/v1/catalog/rent_car/filters')->assertOk()->assertJsonCount(2,'data.categories');
        $this->getJson('/api/v1/catalog/rent_car?category=1&sort=price_desc')->assertOk()->assertJsonCount(2,'data')->assertJsonPath('data.0.title','Innova');
        $this->getJson('/api/v1/catalog/rent_car?q=avanza')->assertOk()->assertJsonCount(1,'data');
        $this->getJson('/api/v1/catalog/rent_car?sort=invalid')->assertStatus(422);
        $this->getJson('/api/v1/catalog/rent_car?subcategory=2')->assertStatus(422);
    }
    public function test_reports_enforce_role_dates_payment_filter_and_selection()
    {
        $user=User::create(['name'=>'Report user','email'=>'report@example.test','password'=>Hash::make('Password123')]);$user->assignRole('user');
        \Laravel\Sanctum\Sanctum::actingAs($user);
        $url='/api/v1/admin/reports/orders?from=2026-10-01&to=2026-10-08';
        $this->getJson($url)->assertForbidden();
        $user->syncRoles(['partner']);$this->getJson($url)->assertForbidden();
        $user->syncRoles(['site_moderator']);$this->getJson($url)->assertForbidden();
        $user->syncRoles(['admin']);
        foreach([['2026-10-01 00:00:00','paid',120000],['2026-10-08 23:59:59','unpaid',230000],['2026-10-09 00:00:00','paid',999000]] as $index=>$entry) {
            DB::table('orders')->insert(['id'=>$index+1,'customer_email'=>'customer@example.test','invoice_number'=>'REPORT'.$index,'product_name'=>'Produk','type'=>'tour','product_id'=>1,'payment_status'=>$entry[1],'final_price'=>$entry[2],'created_at'=>$entry[0]]);
        }
        $this->getJson($url)->assertOk()->assertJsonCount(2,'data')->assertJsonPath('summary.total_orders',2)->assertJsonPath('summary.total_amount',350000);
        $this->getJson($url.'&paid=1')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('summary.total_amount',120000);
        $this->getJson($url.'&order_ids[]=2&order_ids[]=3')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.id',2);
        $this->getJson('/api/v1/admin/reports/orders?from=2026-10-09&to=2026-10-01')->assertStatus(422);
        $this->getJson('/api/v1/admin/reports/orders?from=bad&to=2026-10-01')->assertStatus(422);
    }
    public function test_home_respects_admin_selection_visibility_and_does_not_return_secret_settings()
    {
        Schema::create('settings',function(Blueprint $t){$t->id();$t->string('key');$t->text('value')->nullable();$t->timestamps();});
        Schema::table('tour_packages',function(Blueprint $t){foreach(['title','slug','label','thumbnail_path','partner_review_status'] as $field)$t->text($field)->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
        foreach(['home_ship_promo_enabled'=>'0','home_umrah_promo_enabled'=>'0','home_mice_promo_enabled'=>'0','home_promo_mode'=>'custom','home_promo_custom_ids'=>'[2,1,3]','hero_title'=>'Judul admin','mail_password'=>'PRIVATE'] as $key=>$value)DB::table('settings')->insert(compact('key','value'));
        DB::table('tour_packages')->insert([['id'=>1,'title'=>'First','slug'=>'first','created_by_partner_id'=>null,'partner_review_status'=>null],['id'=>2,'title'=>'Second','slug'=>'second','created_by_partner_id'=>null,'partner_review_status'=>null],['id'=>3,'title'=>'Hidden','slug'=>'hidden','created_by_partner_id'=>999,'partner_review_status'=>'pending']]);
        $this->getJson('/api/v1/public/home')->assertOk()->assertJsonPath('data.hero.title','Judul admin')->assertJsonPath('data.sections.0.products.0.slug','second')->assertJsonCount(2,'data.sections.0.products')->assertDontSee('PRIVATE');
        DB::table('settings')->insert(['key'=>'home_promo_enabled','value'=>'0']);
        $this->getJson('/api/v1/public/home')->assertOk()->assertJsonCount(0,'data.sections');
        $this->getJson('/api/v1/public/pages/about')->assertOk()->assertJsonCount(11,'data.sections');
    }
    public function test_admin_can_create_edit_and_delete_each_product_type_with_nested_forms()
    {
        \Illuminate\Support\Facades\Bus::fake();\Illuminate\Support\Facades\Storage::fake('public');
        $admin=User::create(['name'=>'Products admin','email'=>'all-products@example.test','password'=>Hash::make('Password123')]);$admin->assignRole('admin');\Laravel\Sanctum\Sanctum::actingAs($admin);
        foreach(\App\Services\Mobile\ProductForms::TYPES as $type=>$spec){
            $class='App\\Models\\'.$spec[0];$model=new $class;$table=$model->getTable();
            if(!Schema::hasTable($table))Schema::create($table,function(Blueprint $t){$t->id();});
            $existing=Schema::getColumnListing($table);
            Schema::table($table,function(Blueprint $t)use($model,$existing){foreach(array_unique(array_diff($model->getFillable(),$existing)) as $field)$t->text($field)->nullable();if(!in_array('created_at',$existing))$t->timestamps();});
            if($spec[1]){$categoryClass='App\\Models\\'.$spec[1];$categoryTable=(new $categoryClass)->getTable();if(!Schema::hasTable($categoryTable))Schema::create($categoryTable,function(Blueprint $t){$t->id();$t->string('name');$t->string('slug')->nullable();$t->unsignedBigInteger('parent_id')->nullable();$t->unsignedBigInteger('created_by_partner_id')->nullable();$t->timestamps();});DB::table($categoryTable)->insert(['id'=>1,'name'=>'Category','slug'=>'category']);}
            foreach($spec[4] as $relation){$related=$model->$relation()->getRelated();$relationTable=$related->getTable();if(!Schema::hasTable($relationTable))Schema::create($relationTable,function(Blueprint $t)use($related){$t->id();foreach($related->getFillable() as $field)$t->text($field)->nullable();$t->timestamps();});}
            $tier=['label_text'=>'Standard','price'=>150000,'min_people'=>1,'max_people'=>10,'type'=>'domestic','is_custom'=>'0'];
            $payload=['title'=>'Package '.$type,'slug'=>'package-'.$type,'category_id'=>1,'duration_text'=>'2 hari','flight_info'=>'not_included','is_active'=>'1','price_per_12_hours'=>150000,'price_per_24_hours'=>250000,'price_per_night'=>200000,'price_per_pax'=>75000,'tiers'=>in_array($type,['tour','mice'])?['domestic'=>[$tier]]:[array_merge($tier,['type'=>'weekday'])],'rooms'=>[['name'=>'Room','price'=>200000,'is_ready'=>'1']],'menus'=>[['name'=>'Menu','price'=>75000,'is_ready'=>'1']]];
            $this->postJson('/api/v1/manage/products/'.$type.'/save',['payload'=>json_encode($payload)])->assertCreated();
            $saved=$class::firstOrFail();
            $this->getJson('/api/v1/manage/products/'.$type.'/form/'.$saved->id)->assertOk()->assertJsonPath('data.values.title','Package '.$type);
            $payload['title']='Updated '.$type;
            $this->postJson('/api/v1/manage/products/'.$type.'/save/'.$saved->id,['payload'=>json_encode($payload)])->assertOk();
            $this->assertSame('Updated '.$type,$saved->fresh()->title);
            $this->postJson('/api/v1/manage/products/'.$type.'/'.$saved->id.'/delete')->assertOk();
            $this->assertSame(0,$class::count());
        }
    }
    public function test_partner_hotel_multipart_saves_gallery_and_room_photos_and_blocks_foreign_children()
    {
        \Illuminate\Support\Facades\Storage::fake('public');\Illuminate\Support\Facades\Bus::fake();
        $existing=Schema::getColumnListing('hotel_packages');
        Schema::table('hotel_packages',function(Blueprint $t)use($existing){foreach((new \App\Models\HotelPackage)->getFillable() as $field)if(!in_array($field,$existing))$t->text($field)->nullable();$t->timestamps();});
        Schema::create('hotel_rooms',function(Blueprint $t){$t->id();foreach((new \App\Models\HotelRoom)->getFillable() as $field)$t->text($field)->nullable();$t->timestamps();});
        Schema::create('hotel_package_photos',function(Blueprint $t){$t->id();$t->unsignedBigInteger('hotel_package_id');$t->string('file_path');$t->timestamps();});
        $partner=User::create(['name'=>'Hotel owner','email'=>'hotel@example.test','password'=>Hash::make('Password123'),'partner_type'=>'agency_hotel_vila']);$partner->assignRole('partner');\Laravel\Sanctum\Sanctum::actingAs($partner);
        $png=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX1sAAAAASUVORK5CYII=');
        $file=fn()=>\Illuminate\Http\UploadedFile::fake()->createWithContent('photo.png',$png);
        $payload=['title'=>'Hotel Test','price_per_night'=>300000,'property_type'=>'Hotel','rooms'=>[['name'=>'Deluxe','price'=>350000,'max_guests'=>2,'available_rooms'=>3,'is_ready'=>'1','has_wifi'=>'1','existing_photos'=>[]]]];
        $this->post('/api/v1/manage/products/hotel/save',['payload'=>json_encode($payload),'thumbnail'=>$file(),'gallery'=>[$file()],'rooms'=>[['photos'=>[$file()]]]],['Accept'=>'application/json'])->assertCreated();
        $hotel=\App\Models\HotelPackage::firstOrFail();$room=$hotel->rooms()->firstOrFail();
        $this->assertSame('pending',$hotel->partner_review_status);$this->assertCount(1,$room->photos);$this->assertSame(1,$hotel->photos()->count());
        foreach([$hotel->thumbnail_path,$room->photos[0],$hotel->photos()->first()->file_path] as $path)\Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);
        $this->getJson('/api/v1/manage/products/hotel/form/'.$hotel->id)->assertOk()->assertJsonPath('data.values.rooms.0.existing_photos.0',$room->photos[0]);
        $payload['rooms'][0]['id']=$room->id;$payload['rooms'][0]['existing_photos']=$room->photos;$payload['rooms'][0]['price']=400000;
        $this->postJson('/api/v1/manage/products/hotel/save/'.$hotel->id,['payload'=>json_encode($payload)])->assertOk();
        $this->assertSame(400000.0,$hotel->rooms()->first()->price);
        $payload['rooms'][0]['existing_photos']=['another-hotel/private.png'];
        $this->postJson('/api/v1/manage/products/hotel/save/'.$hotel->id,['payload'=>json_encode($payload)])->assertUnprocessable();
        $payload['rooms'][0]['id']=999;
        $this->postJson('/api/v1/manage/products/hotel/save/'.$hotel->id,['payload'=>json_encode($payload)])->assertUnprocessable();
    }
    public function test_reviews_require_visible_products_and_keep_moderation_and_private_fields()
    {
        Schema::create('reviews',function(Blueprint $t){$t->id();$t->morphs('reviewable');foreach(['name','email','comment','status','ip_address','user_agent'] as $field)$t->text($field)->nullable();$t->integer('rating');$t->timestamps();});
        DB::table('rent_car_packages')->insert([['id'=>1,'slug'=>'public','title'=>'Mobil','is_active'=>1],['id'=>2,'slug'=>'hidden','title'=>'Hidden','is_active'=>0]]);
        $body=['name'=>'Reviewer','email'=>'review@example.test','rating'=>4,'comment'=>'Mobil bersih dan nyaman','status'=>'approved','reviewable_id'=>2];
        $this->postJson('/api/v1/catalog/rent_car/hidden/reviews',$body)->assertNotFound();
        $this->postJson('/api/v1/catalog/rent_car/public/reviews',$body)->assertCreated();
        $review=\App\Models\Review::firstOrFail();
        $this->assertSame('pending',$review->status);$this->assertSame(1,(int)$review->reviewable_id);
        $this->getJson('/api/v1/catalog/rent_car/public/reviews')->assertOk()->assertJsonCount(0,'data');
        $this->postJson('/api/v1/catalog/rent_car/public/reviews',$body)->assertUnprocessable();
        $review->update(['status'=>'approved']);
        $result=$this->getJson('/api/v1/catalog/rent_car/public/reviews')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.rating',4);
        $this->assertStringNotContainsString('review@example.test',$result->getContent());
        $this->assertArrayNotHasKey('ip_address',$result->json('data.0'));
    }
    public function test_public_content_never_exposes_unpublished_articles_or_inactive_documentation()
    {
        Schema::create('articles',function(Blueprint $t){$t->id();foreach(['title','title_en','slug','excerpt','excerpt_en','content','content_en','cover_image','ads_code','tags'] as $field)$t->text($field)->nullable();$t->boolean('is_published')->default(false);$t->timestamp('published_at')->nullable();$t->timestamps();});
        Schema::create('documentations',function(Blueprint $t){$t->id();$t->string('title');$t->string('title_en')->nullable();$t->string('category');$t->string('type');$t->string('file_path');$t->boolean('is_active');$t->integer('sort_order')->default(0);$t->timestamps();});
        \App\Models\Article::create(['title'=>'Berita perjalanan','title_en'=>'Travel news','slug'=>'news','excerpt'=>'Ringkasan','content'=>'Konten publik','is_published'=>true,'ads_code'=>'private-script']);
        \App\Models\Article::create(['title'=>'Draft','slug'=>'draft','content'=>'Belum boleh terlihat','is_published'=>false]);
        \App\Models\Documentation::create(['title'=>'Foto aktif','category'=>'tour','type'=>'photo','file_path'=>'docs/active.jpg','is_active'=>true]);
        \App\Models\Documentation::create(['title'=>'Foto tersembunyi','category'=>'tour','type'=>'photo','file_path'=>'docs/hidden.jpg','is_active'=>false]);
        $this->getJson('/api/v1/public/articles')->assertOk()->assertJsonCount(1,'data')->assertJsonMissing(['ads_code'=>'private-script']);
        $this->getJson('/api/v1/public/articles/news?locale=en')->assertOk()->assertJsonPath('data.title','Travel news')->assertJsonPath('data.content','Konten publik');
        $this->getJson('/api/v1/public/articles/draft')->assertNotFound();
        $this->getJson('/api/v1/public/documentations')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.title','Foto aktif');
        $this->getJson('/api/v1/public/pages/mail_password')->assertNotFound();
        $this->getJson('/api/v1/public/articles?locale=xx')->assertUnprocessable();
    }
    public function test_order_ownership_and_moderator_permission_are_enforced()
    {
        $owner=User::create(['name'=>'Owner','email'=>'owner@example.test','password'=>Hash::make('Password123'),'email_verified_at'=>now()]);$owner->assignRole('user');
        $other=User::create(['name'=>'Other','email'=>'other@example.test','password'=>Hash::make('Password123'),'email_verified_at'=>now()]);$other->assignRole('user');
        DB::table('orders')->insert(['user_id'=>$owner->id,'customer_email'=>$owner->email,'invoice_number'=>'INV1','product_name'=>'Trip','type'=>'tour','product_id'=>1,'created_at'=>now(),'updated_at'=>now()]);
        \Laravel\Sanctum\Sanctum::actingAs($other);
        $this->getJson('/api/v1/account/orders')->assertOk()->assertJsonCount(0,'data');
        $this->getJson('/api/v1/account/orders/1')->assertNotFound();
        $other->syncRoles('site_moderator');
        $this->getJson('/api/v1/account/orders')->assertForbidden();
        \Laravel\Sanctum\Sanctum::actingAs($owner);
        $this->getJson('/api/v1/account/orders/1')->assertOk()->assertJsonPath('data.invoice_number','INV1');
        $this->getJson('/api/v1/account/dashboard')->assertOk()->assertJsonPath('data.orders',1);
    }
    public function test_partner_sees_only_orders_for_owned_products()
    {
        $partner=User::create(['name'=>'Partner','email'=>'partner@example.test','password'=>Hash::make('Password123')]);$partner->assignRole('partner');
        DB::table('tour_packages')->insert([['id'=>1,'created_by_partner_id'=>$partner->id],['id'=>2,'created_by_partner_id'=>999]]);
        foreach([1,2] as $id) DB::table('orders')->insert(['customer_email'=>'guest@example.test','invoice_number'=>'INV'.$id,'product_name'=>'Trip','type'=>'tour','product_id'=>$id]);
        \Laravel\Sanctum\Sanctum::actingAs($partner);
        $this->getJson('/api/v1/account/orders')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.invoice_number','INV1');
        $this->getJson('/api/v1/account/orders/2')->assertNotFound();
        $this->postJson('/api/v1/account/orders/2/delete')->assertNotFound();
        Schema::create('payments',function(Blueprint $t){$t->id();$t->unsignedBigInteger('order_id');$t->timestamps();});
        DB::table('orders')->where('id',1)->update(['payment_status'=>'paid']);
        $this->postJson('/api/v1/account/orders/1/delete')->assertUnprocessable();
        DB::table('orders')->where('id',1)->update(['payment_status'=>'unpaid']);
        $this->postJson('/api/v1/account/orders/1/delete')->assertOk();
        $this->assertFalse(DB::table('orders')->where('id',1)->exists());
        $this->assertTrue(DB::table('orders')->where('id',2)->exists());
    }
    public function test_invoice_is_owner_scoped_and_omits_gateway_secrets()
    {
        Schema::create('payments',function(Blueprint $t){$t->id();$t->unsignedBigInteger('order_id');$t->string('method');$t->decimal('amount');$t->string('status');$t->string('return_token')->nullable();$t->text('gateway_payload')->nullable();$t->timestamps();});
        $owner=User::create(['name'=>'Buyer','email'=>'invoice@example.test','password'=>Hash::make('Password123'),'email_verified_at'=>now()]);$owner->assignRole('user');
        $other=User::create(['name'=>'Other','email'=>'invoice-other@example.test','password'=>Hash::make('Password123'),'email_verified_at'=>now()]);$other->assignRole('user');
        DB::table('orders')->insert(['id'=>1,'user_id'=>$owner->id,'customer_email'=>$owner->email,'invoice_number'=>'INV-PRIVATE','product_name'=>'Trip','type'=>'tour','product_id'=>1]);
        DB::table('payments')->insert(['order_id'=>1,'method'=>'gateway','amount'=>100000,'status'=>'pending','return_token'=>'secret-token','gateway_payload'=>'{"secret":"secret-value"}']);
        \Laravel\Sanctum\Sanctum::actingAs($other);
        $this->getJson('/api/v1/account/orders/1/invoice')->assertNotFound();
        $this->getJson('/api/v1/account/orders/1/contact')->assertNotFound();
        \Laravel\Sanctum\Sanctum::actingAs($owner);
        $result=$this->getJson('/api/v1/account/orders/1/invoice')->assertOk()->assertJsonPath('data.invoice_number','INV-PRIVATE')->assertJsonCount(1,'data.payments');
        $this->assertStringNotContainsString('secret-token',$result->getContent());
        $this->assertStringNotContainsString('secret-value',$result->getContent());
        $this->getJson('/api/v1/account/orders?type=hotel')->assertOk()->assertJsonCount(0,'data');
        $this->getJson('/api/v1/account/orders?payment_status=paid')->assertOk()->assertJsonCount(0,'data');
    }
    public function test_partner_registration_requires_documents_and_waits_for_approval()
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Storage::fake('public');
        Schema::create('settings',function(Blueprint $t){$t->id();$t->string('key');$t->text('value')->nullable();});
        Schema::create('partner_applications',function(Blueprint $t){
            $t->id();foreach(['name','email','phone','address','reason','identity_type','identity_file_path','legal_document_path','password_hash','password_enc','status','partner_type','bank_name','bank_account_number','bank_account_holder'] as $field)$t->text($field);
            $t->timestamp('submitted_at')->nullable();$t->timestamps();
        });
        $data=['name'=>'Partner','email'=>'apply@example.test','phone'=>'08123456789','address'=>'Alamat lengkap','reason'=>'Ingin bergabung sebagai partner','password'=>'Password123','password_confirmation'=>'Password123','partner_type'=>'agency_paket_tour','bank_name'=>'Bank','bank_account_number'=>'123456','bank_account_holder'=>'Partner','identity_type'=>'KTP'];
        $this->postJson('/api/v1/auth/register-partner',$data)->assertUnprocessable()->assertJsonValidationErrors(['identity_file','legal_document']);
        $data['identity_file']=\Illuminate\Http\UploadedFile::fake()->create('identity.pdf',10,'application/pdf');
        $data['legal_document']=\Illuminate\Http\UploadedFile::fake()->create('legal.pdf',10,'application/pdf');
        $this->post('/api/v1/auth/register-partner',$data,['Accept'=>'application/json'])->assertCreated();
        $this->assertSame(0,User::where('email','apply@example.test')->count());
        $application=\App\Models\PartnerApplication::firstOrFail();
        $this->assertSame('pending',$application->status);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($application->identity_file_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($application->legal_document_path);
    }
    public function test_mobile_product_roles_and_partner_crud_preserve_review_and_ownership()
    {
        \Illuminate\Support\Facades\Bus::fake();
        \Illuminate\Support\Facades\Storage::fake('public');
        Schema::create('rent_car_categories',function(Blueprint $t){$t->id();$t->string('name');$t->unsignedBigInteger('created_by_partner_id')->nullable();$t->timestamps();});
        $existing=Schema::getColumnListing('rent_car_packages');
        Schema::table('rent_car_packages',function(Blueprint $t)use($existing){foreach((new \App\Models\RentCarPackage)->getFillable() as $field)if(!in_array($field,$existing))$t->text($field)->nullable();});
        $partner=User::create(['name'=>'Owner','email'=>'product@example.test','password'=>Hash::make('Password123'),'partner_type'=>'agency_rental_mobil']);$partner->assignRole('partner');
        \Laravel\Sanctum\Sanctum::actingAs($partner);
        $this->getJson('/api/v1/manage/products')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.key','rent_car');
        $this->getJson('/api/v1/manage/products/tour/form')->assertForbidden();
        $this->getJson('/api/v1/manage/products/rent_car/form')->assertOk();
        $payload=['title'=>'Mobil Test','price_per_12_hours'=>150000,'price_per_24_hours'=>250000,'is_active'=>1,'created_by_partner_id'=>999,'partner_review_status'=>'approved','features'=>[['name'=>'AC','available'=>'0']]];
        $this->postJson('/api/v1/manage/products/rent_car/save',[])->assertUnprocessable();
        $this->postJson('/api/v1/manage/products/rent_car/save',$payload)->assertCreated();
        $model=\App\Models\RentCarPackage::firstOrFail();$this->assertSame($partner->id,(int)$model->created_by_partner_id);$this->assertFalse($model->is_active);$this->assertSame('pending',$model->partner_review_status);$this->assertFalse($model->features[0]['available']);
        $payload['title']='Mobil Diperbarui';
        $this->postJson('/api/v1/manage/products/rent_car/save/'.$model->id,$payload)->assertOk();
        $this->assertSame('Mobil Diperbarui',$model->fresh()->title);
        DB::table('rent_car_packages')->insert(['id'=>999,'slug'=>'other','title'=>'Other','is_active'=>0,'created_by_partner_id'=>999]);
        $this->getJson('/api/v1/manage/products/rent_car/form/999')->assertNotFound();
        $this->postJson('/api/v1/manage/products/rent_car/save/999',$payload)->assertNotFound();
        $this->postJson('/api/v1/manage/products/rent_car/999/delete')->assertNotFound();
        $this->getJson('/api/v1/manage/products/rent_car')->assertOk()->assertJsonCount(1,'data');
        $this->postJson('/api/v1/manage/products/rent_car/'.$model->id.'/delete')->assertOk();
        $this->assertNull($model->fresh());
        $partner->syncRoles('user');$this->postJson('/api/v1/manage/products/rent_car/save',$payload)->assertForbidden();
        $partner->syncRoles('site_moderator');$this->getJson('/api/v1/manage/products/rent_car')->assertForbidden();
        $partner->syncRoles('admin');$this->getJson('/api/v1/manage/products')->assertOk()->assertJsonCount(7,'data');
        $this->getJson('/api/v1/manage/products/rent_car/form/999')->assertOk();
    }
    public function test_partner_wallet_retries_do_not_double_withdraw_and_cancellation_restores_balance()
    {
        require_once database_path('migrations/2026_10_05_000001_create_mobile_mutations_table.php');(new \CreateMobileMutationsTable)->up();
        Schema::table('users',function(Blueprint $t){foreach(['partner_balance_available','partner_balance_pending','partner_balance_withdrawn','partner_tax_percent'] as $field)$t->integer($field)->default(0);foreach(['partner_bank_name','partner_bank_account_number','partner_bank_account_holder'] as $field)$t->string($field)->nullable();});
        Schema::create('partner_withdrawal_requests',function(Blueprint $t){$t->id();$t->unsignedBigInteger('partner_id');$t->integer('amount');foreach(['email','bank_name','account_number','account_holder','status','admin_note','reviewed_by','reviewed_at'] as $field)$t->string($field)->nullable();$t->softDeletes();$t->timestamps();});
        $user=User::create(['name'=>'Wallet','email'=>'wallet@example.test','password'=>Hash::make('Password123')]);$user->assignRole('partner');DB::table('users')->where('id',$user->id)->update(['partner_balance_available'=>100000]);$user->refresh();\Laravel\Sanctum\Sanctum::actingAs($user);
        $data=['request_id'=>(string)\Illuminate\Support\Str::uuid(),'amount'=>20000,'email'=>$user->email,'bank_name'=>'Bank Test','account_number'=>'123456','account_holder'=>'Wallet','password'=>'incorrect'];
        $this->postJson('/api/v1/member/wallet/action/withdraw',$data)->assertUnprocessable();
        $this->assertSame(0,DB::table('mobile_mutations')->count());
        $data['password']='Password123';$this->postJson('/api/v1/member/wallet/action/withdraw',$data)->assertOk();
        $this->postJson('/api/v1/member/wallet/action/withdraw',$data)->assertOk();
        $this->assertSame(1,DB::table('partner_withdrawal_requests')->count());$this->assertSame(80000,(int)$user->fresh()->partner_balance_available);
        $changed=$data;$changed['amount']=30000;$this->postJson('/api/v1/member/wallet/action/withdraw',$changed)->assertStatus(409);
        $this->getJson('/api/v1/member/wallet')->assertOk()->assertJsonCount(1,'data.tables.0.rows');
        $this->postJson('/api/v1/member/wallet/withdrawals/999/delete')->assertNotFound();
        $this->postJson('/api/v1/member/wallet/withdrawals/1/delete')->assertOk();$this->assertSame(100000,(int)$user->fresh()->partner_balance_available);$this->assertSame(0,(int)$user->fresh()->partner_balance_pending);
        $user->syncRoles('user');$this->getJson('/api/v1/member/wallet')->assertForbidden();
    }
    public function test_content_category_scope_and_blocked_delete_are_reported_to_android()
    {
        Schema::create('rent_car_categories',function(Blueprint $t){$t->id();$t->string('name');$t->string('slug');$t->unsignedBigInteger('created_by_partner_id')->nullable();$t->timestamps();});
        Schema::table('rent_car_packages',function(Blueprint $t){$t->unsignedBigInteger('category_id')->nullable();});
        $user=User::create(['name'=>'Category','email'=>'category@example.test','password'=>Hash::make('Password123'),'partner_type'=>'agency_rental_mobil']);$user->assignRole('partner');\Laravel\Sanctum\Sanctum::actingAs($user);
        $this->getJson('/api/v1/manage/content')->assertOk()->assertJsonCount(1,'data');
        $this->postJson('/api/v1/manage/content/articles/save',['title'=>'Forbidden'])->assertForbidden();
        $this->postJson('/api/v1/manage/content/rent_car_categories/save',['name'=>'Mobil','slug'=>'mobil','created_by_partner_id'=>999])->assertCreated();
        $this->assertSame($user->id,(int)DB::table('rent_car_categories')->value('created_by_partner_id'));
        DB::table('rent_car_packages')->insert(['title'=>'Used','slug'=>'used','is_active'=>1,'category_id'=>1]);
        $this->postJson('/api/v1/manage/content/rent_car_categories/1/delete')->assertUnprocessable();
        $this->assertSame(1,DB::table('rent_car_categories')->count());
        $this->postJson('/api/v1/manage/content/rent_car_categories/save/1',['name'=>'Mobil baru','slug'=>'mobil-baru'])->assertOk();
        $this->getJson('/api/v1/manage/content/rent_car_categories/form/1')->assertOk()->assertJsonPath('data.values.name','Mobil baru');
    }
    public function test_admin_workflows_hide_credentials_enforce_roles_and_apply_review_once()
    {
        require_once database_path('migrations/2026_10_05_000001_create_mobile_mutations_table.php');(new \CreateMobileMutationsTable)->up();
        Schema::create('reviews',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->integer('rating');$t->text('comment');$t->string('status');$t->integer('reviewable_id');$t->string('reviewable_type');$t->timestamps();});
        Schema::create('partner_applications',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->text('password_enc');$t->text('password_hash');$t->string('status');$t->timestamps();});
        DB::table('reviews')->insert(['name'=>'Reviewer','email'=>'review@example.test','rating'=>5,'comment'=>'Perjalanan menyenangkan','status'=>'pending','reviewable_id'=>1,'reviewable_type'=>\App\Models\TourPackage::class,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('partner_applications')->insert(['name'=>'Applicant','email'=>'apply@example.test','password_enc'=>'DO_NOT_EXPOSE','password_hash'=>'DO_NOT_EXPOSE','status'=>'pending']);
        $user=User::create(['name'=>'Admin','email'=>'admin-actions@example.test','password'=>Hash::make('Password123')]);$user->assignRole('user');\Laravel\Sanctum\Sanctum::actingAs($user);
        $this->getJson('/api/v1/admin/workflows')->assertForbidden();$user->syncRoles('site_moderator');$this->postJson('/api/v1/admin/workflows/reviews/1/action/approve',[])->assertForbidden();
        $user->syncRoles('admin');$this->getJson('/api/v1/admin/workflows')->assertOk()->assertJsonCount(12,'data');
        $this->getJson('/api/v1/admin/workflows/applications/1')->assertOk()->assertDontSee('DO_NOT_EXPOSE');
        $this->getJson('/api/v1/admin/workflows/reviews/1/form/approve')->assertOk();
        $payload=['request_id'=>(string)\Illuminate\Support\Str::uuid()];$this->postJson('/api/v1/admin/workflows/reviews/1/action/approve',$payload)->assertOk();$this->postJson('/api/v1/admin/workflows/reviews/1/action/approve',$payload)->assertOk();
        $this->assertSame('approved',DB::table('reviews')->value('status'));$this->assertSame(1,DB::table('mobile_mutations')->count());
        $this->postJson('/api/v1/admin/workflows/reviews/1/action/massUpdate',$payload)->assertNotFound();
    }
    public function test_site_settings_use_permission_and_keep_gateway_credentials_private()
    {
        Schema::create('settings',function(Blueprint $t){$t->id();$t->string('key');$t->text('value')->nullable();$t->timestamps();});
        Schema::create('payment_gateways',function(Blueprint $t){$t->id();$t->string('name');$t->string('label');$t->text('credentials')->nullable();$t->boolean('is_active')->default(false);$t->text('channels')->nullable();$t->timestamp('channels_synced_at')->nullable();$t->timestamps();});
        \App\Models\PaymentGateway::create(['name'=>'tripay','label'=>'TriPay','is_active'=>true,'credentials'=>['mode'=>'sandbox','api_key'=>'KEEP_SECRET','private_key'=>'KEEP_PRIVATE','merchant_code'=>'TEST']]);
        $user=User::create(['name'=>'Settings','email'=>'settings@example.test','password'=>Hash::make('Password123')]);$user->assignRole('site_moderator');\Laravel\Sanctum\Sanctum::actingAs($user);
        $this->getJson('/api/v1/admin/settings/general')->assertForbidden();$user->syncRoles('admin');
        $this->getJson('/api/v1/admin/settings/gateway_tripay')->assertOk()->assertDontSee('KEEP_SECRET')->assertDontSee('KEEP_PRIVATE');
        $this->postJson('/api/v1/admin/settings/gateway_tripay',['enable'=>'0'])->assertOk();$this->assertFalse(\App\Models\PaymentGateway::first()->is_active);
        $this->assertSame('KEEP_SECRET',\App\Models\PaymentGateway::first()->credentials['api_key']);
        $this->postJson('/api/v1/admin/settings/seo',['seo_site_title'=>'Bintang Wisata','seo_meta_description'=>'Perjalanan pilihan','seo_keywords'=>'wisata','mail_password'=>'INJECTION'])->assertOk();
        $this->assertSame('Bintang Wisata',DB::table('settings')->where('key','seo_site_title')->value('value'));$this->assertFalse(DB::table('settings')->where('key','mail_password')->exists());
    }
    public function test_application_document_download_requires_admin_permission_and_fixed_document_key()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        Schema::create('partner_applications',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->string('status');$t->string('identity_file_path');$t->string('legal_document_path')->nullable();$t->timestamps();});
        DB::table('partner_applications')->insert(['id'=>1,'name'=>'Applicant','email'=>'doc@example.test','status'=>'pending','identity_file_path'=>'identities/private.pdf']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('identities/private.pdf','%PDF-1.4 test fixture');
        $user=User::create(['name'=>'Viewer','email'=>'viewer@example.test','password'=>Hash::make('Password123')]);$user->assignRole('partner');\Laravel\Sanctum\Sanctum::actingAs($user);
        $this->get('/api/v1/admin/workflows/applications/1/documents/identity',['Accept'=>'application/json'])->assertForbidden();
        $user->syncRoles('admin');
        $this->getJson('/api/v1/admin/workflows/applications/1')->assertOk()->assertJsonPath('data.documents.0.key','identity')->assertJsonCount(1,'data.documents');
        $this->get('/api/v1/admin/workflows/applications/1/documents/identity')->assertOk()->assertHeader('X-Content-Type-Options','nosniff');
        $this->getJson('/api/v1/admin/workflows/applications/1/documents/password_enc')->assertNotFound();
        DB::table('partner_applications')->where('id',1)->update(['identity_file_path'=>'../.env']);
        $this->getJson('/api/v1/admin/workflows/applications/1/documents/identity')->assertNotFound();
    }
    public function test_notifications_are_scoped_and_migration_preserves_existing_data()
    {
        require_once database_path('migrations/2026_10_04_000001_create_notifications_if_missing.php');
        $migration=new \CreateNotificationsIfMissing();$migration->up();
        $user=User::create(['name'=>'User','email'=>'notice@example.test','password'=>Hash::make('Password123')]);$user->assignRole('user');
        $id=(string)\Illuminate\Support\Str::uuid();
        DB::table('notifications')->insert(['id'=>$id,'type'=>'test','notifiable_type'=>User::class,'notifiable_id'=>$user->id,'data'=>json_encode(['title'=>'Pengujian']),'created_at'=>now(),'updated_at'=>now()]);
        $migration->up();$this->assertSame(1,DB::table('notifications')->count());
        \Laravel\Sanctum\Sanctum::actingAs($user);
        $this->getJson('/api/v1/account/notifications')->assertOk()->assertJsonCount(1,'data');
        $this->postJson('/api/v1/account/notifications/'.$id.'/read')->assertOk();
        $this->assertNotNull(DB::table('notifications')->where('id',$id)->value('read_at'));
        $this->postJson('/api/v1/account/notifications/'.\Illuminate\Support\Str::uuid().'/read')->assertNotFound();
    }
}
