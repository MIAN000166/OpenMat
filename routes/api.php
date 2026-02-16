<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//admin routes
Route::post('admin/login',[\App\Http\Controllers\Admin\LoginController::class,'login']);
//Admin routes end

//user routes
//d

Route::post('user/registration', [\App\Http\Controllers\User\RegistrationController::class, 'register']);
Route::post('user/verification', [\App\Http\Controllers\User\RegistrationController::class, 'verify']);
Route::post('user/login',[\App\Http\Controllers\User\LoginController::class,'login']);
Route::get('user/view/mats',[\App\Http\Controllers\User\MatController::class,'all_mats']);
Route::get('user/get/single/mat/{id}',[\App\Http\Controllers\User\MatController::class,'single_mat']);
Route::post('user/filter/mat/data',[\App\Http\Controllers\User\MatFilterController::class,'filter']);
Route::get('user/get/regions',[\App\Http\Controllers\User\MatFilterController::class,'get_regions']);

Route::get('user/get/trending/mats',[\App\Http\Controllers\User\MatController::class,'get_tending_mats']);
Route::get('user/get/single/trending/mat/{id}',[\App\Http\Controllers\User\MatController::class,'tending_mat']);
Route::post('user/news/subscription',[\App\Http\Controllers\User\SubscriptionController::class,'subscribe_news']);

//user routes end
Route::middleware('auth:api')->group( function () {
//   admin routes
    Route::post('admin/logout',[\App\Http\Controllers\Admin\LoginController::class,'logout']);
    Route::post('admin/add/user',[\App\Http\Controllers\Admin\UserController::class,'store']);
    Route::get('admin/get/all/users',[\App\Http\Controllers\Admin\UserController::class,'index']);
    Route::get('admin/get/user/{id}',[\App\Http\Controllers\Admin\UserController::class,'single_user']);
    Route::post('admin/update/user',[\App\Http\Controllers\Admin\UserController::class,'update']);
    Route::get('admin/del/user/{id}',[\App\Http\Controllers\Admin\UserController::class,'del_user']);
    Route::post('admin/change/user/status',[\App\Http\Controllers\Admin\UserController::class,'change_user_status']);
    Route::post('admin/add/mats',[\App\Http\Controllers\Admin\MatController::class,'store']);
    Route::get('admin/get/all/mats',[\App\Http\Controllers\Admin\MatController::class,'all_mats']);
    Route::get('admin/get/single/mat/{id}',[\App\Http\Controllers\Admin\MatController::class,'single_mat']);
    Route::post('admin/update/mat',[\App\Http\Controllers\Admin\MatController::class,'update']);
    Route::delete('admin/del/mat/{id}',[\App\Http\Controllers\Admin\MatController::class,'del_mat']);
    Route::post('admin/import/mat/file',[\App\Http\Controllers\Admin\MatController::class,'import']);
    Route::post('admin/add/trending/mats',[\App\Http\Controllers\Admin\MatController::class,'store_tending_mats']);
    Route::get('admin/get/single/trending/mat/{id}',[\App\Http\Controllers\Admin\MatController::class,'tending_mat']);
    Route::get('admin/get/all/trending/mats',[\App\Http\Controllers\Admin\MatController::class,'get_tending_mats']);
    Route::delete('admin/del/trending/mat/{id}',[\App\Http\Controllers\Admin\MatController::class,'del_tending_mat']);
    Route::get('admin/get/mat/count',[\App\Http\Controllers\Admin\MatController::class,'mat_count']);
    Route::get('admin/get/region/list',[\App\Http\Controllers\Admin\MatController::class,'get_region']);

//    Route::delete('admin/del/mat/collection',[\App\Http\Controllers\Admin\MatController::class,'deL_mat_collection']);

//    admin routes end
//    Route::post('user/logout',[\App\Http\Controllers\User\LoginController::class,'logout']);

});
Route::get('export',[\App\Http\Controllers\Admin\MatController::class,'export'])->name('export');
