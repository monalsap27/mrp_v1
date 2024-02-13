<?php

use \App\Laravue\Acl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Sales\{DeliveryOrderController, OrderController, PaymentTransactionController};
use App\Http\Controllers\Api\Sales\Master\{BankController, PaymentMethodController, PriceController, TenorController};

Route::group(['prefix' => '/sales', 'as' => 'sales'], function () {
    Route::group(['prefix' => '/master', 'as' => '.master'], function () {
        Route::group(['prefix' => '/payment_method', 'as' => '.payment_method'], function () {
            Route::get('/data', [PaymentMethodController::class, 'data'])->middleware();
            Route::post('/store', [PaymentMethodController::class, 'store'])->middleware();
            Route::post('/delete', [PaymentMethodController::class, 'destroy'])->middleware();
            Route::post('/ShowPaymentMethod', [PaymentMethodController::class, 'ShowPaymentMethod'])->middleware();
        });
        Route::group(['prefix' => '/bank', 'as' => '.bank'], function () {
            Route::get('/data', [BankController::class, 'data'])->middleware();
            Route::post('/store', [BankController::class, 'store'])->middleware();
            Route::post('/delete', [BankController::class, 'destroy'])->middleware();
        });
        Route::group(['prefix' => '/price', 'as' => '.price'], function () {
            Route::get('/data', [PriceController::class, 'data'])->middleware();
            Route::post('/store', [PriceController::class, 'store'])->middleware();
            Route::post('/showByProduct', [PriceController::class, 'showByProduct'])->middleware();
        });
        Route::group(['prefix' => '/tenor', 'as' => '.tenor'], function () {
            Route::get('/data', [TenorController::class, 'data'])->middleware();
            Route::post('/store', [TenorController::class, 'store'])->middleware();
            Route::post('/delete', [TenorController::class, 'destroy'])->middleware();
        });
    });
    // Orders
    Route::group(['prefix' => '/order', 'as' => '.order'], function () {
        Route::get('/data', [OrderController::class, 'data'])->middleware();
        Route::get('/fetchNoSO', [OrderController::class, 'fetchNoSO'])->middleware();
        Route::post('/store', [OrderController::class, 'store'])->middleware();
        Route::post('/show', [OrderController::class, 'show'])->middleware();
        Route::post('/showDetail', [OrderController::class, 'showDetail'])->middleware();
        Route::post('/showDetailByOrder', [OrderController::class, 'showDetailByOrder'])->middleware();
        Route::post('/delete', [OrderController::class, 'destroy'])->middleware();
        Route::get('/dataApproval', [OrderController::class, 'dataApproval'])->middleware();
        Route::post('/approve', [OrderController::class, 'approve'])->middleware();
        Route::post('/showDataDeliveryInbound', [OrderController::class, 'showDataDeliveryInbound'])->middleware();
        Route::post('/confirmInboundPO', [OrderController::class, 'confirmInboundPO'])->middleware();
        Route::get('/dataRequest', [OrderController::class, 'dataRequest'])->middleware();
        Route::post('/confirmManufacture', [OrderController::class, 'confirmManufacture'])->middleware();
        Route::post('/confirmPacked', [OrderController::class, 'confirmPacked'])->middleware();
    });
    Route::group(['prefix' => '/payment', 'as' => '.payment'], function () {
        Route::post('/store', [PaymentTransactionController::class, 'store'])->middleware();
    });
    Route::group(['prefix' => '/delivery_order', 'as' => '.delivery_order'], function () {
        Route::get('/fetchNoDO', [DeliveryOrderController::class, 'fetchNoDO'])->middleware();
        Route::post('/store', [DeliveryOrderController::class, 'store'])->middleware();
        Route::get('/data', [DeliveryOrderController::class, 'data'])->middleware();
        Route::post('/startShipment', [DeliveryOrderController::class, 'startShipment'])->middleware();
        Route::post('/fetchDeliveryByCustomer', [DeliveryOrderController::class, 'dataDeliveryByCustomer'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_VENDOR);
    });
});
