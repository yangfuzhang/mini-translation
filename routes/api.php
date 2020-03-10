<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('recognize', 'AppController@recognize');//线上用
Route::post('/upload', 'AppController@upload');
Route::post('/constellation', 'AppController@constellation');

Route::get('ffmpeg', 'AppController@ffmpeg');