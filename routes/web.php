<?php

use App\Http\Controllers\Manager\AllItemsController;
use App\Http\Controllers\Manager\ManagerMainController;
use App\Http\Controllers\Manager\SalesReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Manager main controller
Route::prefix('manager')->group(function(){
    Route::controller(ManagerMainController::class)->group(function () {
        Route::get('/', 'index')->name('manager');
        Route::get('/add_staff', 'add_staff')->name('manager.add_staff');
        Route::get('/add_item_standard', 'add_item_standard')->name('manager.add_item_standard');
        Route::get('/add_item_variant', 'add_item_variant')->name('manager.add_item_variant');
        Route::get('/add_bundle', 'add_item_bundle')->name('manager.add_item_bundle');
    });

    Route::controller(SalesReportController::class)->group(function () {
        Route::get('/completed_sales', 'completed_sales')->name('manager.completed_sales');
        Route::get('/sales_summary', 'sales_summary')->name('manager.sales_summary');
        Route::get('/staff_sales', 'staff_sales')->name('manager.staff_sales');
        Route::get('/sales_by_item', 'sales_by_item')->name('manager.sales_by_item');
        Route::get('/sales_by_category', 'sales_by_category')->name('manager.sales_by_category');
        Route::get('/valuation_report', 'valuation_report')->name('manager.valuation_report');
        Route::get('/taxes', 'taxes')->name('manager.taxes');
        Route::get('/discount_report', 'discount_report')->name('manager.discount_report');
    });

    Route::controller(AllItemsController::class)->group(function () {
        Route::get('/all_items', 'all_items')->name('all_items');
    });


    
});



