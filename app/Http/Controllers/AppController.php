<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
					        'url'    => 'http://limepie-1253144008.picgz.myqcloud.com/微信截图_20180608150953.png'
					    ],
					    'verify' => false
					]);

    	$res = json_decode((string) $response->getBody(), true);
    	$results = $res['data']['items'];
    	$text = '';

        foreach($results as $result) {
        	$text .= $result['itemstring'].'<br>';
        }

    	echo $text;
    }
}
