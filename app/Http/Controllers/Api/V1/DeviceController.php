<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DeviceController extends Controller
{
    public function save(Request $request)
    {
        $data=$request->validate(['token'=>'required|string|min:20|max:4096']);
        $tokenId=$request->user()->currentAccessToken()?->id;abort_unless($tokenId,403);
        DB::table('mobile_devices')->updateOrInsert(['token_hash'=>hash('sha256',$data['token'])],['user_id'=>$request->user()->id,'access_token_id'=>$tokenId,'token'=>encrypt($data['token']),'created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['message'=>'Notifikasi perangkat diaktifkan.']);
    }
    public function destroy(Request $request)
    {
        $data=$request->validate(['token'=>'required|string|max:4096']);
        DB::table('mobile_devices')->where('user_id',$request->user()->id)->where('token_hash',hash('sha256',$data['token']))->delete();
        return response()->json(['message'=>'Notifikasi perangkat dinonaktifkan.']);
    }
}
