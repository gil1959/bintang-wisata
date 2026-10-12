<?php
namespace App\Services\Mobile;
use Illuminate\Support\Facades\{Cache,DB,Http};
class FirebasePush
{
    public static function dispatch(int $userId,array $payload):void
    {
        if(!config('mobile.firebase_service_account'))return;
        \App\Jobs\SendMobilePush::dispatch($userId,$payload)->afterCommit();
    }
    public function send(int $userId,array $payload):void
    {
        $path=config('mobile.firebase_service_account');if(!$path)return;
        $account=json_decode(file_get_contents($path),true,512,JSON_THROW_ON_ERROR);
        $project=$account['project_id'];
        $token=Cache::remember('mobile-firebase-auth:'.hash('sha256',$account['client_email']),3300,function()use($account){
            $encode=fn($value)=>rtrim(strtr(base64_encode(json_encode($value)),'+/','-_'),'=');
            $jwt=$encode(['alg'=>'RS256','typ'=>'JWT']).'.'.$encode(['iss'=>$account['client_email'],'scope'=>'https://www.googleapis.com/auth/firebase.messaging','aud'=>'https://oauth2.googleapis.com/token','iat'=>time(),'exp'=>time()+3600]);
            if(!openssl_sign($jwt,$signature,$account['private_key'],OPENSSL_ALGO_SHA256))throw new \RuntimeException('Firebase signing failed.');
            $jwt.='.'.rtrim(strtr(base64_encode($signature),'+/','-_'),'=');
            $response=Http::timeout(15)->asForm()->post('https://oauth2.googleapis.com/token',['grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer','assertion'=>$jwt])->throw();
            return $response->json('access_token');
        });
        $devices=DB::table('mobile_devices as d')->join('personal_access_tokens as t','t.id','=','d.access_token_id')->join('users as u','u.id','=','d.user_id')->where('d.user_id',$userId)->where('u.is_suspended',false)->where('t.tokenable_id',$userId)->select('d.*','t.token as session_hash')->get();
        foreach($devices as $device){
            $response=Http::timeout(15)->withToken($token)->post('https://fcm.googleapis.com/v1/projects/'.rawurlencode($project).'/messages:send',['message'=>['token'=>decrypt($device->token),'data'=>['title'=>(string)($payload['title']??'Bintang Wisata'),'body'=>(string)($payload['body']??''),'user_id'=>(string)$userId,'session_hash'=>$device->session_hash],'android'=>['priority'=>'high','ttl'=>'86400s']]]);
            $codes=collect($response->json('error.details',[]))->pluck('errorCode');
            if($codes->contains('UNREGISTERED'))DB::table('mobile_devices')->where('id',$device->id)->delete();
            elseif($response->failed())throw new \RuntimeException('Firebase delivery failed (HTTP '.$response->status().').');
        }
    }
}
