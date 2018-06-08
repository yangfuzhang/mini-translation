<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Storage;

class AppController extends Controller
{
    public function index() {
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
					        'Content-Length' => 187,
					        'Content-Type'   => 'application/json'
					    ],
					    'json' => [
					        'appid'  => $appid,
					        'bucket' => $bucket,
					        'url'    => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1528504210984&di=f84fc157716ae89bfaac67cf065ac9d3&imgtype=0&src=http%3A%2F%2Fimg4.duitang.com%2Fuploads%2Fitem%2F201303%2F30%2F20130330230514_KV4nj.thumb.700_0.jpeg'
					    ],
					    'verify' => false
					]);

    	$res = json_decode((string) $response->getBody(), true);
    	$results = $res['data']['items'];
    	$text = '';

        var_dump($res);

        foreach($results as $result) {
        	$text .= $result['itemstring'].'<br>';
        }

    	echo $text;
    }

    public function upload(Request $request) {
        $image_path = $request->file('rec_image')->store('upload');


        return response()->json(['image_path'=>$image_path]);
    }
}
