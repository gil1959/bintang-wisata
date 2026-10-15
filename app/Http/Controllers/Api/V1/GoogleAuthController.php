<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Setting,User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Cache,DB,Hash,Http,Schema};
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public static function enabled(): bool
    {
        return Setting::getValue('google_auth_enabled','0')==='1'
            && config('services.google.client_id') && config('services.google.client_secret')
            && Schema::hasTable('google_identities');
    }
    public function configuration() {return response()->json(['data'=>['google_enabled'=>self::enabled()]]);}
    private function begin(string $platform,string $challenge): array
    {
        abort_unless(self::enabled(),403,'Login Google sedang tidak tersedia.');
        $state=Str::random(64);
        Cache::put('google-login:'.$state,['platform'=>$platform,'challenge'=>$challenge],now()->addMinutes(10));
        $query=http_build_query(['client_id'=>config('services.google.client_id'),'redirect_uri'=>route('google.callback'),
            'response_type'=>'code','scope'=>'openid email profile','state'=>$state,'prompt'=>'select_account']);
        return ['request_id'=>$state,'authorization_url'=>'https://accounts.google.com/o/oauth2/v2/auth?'.$query];
    }
    public function start(Request $request)
    {
        $data=$request->validate(['challenge'=>'required|string|size:64|regex:/^[a-f0-9]+$/']);
        return response()->json(['data'=>$this->begin('android',$data['challenge'])]);
    }
    public function web(Request $request)
    {
        $result=$this->begin('web','');$request->session()->put('google_login_state',$result['request_id']);
        return redirect()->away($result['authorization_url']);
    }
    private function account(array $profile): User
    {
        abort_unless(!empty($profile['sub'])&&filter_var($profile['email']??'',FILTER_VALIDATE_EMAIL)&&($profile['email_verified']??false)===true,403,'Email Google belum terverifikasi.');
        return DB::transaction(function()use($profile){
            $identity=DB::table('google_identities')->where('subject',$profile['sub'])->first();
            if($identity)$user=User::findOrFail($identity->user_id);
            else {
                $user=User::where('email',$profile['email'])->lockForUpdate()->first();
                abort_if($user&&!$user->hasVerifiedEmail(),403,'Verifikasi akun email yang sudah terdaftar sebelum menggunakan Google.');
                if(!$user){$user=User::create(['name'=>$profile['name']??'Pengguna Google','email'=>$profile['email'],'password'=>Hash::make(Str::random(64)),'email_verified_at'=>now()]);$user->assignRole('user');}
                DB::table('google_identities')->insert(['subject'=>$profile['sub'],'user_id'=>$user->id,'created_at'=>now(),'updated_at'=>now()]);
            }
            abort_if($user->is_suspended,403,'Akun sedang ditangguhkan.');
            return $user;
        });
    }
    public function callback(Request $request)
    {
        $state=(string)$request->query('state');abort_unless(preg_match('/^[A-Za-z0-9]{64}$/',$state),403);
        return Cache::lock('google-callback:'.$state,60)->block(5,function()use($request,$state){
            $pending=Cache::get('google-login:'.$state);abort_unless($pending&&!($pending['processing']??false),403,'Permintaan Google sudah berakhir.');
            if($pending['platform']==='web')abort_unless(hash_equals((string)$request->session()->pull('google_login_state'),$state),403);
            Cache::put('google-login:'.$state,$pending+['processing'=>true],now()->addMinutes(10));
            $error=null;$user=null;
            try {
                abort_unless(self::enabled()&&$request->filled('code')&&!$request->filled('error'),403);
                $tokens=Http::timeout(20)->asForm()->post('https://oauth2.googleapis.com/token',[
                    'client_id'=>config('services.google.client_id'),'client_secret'=>config('services.google.client_secret'),
                    'code'=>$request->query('code'),'grant_type'=>'authorization_code','redirect_uri'=>route('google.callback')])->throw()->json();
                $profile=Http::timeout(15)->withToken($tokens['access_token'])->get('https://openidconnect.googleapis.com/v1/userinfo')->throw()->json();
                $user=$this->account($profile);
            } catch(\Throwable $e) {$error='Login Google belum berhasil. Gunakan email dan kata sandi atau coba lagi.';}
            if($pending['platform']==='web'){
                Cache::forget('google-login:'.$state);
                if($error)return redirect()->route('login')->withErrors(['google'=>$error]);
                Auth::guard('web')->login($user);$request->session()->regenerate();
                $request->session()->forget('url.intended');
                return redirect($user->hasAnyRole(['admin','site_moderator'])?'/bw-admin':($user->hasRole('partner')?'/partner/dashboard':'/user/dashboard'));
            }
            Cache::put('google-result:'.$state,['challenge'=>$pending['challenge'],'user_id'=>$user?->id,'error'=>$error],now()->addMinutes(5));
            Cache::forget('google-login:'.$state);
            return response()->view('auth.google-result',['requestId'=>$state,'error'=>$error])->header('Cache-Control','no-store')->header('Referrer-Policy','no-referrer');
        });
    }
    public function exchange(Request $request)
    {
        $data=$request->validate(['request_id'=>'required|string|size:64|alpha_num','verifier'=>'required|string|min:43|max:128']);
        return Cache::lock('google-exchange:'.$data['request_id'],30)->block(5,function()use($data){
            $key='google-result:'.$data['request_id'];$result=Cache::get($key);
            if(!$result){abort_unless(Cache::has('google-login:'.$data['request_id']),410,'Login Google sudah berakhir.');return response()->json(['data'=>['pending'=>true]],202);}
            abort_unless(hash_equals($result['challenge'],hash('sha256',$data['verifier'])),403);
            Cache::forget($key);abort_if($result['error'],422,$result['error']);abort_unless(self::enabled(),403);
            $user=User::findOrFail($result['user_id']);abort_if($user->is_suspended,403);
            return response()->json(['data'=>['token'=>$user->createToken('android')->plainTextToken,'user'=>AuthController::account($user)]]);
        });
    }
}
