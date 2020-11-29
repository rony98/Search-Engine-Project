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

Route::get('/', 'SearchController@index')->name('search');

Route::get('/resultsDisplay', function ($websites) {
   return View::make('results', array('websites' => $websites));
});

Route::get('/results/{query}', 'GenerateRankingController@generateRanking')->name('submitQuery');
