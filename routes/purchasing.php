<?php

use \App\Laravue\Acl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Purchasing\Master\{UnitController, SettingController};
use App\Http\Controllers\Api\Purchasing\{SubmitController, OrderController};

Route::group(['prefix' => '/purchasing', 'as' => 'purchasing'], function () {
    // Master
    Route::group(['prefix' => '/master', 'as' => '.master'], function () {
        Route::group(['prefix' => '/unit', 'as' => '.unit'], function () {
            Route::get('/data', [UnitController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
            Route::post('/store', [UnitController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
            Route::post('/delete', [UnitController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        });
        Route::group(['prefix' => '/setting', 'as' => '.setting'], function () {
            Route::get('/data', [SettingController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
            Route::post('/store', [SettingController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
            Route::post('/delete', [SettingController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
            Route::post('/fetchProductSettingBySupplier', [SettingController::class, 'fetchProductSettingBySupplier'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        });
    });
    // Submit
    Route::group(['prefix' => '/submit', 'as' => '.submit'], function () {
        Route::post('/store', [SubmitController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::get('/data', [SubmitController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::post('/storeReject', [SubmitController::class, 'storeReject'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::post('/dataSubmitBySupplier', [SubmitController::class, 'dataSubmitBySupplier'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
    });
    // Orders
    Route::group(['prefix' => '/order', 'as' => '.order'], function () {
        Route::get('/data', [OrderController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::get('/fetchNoPO', [OrderController::class, 'fetchNoPO'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::post('/store', [OrderController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::post('/show', [OrderController::class, 'show'])->middleware();
        Route::post('/showDetail', [OrderController::class, 'showDetail'])->middleware();
        Route::post('/delete', [OrderController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::get('/dataApproval', [OrderController::class, 'dataApproval'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_APPROVAL_PURCHASING);
        Route::post('/approve', [OrderController::class, 'approve'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_APPROVAL_PURCHASING);
        Route::post('/showDataDeliveryInbound', [OrderController::class, 'showDataDeliveryInbound'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
        Route::post('/confirmInboundPO', [OrderController::class, 'confirmInboundPO'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PURCHASING);
    });
});
