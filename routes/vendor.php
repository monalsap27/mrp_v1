<?php

use \App\Laravue\Acl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\{RequestController, DeliveryOrderController};

Route::group(['prefix' => '/vendor', 'as' => 'vendor'], function () {
    Route::group(['prefix' => '/request', 'as' => '.request'], function () {
        Route::get('/dataRequest', [RequestController::class, 'dataRequest'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showRequest', [RequestController::class, 'showRequest'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showDetail', [RequestController::class, 'showDetail'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/storeConfirm', [RequestController::class, 'storeConfirm'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showQtyVendor', [RequestController::class, 'showQtyVendor'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::get('/dataTransaction', [RequestController::class, 'dataTransaction'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
    });
    Route::group(['prefix' => '/delivery_order', 'as' => '.delivery_order'], function () {
        Route::get('/data', [DeliveryOrderController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::get('/fetchNoDO', [DeliveryOrderController::class, 'fetchNoDO'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/store', [DeliveryOrderController::class, 'storeConfirm'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showDetailTransaction', [DeliveryOrderController::class, 'showDetail'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/startShipment', [DeliveryOrderController::class, 'startShipment'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showDelivery', [DeliveryOrderController::class, 'showDelivery'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showDeliveryDetail', [DeliveryOrderController::class, 'showDeliveryDetail'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/showDeliveryByTransaction', [DeliveryOrderController::class, 'showDeliveryByTransaction'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
        Route::post('/dataDeliveryBySupplier', [DeliveryOrderController::class, 'dataDeliveryBySupplier'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
    });
});
