<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\MobileVerifyEmail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Password};
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public static function account(User $user): array
    {
        return [
            'id'=>$user->id, 'name'=>$user->name, 'email'=>$user->email,
            'phone'=>$user->phone, 'address'=>$user->address,
            'full_address'=>$user->full_address,'sub_district'=>$user->sub_district,
            'partner_bank_name'=>$user->partner_bank_name,'partner_bank_account_number'=>$user->partner_bank_account_number,'partner_bank_account_holder'=>$user->partner_bank_account_holder,
            'email_verified'=>$user->hasVerifiedEmail(),
            'roles'=>$user->getRoleNames()->values(),
            'permissions'=>$user->getAllPermissions()->pluck('name')->values(),
            'partner_type'=>$user->partner_type,
            'affiliate_status'=>$user->affiliate_status,
        ];
    }
    private function session(User $user, int $status = 200)
    {
        return response()->json(['data'=>[
            'token'=>$user->createToken('android')->plainTextToken,
            'user'=>self::account($user),
        ]], $status);
    }
    public function login(Request $request)
    {
        $data=$request->validate(['email'=>'required|email','password'=>'required|string']);
        $user=User::where('email',$data['email'])->first();
        if (!$user || !Hash::check($data['password'],$user->password)) {
            throw ValidationException::withMessages(['email'=>'Email atau kata sandi tidak sesuai.']);
        }
        abort_if($user->is_suspended,403,'Akun sedang ditangguhkan. Hubungi admin.');
        return $this->session($user);
    }
    public function register(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|string|max:255', 'email'=>'required|email|max:255|unique:users',
            'password'=>['required','confirmed',Rules\Password::defaults()],
        ]);
        $user=DB::transaction(function()use($data){
            $user=User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password'])]);
            $user->assignRole('user');
            return $user;
        });
        $user->notify(new MobileVerifyEmail());
        return $this->session($user,201);
    }
    public function me(Request $request) { return response()->json(['data'=>self::account($request->user())]); }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Berhasil keluar.']);
    }
    public function resend(Request $request)
    {
        if (!$request->user()->hasVerifiedEmail()) $request->user()->notify(new MobileVerifyEmail());
        return response()->json(['message'=>'Periksa email untuk tautan verifikasi.']);
    }
    public function verify(Request $request, $id, $hash)
    {
        $user=User::findOrFail($id);
        abort_unless(hash_equals(sha1($user->getEmailForVerification()),$hash),403);
        if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) event(new Verified($user));
        return response()->json(['message'=>'Email sudah diverifikasi. Silakan kembali ke aplikasi.']);
    }
    public function forgot(Request $request)
    {
        $data=$request->validate(['email'=>'required|email']);
        Password::sendResetLink($data);
        return response()->json(['message'=>'Jika email terdaftar, tautan reset akan dikirim.']);
    }
    public function reset(Request $request)
    {
        $data=$request->validate(['email'=>'required|email','token'=>'required|string','password'=>['required','confirmed',Rules\Password::defaults()]]);
        $status=Password::reset($data,function($user,$password){
            DB::transaction(function()use($user,$password){
                $user->forceFill(['password'=>Hash::make($password),'remember_token'=>Str::random(60)])->save();
                $user->tokens()->delete();
            });
            event(new PasswordReset($user));
        });
        if ($status!==Password::PASSWORD_RESET) throw ValidationException::withMessages(['email'=>__($status)]);
        return response()->json(['message'=>'Kata sandi berhasil diperbarui.']);
    }
}
