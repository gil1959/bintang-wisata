<?php
namespace App\Services\Mobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class IdempotentAction
{
    public function run(Request $request,string $scope,\Closure $action)
    {
        $request->validate(['request_id'=>'required|uuid']);
        $files=[];foreach(\Illuminate\Support\Arr::dot($request->allFiles()) as $key=>$file)$files[$key]=hash_file('sha256',$file->getRealPath());
        $fingerprint=hash('sha256',json_encode([$scope,$request->input(),$files]));
        return DB::transaction(function()use($request,$action,$fingerprint){
            \App\Models\User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $previous=DB::table('mobile_mutations')->where('user_id',$request->user()->id)->where('request_id',$request->request_id)->first();
            if($previous){abort_unless(hash_equals($previous->fingerprint,$fingerprint),409,'Form berubah. Kirim sebagai permintaan baru.');return response()->json(json_decode($previous->response,true));}
            $response=$action();
            DB::table('mobile_mutations')->insert(['user_id'=>$request->user()->id,'request_id'=>$request->request_id,'fingerprint'=>$fingerprint,'response'=>json_encode($response),'created_at'=>now(),'updated_at'=>now()]);
            return response()->json($response);
        });
    }
}
