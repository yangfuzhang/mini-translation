<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Storage;

class AppController extends Controller
{
    
    public function upload(Request $request) {
        $image_path = $request->file('rec_image')->store('upload');


        return response()->json(['image_path'=>$image_path]);
    }

    public function recognize(Request $request) {
        //$rec_type = $request->rec_type;
        $path = $request->path;

        $appid = '1253144008';
        $secret_id = 'AKIDFMH0XsDwfHh7MXvB6sSq22ynSwJWlM1U';
        $secret_key = 'TmttvqaBbpqjPtlRwEKI4jDVaXHpCNMr';

        $bucket = 'limepie';
        $current = time();
        $expired = time() + 2592000;
        $rdm = rand();
        $userid = "0";

        $srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&u='.$userid.'&f=';

        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true).$srcStr);

        $http = new Client; 

        $response = $http->request('POST', 'https://recognition.image.myqcloud.com/ocr/general', [
                        'headers' => [
                            'Host'=>'recognition.image.myqcloud.com',
                            'Authorization'  => $signStr,
                            //'Content-Length' => 187, #被腾讯的文档坑惨了
                            'Content-Type'   => 'application/json'
                        ],
                        'json' => [
                            'appid'  => $appid,
                            'bucket' => $bucket,
                            'url'    => $path
                        ],
                        'verify' => false
                    ]);

        $res = json_decode((string) $response->getBody(), true);
        $results = $res['data']['items'];

        if($res['code'] != 0) {
            return response()->json(['status'=>0, 'msg'=>'图片识别失败！']);
        }

        return response()->json(['status'=>1, 'results'=>$results]);
    }
}
