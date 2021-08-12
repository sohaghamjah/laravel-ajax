<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('upazila-list', [HomeController::class, 'upazilaList'])->name('upazila.list');

Route::group(['prefix'=>'user','as'=>'user.'],function () {
    Route::post('store', [HomeController::class, 'store'])->name('store');
    Route::post('list', [HomeController::class, 'userList'])->name('list');
    Route::post('edit', [HomeController::class, 'edit'])->name('edit');
    Route::post('show', [HomeController::class, 'show'])->name('show');
    Route::post('delete', [HomeController::class, 'delete'])->name('delete');
    Route::post('change-status', [HomeController::class, 'changeStatus'])->name('change.status');
    Route::post('bulk-action-delete', [HomeController::class, 'bulkActionDelete'])->name('bulk.action.delete');
});
