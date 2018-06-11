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
        $path = $request->path;
        $client_id = 'IGpdCaDx14qf8lWfWG00FHwc';
        $client_secret = 'pyeGLISkbeQyjUotB2bmHTtw5c8kqfqp';

        $http = new Client; 

        $response = $http->request('POST', 'https://aip.baidubce.com/oauth/2.0/token', [
                        'headers' => [
                            //'Host'=>'recognition.image.myqcloud.com',
                            //'Authorization'  => $signStr,
                            //'Content-Length' => 187, #被腾讯的文档坑惨了
                            'Content-Type'   => 'application/json'
                        ],
                        'form_params' => [
                            'grant_type'  => 'client_credentials',
                            'client_id' => $client_id,
                            'client_secret'    => $client_secret
                        ],
                        'verify' => false
                    ]);

        $res = json_decode((string) $response->getBody(), true);
        $access_token = $res['access_token'];

        $results = $this->requestBdApi($access_token, $path);

        return response()->json(['status'=>1, 'results'=>$results]);
    }

    public function requestBdApi($access_token, $path) {
        $http = new Client; 

        $response = $http->request('POST', 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic', [
                        'headers' => [
                            //'Host'=>'recognition.image.myqcloud.com',
                            //'Authorization'  => $signStr,
                            //'Content-Length' => 187, #被腾讯的文档坑惨了
                            'Content-Type'   => 'application/x-www-form-urlencoded'
                        ],
                        'form_params' => [
                            'access_token'=>$access_token,
                            'url' => 'https://www.limepietech.com/public/images/upload/SUpKcCgvOzRFbldd1fvzK3KjP0ICp9zS8ekcfArj.jpeg',
                            'detect_direction' => 'true',
                            'probability' => 'true'
                        ],
                        'verify' => false
                    ]);

        $res = json_decode((string) $response->getBody(), true);
        $words_result = $res['words_result'];
        $results = [];

        foreach($words_result as $result) {
           array_push($results, $result['words']);
        }

        return $results;
        //var_dump($words_result);
        //var_dump($results);
    }

    //腾讯的文字识别接口
    public function recognizeTencent(Request $request) {
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
            return response()->json(['status'=>0, 'msg'=>'图片识别失败！', 'code'=>$res['code']]);
        }

        return response()->json(['status'=>1, 'results'=>$results]);
    }
}
