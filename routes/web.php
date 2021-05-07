<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Redirect;
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

// Route::resource('/employee', 'EmployeeController');

// Route::resource('/', 'ShoeController',
//     array('names' => array(
//         'create' => 'shoe.create',
//         'show' => 'shoe.show',
//         'index' => 'shoe.index',
//         'store' => 'shoe.store',
//         'edit' => 'shoe.edit',
//         'update' => 'shoe.update',
//         'destroy' => 'shoe.destroy',
//     ))
// );

// Route::get('/{shoe}/edit', 'ShoeController@edit')->name('shoe.edit');
// Route::get('/{shoe}', 'ShoeController@show')->name('shoe.show');
// Route::put('/{shoe}', 'ShoeController@update')->name('shoe.update');
// Route::delete('/{shoe}', 'ShoeController@destroy')->name('shoe.destroy');

Route::resource('shoe', 'ShoeController');
Route::resource('feedstock', 'FeedstockController');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', function (){
    return redirect()->route('shoe.index');
});
