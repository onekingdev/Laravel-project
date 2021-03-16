<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'MainController@index')->name('home')->middleware('auth');
Route::get('/filedata/{id}', 'MainController@modelview')->name('filedata')->middleware('auth');
Route::get('/files/{type}/{id?}', 'FileController@index');

//didn't work
//Route::get('/modal', 'MainController@modelview')->name('modal')->middleware('auth');

Route::post('files/add', 'FileController@store');
Route::post('files/edit/{id}', 'FileController@edit');
Route::post('files/delete/{id}', 'FileController@destroy');

Auth::routes();