<?php

use App\Http\Controllers\Manager\ActivityLogsController;
use App\Http\Controllers\Manager\AddDiscountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Manager\AllItemsController;
use App\Http\Controllers\Manager\CategoryController;
use App\Http\Controllers\Manager\CustomerController;
use App\Http\Controllers\Manager\SupplierController;
use App\Http\Controllers\Manager\StaffMainController;
use App\Http\Controllers\Manager\ManagerMainController;
use App\Http\Controllers\Manager\SalesReportController;
use App\Http\Controllers\Manager\SellProductController;
use App\Http\Controllers\Manager\StaffSalesController;
use App\Http\Controllers\Manager\VariantItemController;
use App\Http\Controllers\Manager\StandardItemController;
use App\Http\Controllers\Manager\SalesbyItemController;
use App\Http\Controllers\Manager\TaxController;
use App\Http\Controllers\Staff\StaffsMainController;
use App\Http\Controllers\Staff\StaffAuthController;
use App\Http\Controllers\Staff\StaffProfileController;
use App\Http\Controllers\Manager\ValuationReportController;
use App\Http\Controllers\Staff\StaffAddDiscountController;
use App\Http\Controllers\Welcome\SignupController;


/* Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'rolemanager:staff'])->name('dashboard'); */


/* Route::get('/superadmin/dashboard', function () {
    return view('superadmin');
})->middleware(['auth', 'verified', 'rolemanager:superadmin'])->name('superadmin');
 */

Route::get('/', function () {
    return view('welcome');
});

// Signup routes
Route::prefix('signup')->controller(SignupController::class)->group(function () {
    Route::get('/get_started', 'index')->name('get_started');
    Route::post('/get_started_store', 'store')->name('get_started.store');
    Route::get('/verify/{token}', 'verifyToken')->name('signup.verify');
    Route::get('/plan_pricing', 'plan_pricing')->name('plan_pricing');
    Route::post('/plan_pricing', 'selectPlan')->name('select.plan');
    Route::get('/payment', 'showPayment')->name('payment.show')->middleware('auth');
    Route::post('/payment', 'processPayment')->name('payment.process')->middleware('auth');
});



// (Removed duplicate staff routes)

