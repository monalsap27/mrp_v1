<?php

use \App\Laravue\Acl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Production\Master\{CatergoriesController, UnitController, TypeController, SupplierController};
use App\Http\Controllers\Api\Production\{ManufactureOrderController, ProductController, WorkstationController, StockController, WorkstationGroupController};

Route::group(['prefix' => '/production', 'as' => 'product'], function () {
    // Master
    Route::group(['prefix' => '/master', 'as' => '.master'], function () {
        Route::group(['prefix' => '/categories', 'as' => '.master'], function () {
            Route::get('/data', [CatergoriesController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [CatergoriesController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [CatergoriesController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/unit', 'as' => '.unit'], function () {
            Route::get('/data', [UnitController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [UnitController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [UnitController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/workstation', 'as' => '.workstation'], function () {
            Route::get('/data', [WorkstationController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [WorkstationController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [WorkstationController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/type', 'as' => '.type'], function () {
            Route::get('/data', [TypeController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [TypeController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [TypeController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
        Route::group(['prefix' => '/supplier', 'as' => '.supplier'], function () {
            Route::get('/data', [SupplierController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [SupplierController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [SupplierController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
    });
    // Product
    Route::group(['prefix' => '/product', 'as' => '.product'], function () {
        Route::get('/data', [ProductController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
        Route::get('/comboData', [ProductController::class, 'comboData'])->middleware();
        Route::post('/store', [ProductController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::post('/delete', [ProductController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::post('/show', [ProductController::class, 'show'])->middleware();
        Route::post('/showDetail', [ProductController::class, 'showDetail'])->middleware();
        Route::get('/dataApproval', [ProductController::class, 'dataApproval'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_APPROVAL_PRODUCT);
        Route::post('/approve', [ProductController::class, 'approve'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_APPROVAL_PRODUCT);
        Route::get('/dataShowBillOf', [ProductController::class, 'dataShowBillOf'])->middleware();
        Route::post('/updateSafetyStock', [ProductController::class, 'updateSafetyStock'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);

        Route::group(['prefix' => '/stock', 'as' => '.stock'], function () {
            Route::get('/data', [StockController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
            Route::post('/store', [StockController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
            Route::post('/dataIN', [StockController::class, 'dataIN'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
            Route::post('/storeOut', [StockController::class, 'storeOut'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
            Route::get('/showMutasi', [StockController::class, 'showMutasi'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
            Route::post('/showControlID', [StockController::class, 'showControlID'])->middleware();
            Route::post('/showByProduct', [StockController::class, 'showByProduct'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_PRODUCT);
        });
    });
    // Workstation
    Route::group(['prefix' => '/workstation', 'as' => '.workstation'], function () {
        Route::get('/data', [WorkstationController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('/show', [WorkstationController::class, 'show'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('/showDetail', [WorkstationController::class, 'showDetail'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::post('/store', [WorkstationController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::post('/delete', [WorkstationController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('/showByGroup', [WorkstationController::class, 'showByGroup'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

        Route::group(['prefix' => '/group', 'as' => '.group'], function () {
            Route::get('/data', [WorkstationGroupController::class, 'data'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/store', [WorkstationGroupController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/delete', [WorkstationGroupController::class, 'destroy'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::post('/dataDetail', [WorkstationGroupController::class, 'dataDetail'])->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        });
    });
    // Manufacture
    Route::group(['prefix' => '/manufacture', 'as' => '.manufactur'], function () {
        Route::post('/store', [ManufactureOrderController::class, 'store'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_MANUFACTURING);
        Route::post('/dataApproval', [ManufactureOrderController::class, 'dataApproval'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_APPROVAL_MANUFACTURING);
        Route::post('/detailListAvailable', [ManufactureOrderController::class, 'detailListAvailable'])->middleware();
        Route::post('/timelineProgress', [ManufactureOrderController::class, 'timelineProgress'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_MANUFACTURING);
        Route::post('/storeApproval', [ManufactureOrderController::class, 'storeApproval'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_APPROVAL_MANUFACTURING);
        Route::post('/dataManufactureOrder', [ManufactureOrderController::class, 'dataManufactureOrder'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_MANUFACTURING);
        Route::post('/startProduction', [ManufactureOrderController::class, 'startProduction'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_MANUFACTURING);
        Route::post('/timelineProgress', [ManufactureOrderController::class, 'timelineProgress'])->middleware('permission:' . Acl::PERMISSION_VIEW_MENU_MANUFACTURING);
        Route::post('/showDetailManufaturingOrder', [ManufactureOrderController::class, 'showDetailManufaturingOrder'])->middleware();
        Route::post('/showDetailMaterial', [ManufactureOrderController::class, 'showDetailMaterial'])->middleware();
        Route::post('/startWorkstation', [ManufactureOrderController::class, 'startWorkstation'])->middleware();
        Route::post('/pauseORFinish', [ManufactureOrderController::class, 'pauseORFinish'])->middleware();
        Route::post('/getMaterialUsed', [ManufactureOrderController::class, 'getMaterialUsed'])->middleware();
        Route::post('/storeChangeControlID', [ManufactureOrderController::class, 'storeChangeControlID'])->middleware();
        Route::post('/dataManufactureOrderCompleted', [ManufactureOrderController::class, 'dataManufactureOrderCompleted'])->middleware();
        Route::get('/dataListThisMonth', [ManufactureOrderController::class, 'dataListThisMonth'])->middleware();
        Route::get('/dataManufactureOrderSchedule', [ManufactureOrderController::class, 'dataManufactureOrderSchedule'])->middleware();
    });
});
