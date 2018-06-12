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
        $rec_type = $request->rec_type;
        $id_card_side = $request->id_card_side;

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

        switch($rec_type) {
            //通用文字识别
            case 'general':
                $api_url =  'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic';
                $results = $this->requestGeneralApi($api_url, $access_token, $path);
                break;
            //网络图片识别
            case 'webimage':
                $api_url =  'https://aip.baidubce.com/rest/2.0/ocr/v1/webimage';
                $results = $this->requestGeneralApi($api_url, $access_token, $path);
                break;
            //身份证识别
            case 'idcard':
                $api_url =  'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard';
                $results = $this->requestIdcardApi($api_url, $access_token, $path, $id_card_side);
                break;
            //银行卡识别
            case 'bankcard':
                $api_url =  'https://aip.baidubce.com/rest/2.0/ocr/v1/bankcard';
                $results = $this->requestBankcardApi($api_url, $access_token, $path);
                break;
            default:
                break;
        }
        print_r($results);

        return response()->json(['results'=>$results]);
    }

    public function requestGeneralApi($api_url,$access_token, $path) {
        $http = new Client;

        $response = $http->request('POST', $api_url, [
                        'headers' => [
                            'Content-Type'   => 'application/x-www-form-urlencoded'
                        ],
                        'form_params' => [
                            'access_token'=>$access_token,
                            'url' => $path,
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
    }

    public function requestIdcardApi($api_url, $access_token, $path, $id_card_side) {
        $http = new Client;
        //$image = file_get_contents("https://www.limepietech.com/public/images/upload/GyMH265WqptKQNNcjum0KdOTqHBddIeFPLxMV8u1.jpeg");
        $image = file_get_contents($path);
        $image = base64_encode($image);

        $response = $http->request('POST', $api_url, [
                        'headers' => [
                            'Content-Type'   => 'application/x-www-form-urlencoded'
                        ],
                        'form_params' => [
                            'access_token'=>$access_token,
                            'detect_direction' => 'true',
                            'image' => $image,
                            'id_card_side' => $id_card_side
                        ],
                        'verify' => false
                    ]);

        $res = json_decode((string) $response->getBody(), true);
        $results = $res['words_result'];

        return $results;
    }

    public function requestBankcardApi($api_url, $access_token, $path, $rec_type) {

    }

    //腾讯的文字识别接口,暂时没有使用
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
