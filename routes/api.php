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
// 会員登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
// nameはrouteに名前をつけることができる
// ログアウト
Route::post('/logout', 'Auth\LoginCOntroller@logout')->name('logout');

Route::post('/photos', 'PhotoController@create')->name('photo.create');

Route::get('/photos', 'PhotoController@index')->name('photo.index');

Route::get('/photos/{id}', 'PhotoController@show')->name('photo.show');

Route::post('/photos/{photo}/comments', 'PhotoController@addComment')->name('photo.comment');

// いいね
Route::put('/photos/{id}/like', 'PhotoController@like')->name('photo.like');
// いいね解除
Route::delete('/photos/{id}/like', 'PhotoController@unlike');

// トークンリフレッシュ
Route::get('/refresh-token', function (\Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return response()->json();
});

Route::get('/user', function () {
    return Auth::user();
})->name('user');