//Manager routes
Route::middleware(['auth', 'verified', 'rolemanager:manager', 'check.subscription'])->group(function () {
  Route::prefix('manager')->group(function () {
    // Manager Profile routes
    Route::controller(\App\Http\Controllers\Manager\ProfileController::class)->group(function () {
        Route::get('/profile', 'show')->name('manager.profile.show');
        Route::get('/profile/edit', 'edit')->name('manager.profile.edit');
        Route::patch('/profile', 'update')->name('manager.profile.update');
        Route::get('/profile/change-password', 'changePasswordForm')->name('manager.profile.change_password');
        Route::post('/profile/change-password', 'changePassword')->name('manager.profile.change_password.post');
    });

    //managers main controller
    Route::controller(ManagerMainController::class)->group(function () {
        Route::get('/', 'index')->name('manager');
        Route::get('/add_item_standard', 'add_item_standard')->name('manager.add_item_standard');
        Route::get('/add_item_variant', 'add_item_variant')->name('manager.add_item_variant');
        Route::get('/suppliers', 'suppliers')->name('manager.suppliers');
        Route::get('/sell_product', 'sell_product')->name('manager.sell_product');
    });

     Route::controller(StaffMainController::class)->group(function () {
       Route::get('/staff_member/show', 'add_staff')->name('manager.staff');
        Route::post('/staff_member/create', 'createstaff')->name('staff.create');
        Route::get('/staff_member/edit/{id}', 'editstaff')->name('staff.edit');
        Route::put('/staff_member/update/{id}', 'updatestaff')->name('staff.update');
        // Redirect GET request for update to edit page
        Route::get('/staff_member/update/{id}', function($id) {
            return redirect()->route('staff.edit', $id);
        });
        Route::delete('/staff_member/delete/{id}', 'deletestaff')->name('staff.delete');
    });

     Route::controller(StandardItemController::class)->group(function () {
       Route::post('/standard_item/create', 'createstandard')->name('standard.create');
    });

    Route::controller(VariantItemController::class)->group(function () {
       Route::post('/variant_item/create', 'createvariant')->name('variant.create');
    });

    Route::controller(TaxController::class)->group(function () {
         Route::get('/taxes', 'taxes')->name('manager.taxes');
    });

    Route::controller(SalesReportController::class)->group(function () {
    Route::get('/completed_sales', 'completed_sales')->name('manager.completed_sales');
    Route::get('/get_sale_items/{receiptNumber}', 'get_sale_items')->name('manager.get_sale_items');
    Route::get('/print_receipt/{receiptNumber}', 'print_receipt')->name('manager.print_receipt');
    Route::get('/sales_summary', 'sales_summary')->name('manager.sales_summary');
    Route::get('/sales_by_category', 'sales_by_category')->name('manager.sales_by_category');
    Route::get('/get-staff-user-list', 'getStaffUserList')->name('manager.getStaffUserList');
  });



    Route::controller(AddDiscountController::class)->group(function () {
        Route::get('/discount_report', 'discount_report')->name('manager.discount_report');
        Route::get('/add_discount', 'add_discount')->name('manager.add_discount');
        Route::post('/discount/create', 'create_discount')->name('discount.create');
        Route::get('/get_discounts', 'get_discounts')->name('manager.get_discounts');
        Route::put('/discount/update/{id}', 'update_discount')->name('discount.update');
        Route::delete('/discount/delete/{id}', 'delete_discount')->name('discount.delete');
   });



   Route::controller(StaffSalesController::class)->group(function () {
         Route::get('/staff_sales', 'staff_sales')->name('manager.staff_sales');
   });

   Route::controller(ActivityLogsController::class)->group(function () {
         Route::get('/activity_logs', 'activity_logs')->name('manager.activity_logs');
   });

   Route::controller(SalesbyItemController::class)->group(function () {
       Route::get('/sales_by_item', 'sales_by_item')->name('manager.sales_by_item');
       Route::get('/get-categories-list', 'getCategoriesList')->name('manager.getCategoriesList');
       Route::get('/get-items-list', 'getItemsList')->name('manager.getItemsList');
   });


     Route::controller(ValuationReportController::class)->group(function () {
        Route::get('/valuation_report', 'valuation_report')->name('manager.valuation_report');
   });


    Route::controller(AllItemsController::class)->group(function () {
       Route::get('/all_items', 'all_items')->name('all_items');
        Route::delete('/all_items/delete/{type}/{id}', 'delete_item')->name('all_items.delete');
        Route::post('/all_items/delete_multiple', 'delete_multiple')->name('all_items.delete_multiple');
        Route::get('/Show_Item_Details/{type}/{id}', 'show_item_details')->name('all_items.show_item_details');
        Route::get('/all_items/edit/{type}/{id}', 'edit_item')->name('all_items.edit_item');
        Route::put('/all_items/update/{type}/{id}', 'update_item')->name('all_items.update_item');
        // Redirect GET request for update to edit page
        Route::get('/all_items/update/{type}/{id}', function($type, $id) {
            return redirect()->route('all_items.edit_item', ['type' => $type, 'id' => $id]);
        });
    });

    Route::controller(CategoryController::class)->group(function () {
      Route::get('/all_category', 'all_category')->name('all_categories');
         Route::post('/category/create', 'create_category')->name('category.create');
         Route::get('/edit_category/{id}', 'edit_category')->name('category.edit');
         Route::put('/update_category/{id}', 'update_category')->name('category.update');
         Route::delete('/delete_category/{id}', 'delete_category')->name('category.delete');
    });

    Route::controller(SupplierController::class)->group(function () {
       Route::get('/suppliers', 'suppliers')->name('manager.suppliers');
         Route::post('/supplier/create', 'create_supplier')->name('supplier.create');
         Route::get('/edit_supplier/{id}', 'edit_supplier')->name('supplier.edit');
         Route::put('/update_supplier/{id}', 'update_supplier')->name('supplier.update');
         Route::delete('/delete_supplier/{id}', 'delete_supplier')->name('supplier.delete');
    });

    Route::controller(SellProductController::class)->group(function () {
       Route::get('/sell_product', 'sell_product')->name('manager.sell_product');
       Route::post('/checkout', 'checkout')->name('manager.checkout');
       Route::post('/save_cart', 'save_cart')->name('manager.save_cart');
       Route::get('/get_saved_carts', 'get_saved_carts')->name('manager.get_saved_carts');
       Route::get('/load_saved_cart/{sessionId}', 'load_saved_cart')->name('manager.load_saved_cart');
       Route::delete('/delete_saved_cart/{sessionId}', 'delete_saved_cart')->name('manager.delete_saved_cart');
       Route::get('/View_Saved_Carts', 'view_saved_carts')->name('manager.view_saved_carts');
       Route::get('/get_all_staff', 'get_all_staff')->name('manager.get_all_staff');
    });


    Route::controller(CustomerController::class)->group(function () {
      Route::get('/get_all_customers', 'get_all_customers')->name('manager.get_all_customers');
      Route::post('/add_customer', 'add_customer')->name('manager.add_customer');
         Route::get('/customers_information', 'customers')->name('manager.customers');
         Route::get('/get_customer_details/{id}', 'get_customer_details')->name('customer.details');
         Route::get('/edit_customer/{id}', 'edit_customer')->name('customer.edit');
         Route::put('/update_customer/{id}', 'update_customer')->name('customer.update');
         Route::delete('/delete_customer/{id}', 'delete_customer')->name('customer.delete');
    });

  });
}); //End of manager router

