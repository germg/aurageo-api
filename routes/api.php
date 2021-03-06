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
    Route::post('/login', 'Business\UsersController@login');
    Route::get('/logout', 'Business\UsersController@logout');
    Route::get('/test', 'Business\UsersController@test');
});

Route::group(['prefix' => 'hashtags'], function () {
    Route::get('/{id}', 'Business\HashtagsController@getById');
    Route::get('/place/{id}', 'Business\HashtagsController@getByPlaceId');
    Route::put('/create', 'Business\HashtagsController@create')->middleware('jwt.auth');
    Route::post('/edit', 'Business\HashtagsController@edit')->middleware('jwt.auth');
    Route::delete('/delete/{id}', 'Business\HashtagsController@delete')->middleware('jwt.auth');
});

Route::group(['prefix' => 'cards'], function () {
    Route::get('/place/{id}', 'Business\CardsController@getByPlaceId');
    Route::get('/place/{id}/offset/{offset}/limit/{limit}', 'Business\CardsController@getByPlaceIdWithOffsetAndLimit');
    Route::put('/create', 'Business\CardsController@create')->middleware('jwt.auth');
    Route::post('/edit', 'Business\CardsController@edit')->middleware('jwt.auth');
    Route::delete('/delete/{id}', 'Business\CardsController@delete')->middleware('jwt.auth');
});

Route::group(['prefix' => 'places'], function () {
    Route::get('/latitude/{latitude}/longitude/{longitude}', 'Business\PlacesController@getPlacesNearToCoordinate');
    Route::get('/{id}', 'Business\PlacesController@getById');
    Route::get('/user/{user_id}', 'Business\PlacesController@getByUserId')->middleware('jwt.auth');
    Route::get('/bookmarked/user/{user_id}', 'Business\PlacesController@getBookmarkedByUserId')->middleware('jwt.auth');
    Route::put('/create', 'Business\PlacesController@create')->middleware('jwt.auth');
    Route::post('/edit', 'Business\PlacesController@edit')->middleware('jwt.auth');
    Route::delete('/delete/{id}', 'Business\PlacesController@delete')->middleware('jwt.auth');
    Route::post('/near', 'Business\PlacesController@getNearPlaces');
});

Route::group(['prefix' => 'multimedia'], function () {
    Route::post('/upload-avatar/{id}', 'Business\MultimediaController@uploadPlaceAvatar')->middleware('jwt.auth');
    Route::post('/upload-card-image/{id}', 'Business\MultimediaController@uploadCardImage')->middleware('jwt.auth');
});

Route::group(['prefix' => 'bookmarks'], function () {
    Route::put('/create/user/{user_id}/place/{place_id}', 'Business\BookmarksController@create')->middleware('jwt.auth');
    Route::delete('/delete/user/{user_id}/place/{place_id}', 'Business\BookmarksController@delete')->middleware('jwt.auth');
});