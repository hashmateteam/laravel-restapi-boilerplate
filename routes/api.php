<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('authin', 'API\UserController@authin');
Route::put('authup', 'API\UserController@authup');
Route::post('refresh_token','API\UserController@refresh_token');
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('auth', 'API\UserController@auth');
    //other api's
    Route::post('any', 'API\UserController@any');
});
