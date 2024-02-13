<?php

use \App\Laravue\Acl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\DepositController;
use App\Http\Controllers\Api\Customer\Master\{ProvincesController, CitiesController, DistrictController, SubDistrictController, PostalCodeController};

Route::group(['prefix' => '/customer', 'as' => 'customer'], function () {
    Route::group(['prefix' => '/master', 'as' => '.master'], function () {
        Route::group(['prefix' => '/provinces', 'as' => '.provinces'], function () {
            Route::get('/data', [ProvincesController::class, 'data'])->middleware();
            Route::post('/store', [ProvincesController::class, 'store'])->middleware();
            Route::post('/delete', [ProvincesController::class, 'destroy'])->middleware();
            // Route::get('/dataTransaction', [ProvincesController::class, 'dataTransaction'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_SALES);
        });
        Route::group(['prefix' => '/cities', 'as' => '.cities'], function () {
            Route::get('/data', [CitiesController::class, 'data'])->middleware();
            Route::post('/store', [CitiesController::class, 'store'])->middleware();
            Route::post('/delete', [CitiesController::class, 'destroy'])->middleware();
            Route::post('/showByProvince', [CitiesController::class, 'showByProvince'])->middleware();
        });
        Route::group(['prefix' => '/district', 'as' => '.district'], function () {
            Route::get('/data', [DistrictController::class, 'data'])->middleware();
            Route::post('/store', [DistrictController::class, 'store'])->middleware();
            Route::post('/delete', [DistrictController::class, 'destroy'])->middleware();
            Route::post('/showByCity', [DistrictController::class, 'showByCity'])->middleware();
        });
        Route::group(['prefix' => '/sub_district', 'as' => '.sub_district'], function () {
            Route::get('/data', [SubDistrictController::class, 'data'])->middleware();
            Route::post('/store', [SubDistrictController::class, 'store'])->middleware();
            Route::post('/delete', [SubDistrictController::class, 'destroy'])->middleware();
            Route::post('/showByDistrict', [SubDistrictController::class, 'showByDistrict'])->middleware();
        });
        Route::group(['prefix' => '/postal_code', 'as' => '.postal_code'], function () {
            Route::get('/data', [PostalCodeController::class, 'data'])->middleware();
            Route::post('/store', [PostalCodeController::class, 'store'])->middleware();
            Route::post('/delete', [PostalCodeController::class, 'destroy'])->middleware();
            Route::post('/showBySubDistrict', [PostalCodeController::class, 'showBySubDistrict'])->middleware();
        });
    });
    Route::group(['prefix' => '/customer', 'as' => '.customer'], function () {
        Route::get('/data', [CustomerController::class, 'data'])->middleware();
        Route::post('/store', [CustomerController::class, 'store'])->middleware();
        Route::post('/delete', [CustomerController::class, 'destroy'])->middleware();
        Route::post('/fetchShowCustomer', [CustomerController::class, 'fetchShowCustomer'])->middleware();
    });
    Route::group(['prefix' => '/deposit', 'as' => '.deposit'], function () {
        Route::get('/data', [DepositController::class, 'data'])->middleware();
        Route::post('/store', [DepositController::class, 'store'])->middleware();
        Route::post('/dataTransaction', [DepositController::class, 'dataTransaction'])->middleware();
        Route::post('/showByCustomer', [DepositController::class, 'showByCustomer'])->middleware();
    });
});