Route::get('/businessdashboard', function () {
    return view('businessdashboard');
})->middleware(['auth', 'verified', 'rolemanager:businessmanager'])->name('businessdashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Staff Auth Routes
Route::prefix('staff')->group(function () {
    Route::get('/login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('staff.login.submit');
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');
});

// Staff dashboard and main routes (protected)
Route::middleware(['auth:staff'])->prefix('staff')->group(function () {
    Route::controller(StaffsMainController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('staff.dashboard');
        Route::get('/sell_product', 'sell_product')->name('staff.sell_product');
        Route::post('/checkout', 'checkout')->name('staff.checkout');
        Route::post('/save_cart', 'save_cart')->name('staff.save_cart');
        Route::get('/get_saved_carts', 'get_saved_carts')->name('staff.get_saved_carts');
        Route::get('/load_saved_cart/{sessionId}', 'load_saved_cart')->name('staff.load_saved_cart');
        Route::delete('/delete_saved_cart/{sessionId}', 'delete_saved_cart')->name('staff.delete_saved_cart');
        Route::get('/View_Saved_Carts', 'view_saved_carts')->name('staff.view_saved_carts');
        Route::get('/completed_sales', 'completed_sales')->name('staff.completed_sales');
        Route::get('/get_all_customers', 'get_all_customers')->name('staff.get_all_customers');
        Route::post('/add_customer', 'add_customer')->name('staff.add_customer');
        Route::get('/customers_information', 'customers')->name('staff.customers');
        Route::get('/edit_customer/{id}', 'edit_customer')->name('customer.edit');
        Route::put('/update_customer/{id}', 'update_customer')->name('customer.update');
        Route::delete('/delete_customer/{id}', 'delete_customer')->name('customer.delete');
        Route::get('/print_receipt/{receiptNumber}', 'print_receipt')->name('staff.print_receipt');
        // Add staff route for getting sale items by receipt number
        Route::get('/get_sale_items/{receiptNumber}', 'get_sale_items')->name('staff.get_sale_items');
    });


    Route::controller(StaffProfileController::class)->group(function () {
        Route::get('/profile', 'staff_profile')->name('staff.profile');
        Route::post('/update-password', 'updatePassword')->name('staff.update.password');
    });

     Route::controller(StaffAddDiscountController::class)->group(function () {
        Route::get('/get_discounts', 'get_discounts')->name('staff.get_discounts');
   });



});

require __DIR__.'/auth.php';
