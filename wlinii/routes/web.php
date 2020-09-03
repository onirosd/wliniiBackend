<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'LoginController@index')->name('login');
Route::post('login', 'LoginController@login');
Route::get('dashboard', 'LoginController@dashboard')->name('dashboard');
Route::get('update/{id?}/{act?}', 'LoginController@update')->name('updateUser');

// News
Route::get('news', 'NewsController@index')->name('news');
Route::get('news/detail/{id?}', 'NewsController@newsDetail')->name('newsDetail');
Route::post('news/save', 'NewsController@saveNews')->name('saveNews');
Route::get('news/delete/{id?}', 'NewsController@deleteNews')->name('deleteNews');


// Usuarios
Route::get('users', 'UsersController@index')->name('users');
Route::post('users/save', 'UsersController@saveUsers2')->name('saveUsers2');
Route::get('users/detail/{id?}', 'UsersController@usersDetail')->name('usersDetail');
// Route::post('news/save', 'UsuriosController@saveNews')->name('saveNews');