<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Storage;

class AppController extends Controller
{
    public function ffmpeg() {
        $ffmpeg = \FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ));

        $video = $ffmpeg->open(public_path().'/images/upload/video.mpeg');

        $format = new \FFMpeg\Format\Video\X264();
        $format->on('progress', function ($video, $format, $percentage) {
            echo "$percentage % transcoded";
        });

        $format
            -> setKiloBitrate(1000)          // 视频码率
            -> setAudioChannels(2)        // 音频声道
            -> setAudioKiloBitrate(256); // 音频码率

        // 保存为
        $video->save($format, public_path().'/images/upload/video.avi');
    }

    public function upload(Request $request) {
        $image_path = $request->file('rec_image')->store('upload');

        return response()->json(['image_path'=>$image_path]);
    }

    public function recognize(Request $request) {
        $path = $request->path;
        $rec_type = $request->rec_type;

        $client_id = env("BAIDU_CLIENT_ID");
        $client_secret = env("BAIDU_CLIENT_SECRET");

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
            default:
                break;
        }
        
        if(!$results) {
            return response()->json(['error'=>1]);
        } else {
            //print_r($results);
            return response()->json(['results'=>$results]);
        }  
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

        //验证接口是否返回错误消息
        if(!isset($res['error_code'])) {
            $words_result = $res['words_result'];
            $results = [];

            foreach($words_result as $result) {
               array_push($results, $result['words']);
            }

            return $results;
        } else {
            return false;
        } 
    }

    //腾讯的文字识别接口,暂时没有使用
    public function recognizeTencent(Request $request) {
        //$rec_type = $request->rec_type;
        $path = $request->path;

        $appid = env("TECENT_APP_ID");
        $secret_id = env("TECENT_SECRET_ID");
        $secret_key = env("TECENT_SECRET_KEY");

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
