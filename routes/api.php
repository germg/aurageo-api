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

Route::group(['prefix' => 'users'], function () {
    Route::put('/create', 'Business\UsersController@create');
    Route::delete('/delete', 'Business\UsersController@delete');
    Route::get('glogin',array('as'=>'glogin','uses'=>'UserController@googleLogin')) ;
    Route::get('google-user',array('as'=>'user.glist','uses'=>'UserController@listGoogleUser')) ;
});

Route::group(['prefix' => 'hashtags'], function () {
    Route::get('/{id}', 'Business\HashtagsController@getById');
    Route::get('/place/{id}', 'Business\HashtagsController@getByPlaceId');
    Route::put('/create', 'Business\HashtagsController@create');
    Route::post('/edit', 'Business\HashtagsController@edit');
    Route::delete('/delete', 'Business\HashtagsController@delete');
    Route::get('/', 'Business\HashtagsController@get_all');
});

Route::group(['prefix' => 'cards'], function () {
    Route::get('/{id}', 'Business\CardsController@getById');
    Route::get('/place/{id}', 'Business\CardsController@getByPlaceId');
    Route::put('/create', 'Business\CardsController@create');
    Route::post('/edit', 'Business\CardsController@edit');
    Route::delete('/delete/{id}', 'Business\CardsController@delete');
});

Route::group(['prefix' => 'places'], function () {
    Route::get('/latitude/{latitude}/longitude/{longitude}', 'Business\PlacesController@getPlacesNearToCoordinate');
    Route::get('/{id}', 'Business\PlacesController@getById');
    Route::get('/', 'Business\PlacesController@get_all');
    Route::get('/user/{user_id}', 'Business\PlacesController@getByUserId');
    Route::get('/bookmarked/user/{user_id}', 'Business\PlacesController@getBookmarkedByUserId');
    Route::put('/create', 'Business\PlacesController@create');
    Route::post('/edit', 'Business\PlacesController@edit');
    Route::delete('/delete', 'Business\PlacesController@delete');
});
