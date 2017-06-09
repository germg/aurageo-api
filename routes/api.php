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

Route::group(['middleware' => 'web','prefix' => 'users'], function () {
    Route::put('/create', 'Business\UsersController@create');
    Route::delete('/delete/{id}', 'Business\UsersController@delete');
    Route::get('glogin',array('as'=>'glogin','uses'=>'Business\UsersController@googleLogin')) ;
    Route::get('google-user',array('as'=>'user.glist','uses'=>'Business\UsersController@listGoogleUser')) ;
});

Route::group(['prefix' => 'hashtags'], function () {
    Route::get('/{id}', 'Business\HashtagsController@getById');
    Route::get('/place/{id}', 'Business\HashtagsController@getByPlaceId');
    Route::put('/create', 'Business\HashtagsController@create');
    Route::post('/edit', 'Business\HashtagsController@edit');
    Route::delete('/delete/{id}', 'Business\HashtagsController@delete');
    Route::get('/', 'Business\HashtagsController@get_all');
});

Route::group(['prefix' => 'cards'], function () {
    Route::get('/{id}', 'Business\CardsController@getById');
    Route::get('/place/{id}', 'Business\CardsController@getByPlaceId');
    Route::put('/create', 'Business\CardsController@create');
    Route::post('/edit', 'Business\CardsController@edit');
    Route::delete('/delete/{id}', 'Business\CardsController@delete');
});

Route::group([/*'middleware' => 'jwt.auth', */'prefix' => 'places'], function () {
    Route::get('/latitude/{latitude}/longitude/{longitude}', 'Business\PlacesController@getPlacesNearToCoordinate');
    Route::get('/{id}', 'Business\PlacesController@getById');
    Route::get('/user/{user_id}', 'Business\PlacesController@getByUserId');
    Route::get('/bookmarked/user/{user_id}', 'Business\PlacesController@getBookmarkedByUserId');
    Route::put('/create', 'Business\PlacesController@create');
    Route::post('/edit', 'Business\PlacesController@edit');
    Route::delete('/delete/{id}', 'Business\PlacesController@delete');
});

Route::group(['prefix' => 'multimedia'], function () {
    Route::post('/upload-avatar/{id}', 'Business\MultimediaController@uploadPlaceAvatar');
    Route::post('/upload-card-image/{id}', 'Business\MultimediaController@uploadCardImage');
});

Route::group(['prefix' => 'bookmarks'], function () {
    Route::put('/create/user/{user_id}/place/{place_id}', 'Business\BookmarksController@create');
    Route::delete('/delete/user/{user_id}/place/{place_id}', 'Business\BookmarksController@delete');
});