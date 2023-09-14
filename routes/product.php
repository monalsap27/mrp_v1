<?php

use \App\Laravue\Acl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product\Master\{CatergoriesController, UnitController, WorkstationController, TypeController};


// Route::namespace('Api')->group(['middleware' => 'auth:sanctum'], function () {
Route::group(['prefix' => '/product', 'as' => 'product'], function () {
    Route::group(['prefix' => '/master', 'as' => '.master'], function () {
        Route::group(['prefix' => '/categories', 'as' => '.master'], function () {
            Route::get('/data', [CatergoriesController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [CatergoriesController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [CatergoriesController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/unit', 'as' => '.master'], function () {
            Route::get('/data', [UnitController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [UnitController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [UnitController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/workstation', 'as' => '.master'], function () {
            Route::get('/data', [WorkstationController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [WorkstationController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [WorkstationController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/type', 'as' => '.master'], function () {
            Route::get('/data', [TypeController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [TypeController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [TypeController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
    });
});
// });
