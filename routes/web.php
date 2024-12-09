<?php

use App\Http\Controllers\BudgetServiceController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerComplaintsController;
use App\Http\Controllers\InputUsedController;
use App\Http\Controllers\OrderServiceController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\WishServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'App\Http\Controllers\HomeController@index');
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::get('/config', 'App\Http\Controllers\ConfigController@index')->name('config');
Route::put('/config/update/{id}', 'App\Http\Controllers\ConfigController@update')->name('config.update');

Route::group(['namespace' => 'App\Http\Controllers\Profile'], function (){
	Route::get('/profile', 'ProfileController@index')->name('profile');
	Route::put('/profile/update/profile/{id}', 'ProfileController@updateProfile')->name('profile.update.profile');
	Route::put('/profile/update/password/{id}', 'ProfileController@updatePassword')->name('profile.update.password');
	Route::put('/profile/update/avatar/{id}', 'ProfileController@updateAvatar')->name('profile.update.avatar');
});

Route::group(['namespace' => 'App\Http\Controllers\Error'], function (){
	Route::get('/unauthorized', 'ErrorController@unauthorized')->name('unauthorized');
});

Route::group(['namespace' => 'App\Http\Controllers'], function (){

	Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
	//Users
	Route::get('user', 'UserController@index')->name('user');
	Route::get('user/create', 'UserController@create')->name('user.create');
	Route::post('user/store', 'UserController@store')->name('user.store');
	Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit');
	Route::put('user/update/{id}', 'UserController@update')->name('user.update');
	Route::get('user/edit/password/{id}', 'UserController@editPassword')->name('user.edit.password');
	Route::put('user/update/password/{id}', 'UserController@updatePassword')->name('user.update.password');
	Route::get('user/show/{id}', 'UserController@show')->name('user.show');
	Route::get('user/destroy/{id}', 'UserController@destroy')->name('user.destroy');
	// Roles
	Route::get('role', 'RoleController@index')->name('role');
	Route::get('role/create', 'RoleController@create')->name('role.create');
	Route::post('role/store', 'RoleController@store')->name('role.store');
	Route::get('role/edit/{id}', 'RoleController@edit')->name('role.edit');
	Route::put('role/update/{id}', 'RoleController@update')->name('role.update');
	Route::get('role/show/{id}', 'RoleController@show')->name('role.show');
	Route::get('role/destroy/{id}', 'RoleController@destroy')->name('role.destroy');


	Route::get('articulo', 'ArticuloController@index')->name('articulo');
	Route::get('articulo/create', 'ArticuloController@create')->name('articulo-create');
	Route::post('articulo', 'ArticuloController@store')->name('articulo.store');
	Route::get('articulo/{articulo}/pdf', 'ArticuloController@pdf')->name('articulo.pdf');
	Route::get('articulo/{articulo}', 'ArticuloController@show')->name('artiiculo-show');
	Route::get('articulo/{articulo}/edit', 'ArticuloController@edit')->name('articulo.edit');
	Route::put('articulo/{articulo}/update', 'ArticuloController@update')->name('articulo.update');





	Route::get('cliente', 'ClienteController@index')->name('cliente');
	Route::get('cliente/create', 'ClienteController@create')->name('cliente-create');
	Route::post('cliente', 'ClienteController@store')->name('cliente.store');
    Route::get('ajax/department', 'ClienteController@ajax_department')->name('ajax.get_deparment');


    Route::get('ajax/purchases_providers', 'ProveedorController@ajax_providers')->name('ajax.providers');
    Route::get('ajax/purchases_providers_details', 'ProveedorController@ajax_providers_purchases')->name('ajax.providers-purchases');
	Route::get('provider', 'ProveedorController@index')->name('provider');
	Route::get('provider/create', 'ProveedorController@create')->name('provider-create');
	Route::get('provider/{provider_id}/edit', 'ProveedorController@edit')->name('provider-create');
	Route::post('provider', 'ProveedorController@store')->name('provider.store');
	Route::put('provider/{provider_id}', 'ProveedorController@update')->name('provider.update');
	Route::get('provider-xls', 'ProveedorController@export_xls')->name('provider.export-xls');

	Route::get('raw-materials', 'RawMaterialsController@index')->name('raw-materials');
	Route::get('raw-materials/create', 'RawMaterialsController@create')->name('raw-materials-create');
	Route::get('raw-materials/{materiap}', 'RawMaterialsController@show')->name('raw-materials-show');
	Route::post('raw-materials', 'RawMaterialsController@store')->name('raw-materials.store');

    Route::get('brand', 'BrandController@index')->name('brand');
	Route::get('brand/create', 'BrandController@create')->name('brand-create');
	Route::post('brand', 'BrandController@store')->name('brand.store');
	Route::get('brand/{brands}/edit', 'BrandController@edit')->name('brand.edit');
	Route::put('brand/{brands}/update', 'BrandController@update')->name('brand.update');

	Route::get('production-stage', 'ProductionStageController@index')->name('production-stage');
	Route::get('production-stage/create', 'ProductionStageController@create')->name('production-stage-create');
	Route::post('production-stage', 'ProductionStageController@store')->name('production-stage.store');
	Route::get('production-stage/{stages}/edit', 'ProductionStageController@edit')->name('production-stage.edit');
	Route::put('production-stage/{stages}/update', 'ProductionStageController@update')->name('production-stage.update');

	Route::get('production-quality', 'ProductionQualityController@index')->name('production-quality');
	Route::get('production-quality/create', 'ProductionQualityController@create')->name('production-quality-create');
	Route::post('production-quality', 'ProductionQualityController@store')->name('production-quality.store');
	Route::get('production-quality/{qualitys}/edit', 'ProductionQualityController@edit')->name('production-quality.edit');
	Route::put('production-quality/{qualitys}/update', 'ProductionQualityController@update')->name('production-quality.update');

	Route::get('nationalities', 'NationalitiesController@index')->name('nationalities');
	Route::get('nationalities/create', 'NationalitiesController@create')->name('nationalities-create');
	Route::post('nationalities', 'NationalitiesController@store')->name('nationalities.store');


    Route::get('wish-purchase', 'WishPurchaseController@index')->name('wish-purchase');
	Route::get('wish-purchase/create', 'WishPurchaseController@create')->name('wish-purchase-create');
	Route::get('wish-purchase/{wish_purchase}', 'WishPurchaseController@show')->name('wish-purchase-create');
	Route::get('wish-purchase/{wish_purchase}/charge-purchase-budgets', 'WishPurchaseController@charge_purchase_budgets')->name('wish-purchases.charge-budgets');
    Route::post('wish-purchase-budgets/{wish_purchase}/charge-purchase-budgets', 'WishPurchaseController@charge_purchase_budgets_store')->name('wish-purchases.charge_purchase_budgets_store');
	Route::get('wish-purchase/{wish_purchase}/confirm-purchase-budgets', 'WishPurchaseController@confirm_purchase_budgets')->name('wish-purchases.charge-budgets');
    Route::get('wish-purchase-budgets/{purchase_budget}/confirm-purchase-budgets', 'WishPurchaseController@confirm_purchase_budgets_store')->name('wish-purchases.confirm_purchase_budgets_store');
    Route::get('wish-purchase-budgets/{wish_purchase}/wish-purchase-budgets-approved', 'WishPurchaseController@wish_purchase_budgets_approved')->name('wish-purchases.budgets_approved');
	Route::post('wish-purchase', 'WishPurchaseController@store')->name('wish-purchase.store');
	Route::get('wish-purchase/{wish_purchase}/edit', 'WishPurchaseController@edit')->name('wish-purchase.edit');
	Route::put('wish-purchase/{wish_purchase}/update', 'WishPurchaseController@update')->name('wish-purchase.update');
	Route::get('wish-purchase/{restocking}/pdf', 'WishPurchaseController@pdf')->name('wish-purchases.pdf');
	Route::get('show-multiple/wish-purchase', 'WishPurchaseController@show_multiple')->name('wish-purchases.show_multiple');
	Route::post('show-multiple/wish-purchase', 'WishPurchaseController@show_multiple_submit')->name('wish-purchases.show_multiple_submit');
	Route::get('show-multiple/transfer-create', 'WishPurchaseControllerController@transfer_create')->name('wish-purchases.transfer_create');



	Route::get('purchase-order', 'PurchaseOrderController@index')->name('purchase-order');
	Route::get('purchase-order/create', 'PurchaseOrderController@create')->name('purchase-order.create');
	Route::get('purchase-order/{purchase_order}', 'PurchaseOrderController@show')->name('purchase-order-create');
    Route::post('purchase-orders', 'PurchaseOrderController@store')->name('purchase-order.store');
    // Route::get('ajax/purchases_providers', 'ProveedorController@ajax_providers')->name('ajax.providers');
	Route::get('purchase-order/{purchase_order}/edit', 'PurchaseOrderController@edit')->name('purchase-order.edit');
	Route::put('purchase-order/{purchase_order}/update', 'PurchaseOrderController@update')->name('purchase-order.update');

	Route::get('purchase-movement', 'PurchaseMovementsController@index')->name('purchase-movement');
	Route::get('purchase-movement/create', 'PurchaseMovementsController@create')->name('purchase-movement-create');
	Route::get('purchase-movement/{purchase_movement}', 'PurchaseMovementsController@show')->name('purchase-movement-show');
	Route::post('purchase-movement', 'PurchaseMovementsController@store')->name('purchase-movement-store');
	Route::get('ajax/purchases-movements', 'PurchaseMovementsController@ajax_purchases_movements')->name('ajax.purchases-movements');
	Route::get('ajax/purchases-products-movements', 'PurchaseMovementsController@ajax_purchases_products_movements')->name('ajax.purchases-products-movements');
	Route::get('search/provider-stamped', 'WishPurchaseController@searchProviderStamped')->name('provider-stamped.search');
    Route::get('ajax/purchases_products_last', 'ArticuloController@ajax_purchases_last')->name('ajax.products-purchases-last');
	Route::get('purchase-movement/{purchase_movement}/edit', 'PurchaseMovementsController@edit')->name('purchase-movement.edit');
	Route::put('purchase-movement/{purchase_movement}/update', 'PurchaseMovementsController@update')->name('purchase-movement.update');

	Route::get('purchase', 'PurchaseController@index')->name('purchase');
	Route::get('purchase/create', 'PurchaseController@create')->name('purchase-create');
	Route::get('purchase/{purchase}', 'PurchaseController@show')->name('purchase-show');
	Route::post('purchase', 'PurchaseController@store')->name('purchase.store');
	Route::get('purchase/{purchase}/pdf', 'PurchaseController@pdf')->name('purchases.pdf');
	Route::get('purchase/{purchase}/edit', 'PurchaseController@edit')->name('purchase.edit');
	Route::put('purchase/{purchase}/update', 'PurchaseController@update')->name('purchase.update');

    Route::get('ajax/purchases_products_orders', 'RawMaterialsController@ajax_purchases_orders')->name('ajax.products-purchases-orders');
	Route::get('ajax/raw-material', 'RawMaterialsController@ajax_purchases_products')->name('ajax.purchases-products');
	Route::get('ajax/purchases/note-credits', 'PurchaseController@ajax_purchases_note_credits')->name('ajax.invoices-purchases');

	Route::get('inventories', 'PurchasesProductInventoriesController@index')->name('inventories');
    Route::get('inventories/create', 'PurchasesProductInventoriesController@create')->name('inventories.create');
    Route::post('inventories', 'PurchasesProductInventoriesController@store')->name('inventories.store');
    Route::get('inventories/{purchases_product_inventory}', 'PurchasesProductInventoriesController@show')->name('inventories.show');
    Route::get('inventories/{purchases_product_inventory}/pdf', 'PurchasesProductInventoriesController@pdf')->name('inventories.pdf');
	Route::get('inventories/{purchases_product_inventory}/confirm-inventory', 'PurchasesProductInventoriesController@confirm_inventory')->name('inventories.confirm-inventory');
	Route::get('reports/stock-product-purchases', 'ReportsController@stock_product_purchases_report')->name('reports.stock-product-purchases');
	Route::get('reports/stock-product-purchases-xls', 'ReportsController@stock_product_purchases_report_excel')->name('reports.stock-product-purchases-xls');
	Route::get('reports/purchases_report', 'ReportsController@purchases_report')->name('reports.purchases_report');
	Route::get('reports/purchases_report/pdf', 'ReportsController@purchases_report_pdf')->name('reports.purchases_report.pdf');

	Route::get('wish-service', 'WishServiceController@index')->name('wish_service');
	Route::get('wish-service/create', 'WishServiceController@create')->name('wish_service_create');
    Route::post('wish-service', 'WishServiceController@store')->name('wish_service.store');
    Route::get('ajax/clients', 'ClienteController@ajax_clients')->name('ajax.clients');
    Route::get('ajax/sites', 'WishServiceController@ajax_sites')->name('ajax.sites');
    Route::get('wish-service/change-status/{id}', [WishServiceController::class, 'changeStatus'])->name('wish_service.change_status');
    Route::get('wish-service/{id}/edit', [WishServiceController::class, 'edit'])->name('wish_service.edit');
    Route::put('wish-service/{id}', [WishServiceController::class, 'update'])->name('wish_service.update');
    Route::get('ajax/sites2', [WishServiceController::class, 'ajax_sites2'])->name('ajax.sites2');

    Route::get('budget-service', 'BudgetServiceController@index')->name('budget_service');
	Route::get('budget-service/create', 'BudgetServiceController@create')->name('budget_service_create');
    Route::post('budget-service', 'BudgetServiceController@store')->name('budget_service.store');
    Route::get('ajax/wish', 'BudgetServiceController@ajax_wish')->name('ajax.wish');
    Route::get('budget-service/{id}/edit', [BudgetServiceController::class, 'edit'])->name('budget_service.edit');
    Route::put('budget-service/{id}', [BudgetServiceController::class, 'update'])->name('budget_service.update');
    Route::get('budget-service/change-status/{id}', [BudgetServiceController::class, 'changeStatus'])->name('budget_service.change_status');


    Route::get('contracts', 'ContractController@index')->name('contract');
	Route::get('contract/create', 'ContractController@create')->name('contract-create');
    Route::post('contract', 'ContractController@store')->name('contract.store');
    Route::get('ajax/contract', 'ContractController@ajax_contract')->name('ajax.contract');
    Route::get('contract/change-status/{id}', [ContractController::class, 'changeStatus'])->name('contract.change_status');

    Route::get('order-service', 'OrderServiceController@index')->name('order_service');
    Route::get('order-service/create', 'OrderServiceController@create')->name('order_service_create');
    Route::post('order-service', 'OrderServiceController@store')->name('order_service.store');
    Route::get('ajax/order', 'OrderServiceController@ajax_order')->name('ajax.order');
    Route::get('order-service/change-status/{id}', [OrderServiceController::class, 'changeStatus'])->name('order_service.change_status');


    Route::get('input-used', 'InputUsedController@index')->name('input_used');
    Route::get('input-used/create', 'InputUsedController@create')->name('input_used_create');
    Route::post('input-used', 'InputUsedController@store')->name('input_used.store');
    Route::get('ajax/inputused', 'InputUsedController@ajax_inputused')->name('ajax.inputused');
    Route::get('input-used/change-status/{id}', [InputUsedController::class, 'changeStatus'])->name('input_used.change_status');
    Route::get('input-used/{id}/pdf', [InputUsedController::class, 'generatePDF'])->name('input_used.pdf');

    Route::get('customer-complaints', 'CustomerComplaintsController@index')->name('customer_complaints');
    Route::get('customer-complaints/create', 'CustomerComplaintsController@create')->name('customer_complaints_create');
    Route::post('customer-complaints', 'CustomerComplaintsController@store')->name('customer_complaints.store');
    Route::get('ajax/customer', 'CustomerComplaintsController@ajax_customer')->name('ajax.customer');
    Route::get('customer-complaint/change-status/{id}', [CustomerComplaintsController::class, 'changeStatus'])->name('customer_complaint.change_status');

    Route::get('promotions', [PromotionsController::class, 'index'])->name('promotions.index');
    Route::get('promotions/create', [PromotionsController::class, 'create'])->name('promotions.create');
    Route::post('promotions', [PromotionsController::class, 'store'])->name('promotions.store');
    Route::get('promotions/change-status/{id}', [PromotionsController::class, 'changeStatus'])->name('promotions.change_status');
    Route::get('promotions/{id}/edit', [PromotionsController::class, 'edit'])->name('promotions.edit');
});
