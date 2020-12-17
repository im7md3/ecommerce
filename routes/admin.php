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
define('PAGINATION_COUNT',10);

Route::group(['namespace' => 'Admin', 'middleware' => 'auth:admin'], function () {
    Route::get('/', 'DashboardController@index')->name('admin.dashboard');
    Route::get('logout', 'DashboardController@logout')->name('admin.logout');
    /* =========================================== language ================================= */
    Route::group(['prefix'=>'languages'],function(){
        Route::get('/','LanguageController@index')->name('admin.languages');
        Route::get('create','LanguageController@create')->name('admin.languages.create');
        Route::post('insert','LanguageController@insert')->name('admin.languages.insert');
        Route::get('edit/{id}','LanguageController@edit') -> name('admin.languages.edit');
        Route::post('update/{id}','LanguageController@update')->name('admin.languages.update');
        Route::get('delete/{id}','LanguageController@delete')->name('admin.languages.delete');
    });
    /* =========================================== Main Categories ================================= */
    Route::group(['prefix'=>'main_categories'],function(){
        Route::get('/','MainCategoriesController@index')->name('admin.maincategories');
        Route::get('create','MainCategoriesController@create')->name('admin.maincategories.create');
        Route::post('insert','MainCategoriesController@insert')->name('admin.maincategories.insert');
        Route::get('edit/{id}','MainCategoriesController@edit') -> name('admin.maincategories.edit');
        Route::post('update/{id}','MainCategoriesController@update')->name('admin.maincategories.update');
        Route::get('delete/{id}','MainCategoriesController@delete')->name('admin.maincategories.delete');
        Route::get('status/{id}','MainCategoriesController@changeStatus')->name('admin.maincategories.status');
    });

    /* =========================================== Vendors ================================= */
    Route::group(['prefix'=>'vendors'],function(){
        Route::get('/','VendorsController@index')->name('admin.vendors');
        Route::get('create','VendorsController@create')->name('admin.vendors.create');
        Route::post('insert','VendorsController@insert')->name('admin.vendors.insert');
        Route::get('edit/{id}','VendorsController@edit') -> name('admin.vendors.edit');
        Route::post('update/{id}','VendorsController@update')->name('admin.vendors.update');
        Route::get('delete/{id}','VendorsController@delete')->name('admin.vendors.delete');
        Route::get('status/{id}','VendorsController@changeStatus')->name('admin.vendors.status');
    });
});
    





Route::group(['namespace' => 'Admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login', 'LoginController@getLogin')->name('get.admin.login');
    Route::post('login', 'LoginController@login')->name('admin.login');
});

