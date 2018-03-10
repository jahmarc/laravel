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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'QuestionsController@index')->name('home');

Auth::routes();

Route::get('chart', 'QuestionsController@chart');

Route::get('category/{id}', 'QuestionsController@category');

Route::get('categorysend/{id}', 'QuestionsController@categorysend');

Route::resource('questions', 'QuestionsController');

Route::resource('category', 'QuestionsController@category');

Route::post('form-submit', array('before'=>'csrf',function(){
    //form validation come here
}));
