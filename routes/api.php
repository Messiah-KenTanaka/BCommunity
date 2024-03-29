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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// 達成済みの都道府県を取得
Route::get('/achieved-prefectures/{user_id}', 'AchievedPrefecturesController@index');

// 全国の釣り場情報を取得
Route::get('/place-maps', 'PlaceMapController@index');

// プレビューを取得
Route::post('/link-preview', 'LinkPreviewController@getLinkPreview');

// コメントを取得
Route::get('/article/{article_id}/comments', 'ArticleController@getComments');
