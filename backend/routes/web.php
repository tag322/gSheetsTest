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

Route::get('/google/oauth', 'App\Http\Controllers\GoogleAuthController@oAuth');

Route::post('/sheets/get/{sheet_id}', 'App\Http\Controllers\GoogleSheetsController@fetchSheet');
Route::post('/sheets/update/{sheet_id}', 'App\Http\Controllers\GoogleSheetsController@updateSheet');
Route::post('/sheets/edit/{sheet_id}', 'App\Http\Controllers\GoogleSheetsController@editRow');
Route::post('/sheets/slice/{sheet_id}', 'App\Http\Controllers\GoogleSheetsController@deleteRow');
Route::post('/sheets/erase/{sheet_id}', 'App\Http\Controllers\GoogleSheetsController@eraseTable');
Route::post('/sheets/fill/{sheet_id}', 'App\Http\Controllers\GoogleSheetsController@fillGoogleTable');

Route::post('/sheets/database/import', 'App\Http\Controllers\DBSyncController@import');
Route::post('/sheets/database/erase', 'App\Http\Controllers\DBSyncController@erase');
Route::post('/sheets/database/seed', 'App\Http\Controllers\DBSyncController@seed');


