<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\SalesAgentController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\TransportModeController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\StandardConsumptionController;
use App\Http\Controllers\BrandCategoryController;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\GrnEntryController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockConsumableReturnController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\JobCardEntryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TaskManagementController;
use App\Http\Controllers\DocumentRepositoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PayrollReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\FabricTypeController;
use App\Http\Controllers\PurchaseCommissionAgentController;
use App\Http\Controllers\SizeRatioController;
use App\Http\Controllers\StoreLocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\SalesMarketingReportController;
use App\Http\Controllers\WarehouseReportController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\OperationStageController;
use App\Http\Controllers\TaxTypeController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\FitController;
use App\Http\Controllers\PattiTypeController;
use App\Http\Controllers\CollarTypeController;
use App\Http\Controllers\CuffTypeController;
use App\Http\Controllers\PocketTypeController;
use App\Http\Controllers\BottomCutController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\StyleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ProductionServiceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ProductionReceiptController;
use App\Http\Controllers\ProcessGroupController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\TicketManagementController;
use App\Http\Controllers\FabricSizeController;
use App\Http\Controllers\ItemPriceController;


Route::get('/', function () {
    return view('login');
});
Route::get('/update_page', function () {
    return view('update_page');
});


Route::match(['get', 'post'], 'login', [AuthController::class, 'authentication'])->name('login');
Route::middleware(['auth.admin', 'auth.session', 'role.active', 'employee.active'])->group(function () {
    Route::match(['get', 'post'], '/dashboard', [HomeController::class, 'index']);
    Route::get('/dashboard/service-wip', [HomeController::class, 'getServiceWipDetails']);
    Route::match(['get', 'post'], 'profile', [AuthController::class, 'profile']);
    Route::match(['get', 'post'], 'logout', [AuthController::class, 'logout']);

    /* Roles */
    Route::get('/roles', [RoleController::class, 'index']);
    Route::match(['GET', 'POST'], '/roles/add/{id?}', [RoleController::class, 'add']);
    Route::post('roles/status/{id}', [RoleController::class, 'updateStatus']);
    Route::get('/roles/delete/{id}', [RoleController::class, 'destroy']);

    /* Ajax */
    Route::get('get-cities/{state_id}', [AjaxController::class, 'fetchCities']);
    Route::get('get-zones/{state_id}', [AjaxController::class, 'fetchZones']);
    Route::get('get-zones-by-city/{city_id}', [AjaxController::class, 'fetchZonesByCity']);
    Route::get('/get-places/{city_id}', [AjaxController::class, 'fetchPlaces']);
    Route::get('/raw-materials-by-category/{categoryId}', [AjaxController::class, 'getRawMaterialsByCategory']);
    Route::get('/get_charges', [AjaxController::class, 'getCharges']);
    Route::get('get-materials-by-category/{category_id}', [AjaxController::class, 'getMaterialsByCategory']);
    Route::get('ajax/search_raw_materials', [AjaxController::class, 'searchRawMaterials']);
    Route::get('get-employees-by-plant/{plantId?}/{stageId?}', [AjaxController::class, 'getEmployeesByPlant']);

    Route::get('get-service-providers-by-stage/{stageId}', [AjaxController::class, 'getServiceProvidersByStage']);
    Route::get('get-services-by-stage/{stageId}', [AjaxController::class, 'getServicesByStage']);
    Route::get('get-item-details/{id}', [AjaxController::class, 'getItemDetails']);
    Route::get('get-items-by-brand-category/{brandCategoryId}', [AjaxController::class, 'getItemsByBrandCategory']);
    Route::get('get-customer-details/{id}', [AjaxController::class, 'getCustomerDetails']);
    Route::get('get-agents-by-zone/{zone_id}', [AjaxController::class, 'fetchAgentsByZone']);
    Route::get('get-finished-item-details/{code}/{color_id}', [SalesOrderController::class, 'getFinishedItemDetails']);
    Route::get('get-finished-item-stock', [SalesOrderController::class, 'getFinishedItemStock']);

    /* Employees */
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('employees/view/{id}', [EmployeeController::class, 'view']);
    Route::get('employees/add/{id?}', [EmployeeController::class, 'add']);
    Route::post('employees/add/{id?}', [EmployeeController::class, 'add']);
    Route::post('employees/status/{id}', [EmployeeController::class, 'updateStatus']);
    Route::get('employees/delete/{id}', [EmployeeController::class, 'destroy']);

    /* Shipping Methods */
    Route::get('shipping_methods', [ShippingMethodController::class, 'index']);
    Route::match(['get', 'post'], 'shipping_methods/add/{id?}', [ShippingMethodController::class, 'add']);
    Route::get('shipping_methods/delete/{id}', [ShippingMethodController::class, 'destroy']);
    Route::post('shipping_methods/status/{id}', [ShippingMethodController::class, 'updateStatus']);

    /* Transport Modes */
    Route::get('transport_modes', [TransportModeController::class, 'index']);
    Route::match(['get', 'post'], 'transport_modes/add/{id?}', [TransportModeController::class, 'add']);
    Route::get('transport_modes/delete/{id}', [TransportModeController::class, 'destroy']);
    Route::post('transport_modes/status/{id}', [TransportModeController::class, 'updateStatus']);

    /* States */
    Route::get('/states', [StateController::class, 'index']);
    Route::match(['get', 'post'], '/states/add/{id?}', [StateController::class, 'add']);
    Route::post('states/status/{id}', [StateController::class, 'updateStatus']);
    Route::get('/states/delete/{id}', [StateController::class, 'destroy']);

    /* Cities */
    Route::get('cities', [CityController::class, 'index']);
    Route::match(['get', 'post'], 'cities/add/{id?}', [CityController::class, 'add']);
    Route::post('cities/status/{id}', [CityController::class, 'updateStatus']);
    Route::get('cities/delete/{id}', [CityController::class, 'destroy']);

    /* Places */
    Route::get('/places', [PlaceController::class, 'index']);
    Route::match(['get', 'post'], 'places/add/{id?}', [PlaceController::class, 'add']);
    Route::post('places/status/{id}', [PlaceController::class, 'updateStatus']);
    Route::get('/places/delete/{id}', [PlaceController::class, 'destroy']);

    /* UOM */
    Route::get('/uoms', [UomController::class, 'index']);
    Route::match(['get', 'post'], '/uoms/add/{id?}', [UomController::class, 'add']);
    Route::get('/uoms/delete/{id}', [UomController::class, 'destroy']);
    Route::post('/uoms/status/{id}', [UomController::class, 'updateStatus']);

    /* Colors */
    Route::get('/colors', [ColorController::class, 'index']);
    Route::match(['get', 'post'], '/colors/add/{id?}', [ColorController::class, 'add']);
    Route::get('/colors/delete/{id}', [ColorController::class, 'destroy']);
    Route::post('/colors/status/{id}', [ColorController::class, 'updateStatus']);

    /* Operation Stages */
    Route::get('/operation_stages', [OperationStageController::class, 'index']);
    Route::get('/operation_stages/add/{id?}', [OperationStageController::class, 'add']);
    Route::post('/operation_stages/add/{id?}', [OperationStageController::class, 'add']);
    Route::get('/operation_stages/delete/{id}', [OperationStageController::class, 'destroy']);
    Route::post('/operation_stages/status/{id}', [OperationStageController::class, 'updateStatus']);

    /* Charges */
    Route::get('/charges', [ChargeController::class, 'index']);
    Route::match(['GET', 'POST'], '/charges/add/{id?}', [ChargeController::class, 'add']);
    Route::get('/charges/delete/{id}', [ChargeController::class, 'destroy']);
    Route::post('/charges/status/{id}', [ChargeController::class, 'updateStatus']);

    /* Fabric type */
    Route::get('/fabric_type', [FabricTypeController::class, 'index']);
    Route::match(['GET', 'POST'], '/fabric_type/add/{id?}', [FabricTypeController::class, 'add']);
    Route::get('/fabric_type/delete/{id}', [FabricTypeController::class, 'destroy']);
    Route::post('/fabric_type/status/{id}', [FabricTypeController::class, 'updateStatus']);

    /* Fabric Size (Width) */
    Route::get('/fabric-sizes', [FabricSizeController::class, 'index']);
    Route::match(['GET', 'POST'], '/fabric-sizes/add/{id?}', [FabricSizeController::class, 'add']);
    Route::get('/fabric-sizes/delete/{id}', [FabricSizeController::class, 'destroy']);
    Route::post('/fabric-sizes/status/{id}', [FabricSizeController::class, 'updateStatus']);

    /* Purchase Commission Agents */
    Route::get('purchase_commission_agent', [PurchaseCommissionAgentController::class, 'index']);
    Route::match(['GET', 'POST'], '/purchase_commission_agent/add/{id?}', [PurchaseCommissionAgentController::class, 'add']);
    Route::get('purchase_commission_agents/view/{id}', [PurchaseCommissionAgentController::class, 'view']);
    Route::post('purchase_commission_agent/status/{id}', [PurchaseCommissionAgentController::class, 'updateStatus']);
    Route::get('purchase_commission_agent/delete/{id}', [PurchaseCommissionAgentController::class, 'destroy']);

    /* Store location */
    Route::get('/store_location', [StoreLocationController::class, 'index']);
    Route::match(['GET', 'POST'], '/store_location/add/{id?}', [StoreLocationController::class, 'add']);
    Route::get('/store_location/delete/{id}', [StoreLocationController::class, 'destroy']);
    Route::post('/store_location/status/{id}', [StoreLocationController::class, 'updateStatus']);

    /* Departments */
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::match(['GET', 'POST'], '/departments/add/{id?}', [DepartmentController::class, 'add']);
    Route::get('/department/delete/{id}', [DepartmentController::class, 'destroy']);
    Route::post('/department/status/{id}', [DepartmentController::class, 'updateStatus']);

    /* Styles */
    Route::get('/styles', [StyleController::class, 'index']);
    Route::match(['GET', 'POST'], '/styles/add/{id?}', [StyleController::class, 'add']);
    Route::get('/styles/delete/{id}', [StyleController::class, 'destroy']);
    Route::post('/styles/status/{id}', [StyleController::class, 'updateStatus']);

    /* Tax */
    Route::get('/taxes', [TaxController::class, 'index']);
    Route::match(['GET', 'POST'], '/taxes/add/{id?}', [TaxController::class, 'add']);
    Route::get('/tax/delete/{id}', [TaxController::class, 'destroy']);
    Route::post('/tax/status/{id}', [TaxController::class, 'updateStatus']);

    /* Fits */
    Route::get('/fits', [FitController::class, 'index']);
    Route::match(['GET', 'POST'], '/fits/add/{id?}', [FitController::class, 'add']);
    Route::get('/fits/delete/{id}', [FitController::class, 'destroy']);
    Route::post('/fits/status/{id}', [FitController::class, 'updateStatus']);

    /* Patti Types */
    Route::get('/patti_types', [PattiTypeController::class, 'index']);
    Route::match(['GET', 'POST'], '/patti_types/add/{id?}', [PattiTypeController::class, 'add']);
    Route::get('/patti_types/delete/{id}', [PattiTypeController::class, 'destroy']);
    Route::post('/patti_types/status/{id}', [PattiTypeController::class, 'updateStatus']);

    /* Collar Types */
    Route::get('/collar_types', [CollarTypeController::class, 'index']);
    Route::match(['GET', 'POST'], '/collar_types/add/{id?}', [CollarTypeController::class, 'add']);
    Route::get('/collar_types/delete/{id}', [CollarTypeController::class, 'destroy']);
    Route::post('/collar_types/status/{id}', [CollarTypeController::class, 'updateStatus']);

    /* Cuff Types */
    Route::get('/cuff_types', [CuffTypeController::class, 'index']);
    Route::match(['GET', 'POST'], '/cuff_types/add/{id?}', [CuffTypeController::class, 'add']);
    Route::get('/cuff_types/delete/{id}', [CuffTypeController::class, 'destroy']);
    Route::post('/cuff_types/status/{id}', [CuffTypeController::class, 'updateStatus']);

    /* Pocket Types */
    Route::get('/pocket_types', [PocketTypeController::class, 'index']);
    Route::match(['GET', 'POST'], '/pocket_types/add/{id?}', [PocketTypeController::class, 'add']);
    Route::get('/pocket_types/delete/{id}', [PocketTypeController::class, 'destroy']);
    Route::post('/pocket_types/status/{id}', [PocketTypeController::class, 'updateStatus']);

    /* Bottom Cuts */
    Route::get('/bottom_cuts', [BottomCutController::class, 'index']);
    Route::match(['GET', 'POST'], '/bottom_cuts/add/{id?}', [BottomCutController::class, 'add']);
    Route::get('/bottom_cuts/delete/{id}', [BottomCutController::class, 'destroy']);
    Route::post('/bottom_cuts/status/{id}', [BottomCutController::class, 'updateStatus']);

    /* Process Groups */
    Route::get('/process_groups', [ProcessGroupController::class, 'index']);
    Route::match(['GET', 'POST'], '/process_groups/add/{id?}', [ProcessGroupController::class, 'add']);
    Route::get('/process_groups/delete/{id}', [ProcessGroupController::class, 'destroy']);
    Route::post('/process_groups/status/{id}', [ProcessGroupController::class, 'updateStatus']);

    /* Seasons */
    Route::get('/seasons', [SeasonController::class, 'index']);
    Route::match(['GET', 'POST'], '/seasons/add/{id?}', [SeasonController::class, 'add']);
    Route::get('/seasons/delete/{id}', [SeasonController::class, 'destroy']);
    Route::post('/seasons/status/{id}', [SeasonController::class, 'updateStatus']);

    /* Brands */
    Route::get('brands', [BrandController::class, 'index']);
    Route::match(['GET', 'POST'], 'brands/add/{id?}', [BrandController::class, 'add']);
    Route::get('brands/delete/{id}', [BrandController::class, 'destroy']);
    Route::post('brands/status/{id}', [BrandController::class, 'updateStatus']);

    /* Zone */
    Route::get('/zones', [ZoneController::class, 'index']);
    Route::match(['GET', 'POST'], 'zones/add/{id?}', [ZoneController::class, 'add']);
    Route::get('/zones/delete/{id}', [ZoneController::class, 'destroy']);
    Route::post('/zones/status/{id}', [ZoneController::class, 'updateStatus']);

    /* Size & ratio */
    Route::get('size_ratio', [SizeRatioController::class, 'index']);
    Route::match(['GET', 'POST'], '/size_ratio/add/{id?}', [SizeRatioController::class, 'add']);
    Route::get('size_ratio/delete/{id}', [SizeRatioController::class, 'destroy']);
    Route::post('size_ratio/status/{id}', [SizeRatioController::class, 'updateStatus']);

    /* Customer */
    Route::get('customers', [CustomerController::class, 'index']);
    Route::match(['GET', 'POST'], 'customers/add/{id?}', [CustomerController::class, 'add']);
    Route::post('customers/status/{id}', [CustomerController::class, 'updateStatus']);
    Route::get('customers/view/{id}', [CustomerController::class, 'view']);
    Route::get('customers/delete/{id}', [CustomerController::class, 'destroy']);
    Route::post('customers/import', [CustomerController::class, 'import']);
    Route::get('customers/download-sample', [CustomerController::class, 'downloadSample']);
    Route::get('customers/export-excel', [CustomerController::class, 'exportExcel']);

    /* Customer/Suppliers */
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::match(['GET', 'POST'], '/suppliers/add/{id?}', [SupplierController::class, 'add']);
    Route::get('/supplier/delete/{id}', [SupplierController::class, 'destroy']);
    Route::post('/supplier/status/{id}', [SupplierController::class, 'updateStatus']);
    Route::get('/suppliers/view_details/{id}', [SupplierController::class, 'view']);
    Route::post('/suppliers/import', [SupplierController::class, 'import']);
    Route::get('/suppliers/download-sample', [SupplierController::class, 'downloadSample']);

    /* Service Providers */
    Route::get('service_providers', [ServiceProviderController::class, 'index']);
    Route::match(['get', 'post'], 'service_providers/add', [ServiceProviderController::class, 'add']);
    Route::post('/service_provider/status/{id}', [ServiceProviderController::class, 'updateStatus']);
    Route::match(['get', 'post'], 'service_providers/add/{id}', [ServiceProviderController::class, 'add']);
    Route::get('service_providers/view/{id}', [ServiceProviderController::class, 'view']);
    Route::get('service_provider/delete/{id}', [ServiceProviderController::class, 'destroy']);

    /* Retailers */
    Route::get('retailers', [RetailerController::class, 'index']);
    Route::match(['GET', 'POST'], 'retailers/add/{id?}', [RetailerController::class, 'add']);
    Route::get('retailers/view/{id}', [RetailerController::class, 'view']);
    Route::get('retailers/delete/{id}', [RetailerController::class, 'destroy']);
    Route::post('retailers/status/{id}', [RetailerController::class, 'updateStatus']);
    Route::get('retailers/export-excel', [RetailerController::class, 'exportExcel']);

    /* Sales Agents */
    Route::get('sales_agents', [SalesAgentController::class, 'index']);
    Route::match(['GET', 'POST'], 'sales_agents/add/{id?}', [SalesAgentController::class, 'add']);
    Route::get('sales_agents/view/{id}', [SalesAgentController::class, 'view']);
    Route::get('sales_agent/delete/{id}', [SalesAgentController::class, 'destroy']);
    Route::post('sales_agent/status/{id}', [SalesAgentController::class, 'updateStatus']);

    /* Purchase Order */
    Route::get('/purchase_orders', [PurchaseOrderController::class, 'index'])->name('purchase_orders.index');
    Route::match(['GET','POST'],'purchase_orders/add/{id?}', [PurchaseOrderController::class, 'add']);
    Route::get('/purchase_orders/view/{id}', [PurchaseOrderController::class, 'view'])->name('purchase_orders.view');
    Route::get('purchase_orders/download-pdf/{id}', [PurchaseOrderController::class, 'downloadPdf']);
    Route::get('purchase_orders/print/{id}', [PurchaseOrderController::class, 'print']);
    Route::get('/purchase_orders/delete/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase_orders.delete');
    Route::post('/purchase_orders/status/{id}', [PurchaseOrderController::class, 'updateStatus'])->name('purchase_orders.status');
    Route::post('/purchase_orders/toggle-self-close/{id}', [PurchaseOrderController::class, 'toggleSelfClose']);

    /* Purchase Invoice */
    Route::get('purchase_invoices', [PurchaseInvoiceController::class, 'index']);
    Route::match(['GET','POST'],'purchase_invoices/add/{id?}',[PurchaseInvoiceController::class, 'add']);
    Route::get('purchase_invoices/view/{id}', [PurchaseInvoiceController::class, 'view']);
    Route::get('purchase_invoices/delete/{id}', [PurchaseInvoiceController::class, 'destroy']);
    Route::post('purchase_invoices/update-status/{id}', [PurchaseInvoiceController::class, 'updateStatus']);
    Route::get('purchase_invoices/get-po-details/{id}', [PurchaseInvoiceController::class, 'getPurchaseOrderDetails']);
    Route::get('purchase_invoices/get-items/{id}', [PurchaseInvoiceController::class, 'getInvoiceItems']);
    Route::get('purchase_invoices/download-pdf/{id}', [PurchaseInvoiceController::class, 'downloadPdf']);
    Route::get('purchase_invoices/print/{id}', [PurchaseInvoiceController::class, 'print']);
    Route::delete('purchase_invoices/delete-charge/{id}', [PurchaseInvoiceController::class, 'deleteCharge']);

    /* Raw Material Category */
    Route::get('store_categories', [StoreCategoryController::class, 'index']);
    Route::match(['GET', 'POST'], 'store_categories/add/{id?}', [StoreCategoryController::class, 'add']);
    Route::get('store_categories/delete/{id}', [StoreCategoryController::class, 'destroy']);
    Route::post('store_categories/status/{id}', [StoreCategoryController::class, 'updateStatus']);

    /* Raw Material  */
    Route::get('raw_materials', [RawMaterialController::class, 'index']);
    Route::match(['GET', 'POST'], 'raw_materials/add/{id?}', [RawMaterialController::class, 'add']);
    Route::get('raw_materials/delete/{id}', [RawMaterialController::class, 'destroy']);
    Route::post('raw_materials/status/{id}', [RawMaterialController::class, 'updateStatus']);

    /* Standard Consumption */
    Route::get('standard_consumptions', [StandardConsumptionController::class, 'index']);
    Route::match(['GET', 'POST'], 'standard_consumptions/add/{id?}', [StandardConsumptionController::class, 'add']);
    Route::get('standard_consumptions/delete/{id}', [StandardConsumptionController::class, 'destroy']);
    Route::post('standard_consumptions/status/{id}', [StandardConsumptionController::class, 'updateStatus']);

    /* Brand Category */
    Route::get('brand_categories', [BrandCategoryController::class, 'index']);
    Route::match(['GET', 'POST'], 'brand_categories/add/{id?}', [BrandCategoryController::class, 'add']);
    Route::get('brand_categories/delete/{id}', [BrandCategoryController::class, 'destroy']);
    Route::post('brand_categories/status/{id}', [BrandCategoryController::class, 'updateStatus']);

    /* Item */
    Route::get('items', [ItemController::class, 'index']);
    Route::match(['get', 'post'], 'items/add/{id?}', [ItemController::class, 'add']);
    Route::get('items/view/{id}', [ItemController::class, 'view']);
    Route::get('items/delete/{id}', [ItemController::class, 'destroy']);
    Route::post('items/status/{id}', [ItemController::class, 'updateStatus']);
    Route::get('items/filter', [ItemController::class, 'filter']);

    /* Item Price */
    Route::get('item_prices', [ItemPriceController::class, 'index']);

    Route::match(['get', 'post'], 'item_prices/add/{id?}', [ItemPriceController::class, 'add']);
    Route::get('item_prices/delete/{id}', [ItemPriceController::class, 'destroy']);
    Route::post('item_prices/status/{id}', [ItemPriceController::class, 'updateStatus']);
    Route::get('item_prices/export-excel', [ItemPriceController::class, 'exportExcel']);
    Route::get('item_prices/get_art_nos', [ItemPriceController::class, 'getArtNos']);
    Route::get('item_prices/search_items', [ItemPriceController::class, 'searchItems']);

    /* Grn Entry */
    Route::get('grn_entries', [GrnEntryController::class, 'index']);
    Route::match(['get', 'post'], 'grn_entries/add/{id?}', [GrnEntryController::class, 'add']);
    Route::delete('grn_entries/delete/{id}', [GrnEntryController::class, 'destroy']);
    Route::post('grn_entries/status/{id}', [GrnEntryController::class, 'updateStatus']);
    Route::get('grn_entries/get-invoice-details/{id}', [GrnEntryController::class, 'getInvoiceDetails']);
    Route::get('grn_entries/view/{id}', [GrnEntryController::class, 'view']);
    Route::get('grn_entries/print/{id}', [GrnEntryController::class, 'print']);
    Route::get('grn_entries/download-pdf/{id}', [GrnEntryController::class, 'downloadPdf']);

    /* Stock Entry */
    Route::get('stock_entries/get-grn-items/{id}', [StockEntryController::class, 'getGrnEntryItems']);
    Route::get('stock_entries/get-items/{id}', [StockEntryController::class, 'getEntryItems']);
    Route::get('stock_entries', [StockEntryController::class, 'index']);
    Route::match(['get', 'post'], 'stock_entries/add/{id?}', [StockEntryController::class, 'add']);
    Route::post('stock_entries/quick-adjustment', [StockEntryController::class, 'quickAdjustment'])->name('stock_entries.quick_adjustment');
    Route::get('stock_entries/adjustment-logs/{id?}', [StockEntryController::class, 'adjustmentLogs'])->name('stock_entries.adjustment_logs');
    Route::get('stock_entries/view/{id}/{entry_type?}', [StockEntryController::class, 'view']);
    Route::post('stock_entries/import-raw-materials', [StockEntryController::class, 'importRawMaterials']);
    Route::get('stock_entries/download-sample', [StockEntryController::class, 'downloadSample']);
    Route::get('labels/print/{id}', [\App\Http\Controllers\LabelController::class, 'print']);

    /* Store */
    Route::get('stock_consumables_returns', [StockConsumableReturnController::class, 'index']);
    Route::get('add_stock_consumables_return', [StockConsumableReturnController::class, 'add']);
    Route::get('stock_consumables_returns/view/{id}', [StockConsumableReturnController::class, 'view']);

    /* Sales Order */
    Route::get('sales_orders', [SalesOrderController::class, 'index']);
    Route::match(['GET', 'POST'], 'sales_orders/add/{id?}', [SalesOrderController::class, 'add']);
    Route::get('sales_orders/view/{id}', [SalesOrderController::class, 'view']);
    Route::get('sales_orders/download-pdf/{id}', [SalesOrderController::class, 'downloadPdf']);
    Route::get('sales_orders/print/{id}', [SalesOrderController::class, 'print']);
    Route::get('sales_orders/delete/{id}', [SalesOrderController::class, 'destroy']);
    Route::post('sales_orders/status/{id}', [SalesOrderController::class, 'updateStatus']);
    Route::get('sales_orders/search-stock-items', [SalesOrderController::class, 'searchStockItems']);
    Route::get('sales_orders/sync-orderaxe', [SalesOrderController::class, 'syncOrderaxe']);
    Route::get('stock_entries/export-finished-goods', [StockEntryController::class, 'exportFinishedGoods']);
    Route::get('stock_entries/export-barcode', [StockEntryController::class, 'exportBarcode']);
    Route::get('stock_entries/export-raw-materials', [StockEntryController::class, 'exportRawMaterials']);
    Route::delete('sales_orders/delete-charge/{id}', [SalesOrderController::class, 'deleteCharge']);

    /* Sales Invoice */
    Route::get('sales_invoices', [SalesInvoiceController::class, 'index']);
    Route::match(['GET', 'POST'], 'sales_invoices/add/{id?}', [SalesInvoiceController::class, 'add']);
    Route::get('sales_invoices/view/{id}', [SalesInvoiceController::class, 'view']);
    Route::get('sales_invoices/download-pdf/{id}', [SalesInvoiceController::class, 'downloadPdf']);
    Route::get('sales_invoices/print/{id}', [SalesInvoiceController::class, 'print']);
    Route::get('sales_invoices/download/{id}', [SalesInvoiceController::class, 'downloadPdf']);
    Route::post('sales_invoices/status/{id}', [SalesInvoiceController::class, 'updateStatus']);
    Route::get('sales_invoices/get-sale-order-details/{id}', [SalesInvoiceController::class, 'getSaleOrderDetails']);
    Route::get('sales_invoices/calculate-distance', [SalesInvoiceController::class, 'calculateDistance']);
    Route::get('sales_invoices/print-sticker/{id}', [SalesInvoiceController::class, 'printSticker']);
    Route::post('sales_invoices/generate-einvoice/{id}', [SalesInvoiceController::class, 'generateEInvoice']);
    Route::post('sales_invoices/generate-ewaybill/{id}', [SalesInvoiceController::class, 'generateEWayBill']);
    Route::post('sales_invoices/cancel-einvoice/{id}', [SalesInvoiceController::class, 'cancelEInvoice']);
    Route::post('sales_invoices/cancel-ewaybill/{id}', [SalesInvoiceController::class, 'cancelEWayBill']);
    Route::get('sales_invoices/recreate/{id}', [SalesInvoiceController::class, 'recreate']);

    /* Credit Notes */
    Route::get('credit_notes', [CreditNoteController::class, 'index']);
    Route::match(['GET', 'POST'], 'credit_notes/add/{id?}', [CreditNoteController::class, 'add']);
    Route::get('credit_notes/view/{id}', [CreditNoteController::class, 'view']);
    Route::get('credit_notes/delete/{id}', [CreditNoteController::class, 'destroy']);
    Route::get('credit_notes/get-invoice-details/{id}', [CreditNoteController::class, 'getInvoiceDetails']);
    Route::post('credit_notes/status/{id}', [CreditNoteController::class, 'updateStatus']);
    Route::get('credit_notes/print/{id}', [CreditNoteController::class, 'print']);
    Route::get('credit_notes/download/{id}', [CreditNoteController::class, 'download']);

    /* Debit Notes */
    Route::get('debit_notes', [DebitNoteController::class, 'index']);
    Route::match(['GET', 'POST'], 'debit_notes/add/{id?}', [DebitNoteController::class, 'add']);
    Route::get('debit_notes/view/{id}', [DebitNoteController::class, 'view']);
    Route::get('debit_notes/delete/{id}', [DebitNoteController::class, 'destroy']);
    Route::get('debit_notes/get-invoice-details/{id}', [DebitNoteController::class, 'getInvoiceDetails']);
    Route::get('debit_notes/get-supplier-invoices/{id}', [DebitNoteController::class, 'getSupplierInvoices']);
    Route::get('debit_notes/print/{id}', [DebitNoteController::class, 'print']);
    Route::get('debit_notes/download/{id}', [DebitNoteController::class, 'download']);
    Route::post('debit_notes/status/{id}', [DebitNoteController::class, 'updateStatus']);

    /* Job Card Entry */
    Route::get('job_card_entries', [JobCardEntryController::class, 'index']);
    Route::match(['GET', 'POST'], 'job_card_entries/add/{id?}', [JobCardEntryController::class, 'add']);
    Route::get('job_card_entries/view/{id}', [JobCardEntryController::class, 'view_details']);
    Route::post('job_card_entries/delete/{id}', [JobCardEntryController::class, 'destroy']);
    Route::get('job_card_entries/get-size-ratio/{id}', [JobCardEntryController::class, 'getSizeRatioDetails']);
    Route::get('job_card_entries/get-po-details/{id}', [JobCardEntryController::class, 'getPoDetails']);
    Route::get('job_card_entries/check-stock/{id}', [JobCardEntryController::class, 'checkStock']);
    Route::get('job_card_entries/search-stock-entries', [JobCardEntryController::class, 'searchStockEntries']);
    Route::get('job_card_entries/get-stock-entry-details', [JobCardEntryController::class, 'getStockEntryDetails']);
    Route::get('job_card_entries/get_items_by_store_category', [JobCardEntryController::class, 'getItemsByStoreCategory']);
    Route::get('job_card_entries/get_items_by_brand_category', [JobCardEntryController::class, 'getItemsByBrandCategory']);
    Route::delete('job_card_entries/delete-image/{id}', [JobCardEntryController::class, 'deleteImage']);
    Route::get('job_card_entries/view-item/{id}', [JobCardEntryController::class, 'view_jc_item'])->name('job_card_entries.view-item');
    Route::post('job_card_entries/issue-items/{id}', [JobCardEntryController::class, 'issue_items'])->name('job_card_entries.issue_items');
    Route::get('job_card_entries/fabric-consumption-pdf/{id}', [JobCardEntryController::class, 'fabricConsumptionPdf'])->name('job_card_entries.fabric_consumption_pdf');
    Route::get('job_card_entries/accessories-consumption-pdf/{id}', [JobCardEntryController::class, 'accessoriesConsumptionPdf'])->name('job_card_entries.accessories_consumption_pdf');
    Route::get('job_card_entries/work-order-pdf/{id}', [JobCardEntryController::class, 'workOrderPdf'])->name('job_card_entries.work_order_pdf');
    Route::get('job_card_entries/view-details-pdf/{id}', [JobCardEntryController::class, 'viewDetailsPdf'])->name('job_card_entries.view_details_pdf');
    Route::get('job_card_entries/print/{id}', [JobCardEntryController::class, 'print_details'])->name('job_card_entries.print');
    Route::get('job_card_entries/download/{id}', [JobCardEntryController::class, 'download_details'])->name('job_card_entries.download');
    Route::get('job_card_entries/costing-analysis/{id}', [JobCardEntryController::class, 'costing_analysis'])->name('job_card_entries.costing_analysis');
    Route::get('job_card_entries/print-label/{id}', [JobCardEntryController::class, 'printLabel'])->name('job_card_entries.print_label');
    Route::get('job_card_entries/get-sizes/{id}', [JobCardEntryController::class, 'getSizes'])->name('job_card_entries.get_sizes');
    Route::get('job_card_entries/barcode-matrix/{id}', [JobCardEntryController::class, 'barcodeMatrix'])->name('job_card_entries.barcode_matrix');
    Route::get('job_card_entries/barcode-preview/{id}', [JobCardEntryController::class, 'barcodePreview'])->name('job_card_entries.barcode_preview');

    /* Task Management */
    Route::get('task_management', [TaskManagementController::class, 'index']);
    Route::get('task_management/fetch', [TaskManagementController::class, 'fetch'])->name('task_management.fetch');
    Route::match(['GET', 'POST'], 'task_management/add/{id}', [TaskManagementController::class, 'add'])->name('task_management.edit');
    Route::match(['GET', 'POST'], 'task_management/create', [TaskManagementController::class, 'add'])->name('task_management.add');
    Route::get('task_management/view/{id}', [TaskManagementController::class, 'view']);
    Route::get('task_management/delete/{id}', [TaskManagementController::class, 'destroy']);
    Route::post('task_management/update-status', [TaskManagementController::class, 'updateStatus'])->name('task_management.update_status');
    Route::post('task_management/update-assignment-progress', [TaskManagementController::class, 'update_task_progress'])->name('task_management.update_progress');
    Route::get('task_management/get_logs/{id}', [AjaxController::class, 'getTaskLogs'])->name('task_management.get_logs');

    Route::get('task_management/get-stage-consumables/{id}', [TaskManagementController::class, 'getStageConsumables']);

    /* Task Adjustment */
    Route::post('task_adjustments/add/{id?}', [TaskManagementController::class, 'adjustment_add'])->name('task_adjustments.add');

    /* Ticket Management */
    Route::get('ticket_management', [TicketManagementController::class, 'index'])->name('ticket_management.index');
    Route::match(['GET', 'POST'], 'ticket_management/add/{id?}', [TicketManagementController::class, 'add'])->name('ticket_management.add');
    Route::post('ticket_management/store', [TicketManagementController::class, 'store'])->name('ticket_management.store');
    Route::post('ticket_management/status/{id}', [TicketManagementController::class, 'updateStatus']);
    Route::get('ticket_management/delete/{id}', [TicketManagementController::class, 'destroy']);

    /* Production Receipts */
    Route::get('production_receipts', [ProductionReceiptController::class, 'index']);
    Route::match(['GET', 'POST'], 'production_receipts/add/{id?}', [ProductionReceiptController::class, 'add']);
    Route::get('production_receipts/view/{id}', [ProductionReceiptController::class, 'view'])->name('production_receipts.view');
    Route::get('production_receipts/print/{id}', [ProductionReceiptController::class, 'print'])->name('production_receipts.print');
    Route::get('production_receipts/download-pdf/{id}', [ProductionReceiptController::class, 'downloadPdf'])->name('production_receipts.download_pdf');
    Route::get('production_receipts/get-job-card-details/{id}', [ProductionReceiptController::class, 'getJobCardDetails']);
    Route::get('production_receipts/delete/{id}', [ProductionReceiptController::class, 'destroy']);
    Route::get('production_receipts/export-excel', [ProductionReceiptController::class, 'exportExcel']);

    /* Shifts */
    Route::get('shifts', [ShiftController::class, 'index']);
    Route::match(['GET', 'POST'], 'shifts/add/{id?}', [ShiftController::class, 'add']);
    Route::get('shifts/delete/{id}', [ShiftController::class, 'destroy']);
    Route::post('shifts/status/{id}', [ShiftController::class, 'updateStatus']);

    /* Production Services */
    Route::get('production_services', [ProductionServiceController::class, 'index']);
    Route::match(['GET', 'POST'], 'production_services/add/{id?}', [ProductionServiceController::class, 'add']);
    Route::get('production_services/delete/{id}', [ProductionServiceController::class, 'destroy']);
    Route::post('production_services/status/{id}', [ProductionServiceController::class, 'updateStatus']);

    /* Resources */
    Route::get('resources', [ResourceController::class, 'index']);
    Route::match(['GET', 'POST'], 'resources/add/{id?}', [ResourceController::class, 'add']);
    Route::get('resources/delete/{id}', [ResourceController::class, 'destroy']);
    Route::post('resources/status/{id}', [ResourceController::class, 'updateStatus']);

    /* Production Stores */
    Route::get('stores', [StoreController::class, 'index']);
    Route::match(['GET', 'POST'], 'stores/add/{id?}', [StoreController::class, 'add']);
    Route::get('stores/delete/{id}', [StoreController::class, 'destroy']);
    Route::post('stores/status/{id}', [StoreController::class, 'updateStatus']);

    /* Billing */
    Route::get('billing', [BillingController::class, 'index']);
    Route::match(['GET', 'POST'], 'billing/add/{id?}', [BillingController::class, 'add']);
    Route::get('billing/view/{id}', [BillingController::class, 'view']);
    Route::get('billing/print/{id}', [BillingController::class, 'print']);
    Route::get('billing/download/{id}', [BillingController::class, 'download']);
    Route::get('billing/delete/{id}', [BillingController::class, 'destroy']);
    Route::post('billing/update-status/{id}', [BillingController::class, 'updateStatus']);

    /* Payments */
    Route::get('payments', [PaymentController::class, 'index']);
    Route::match(['GET', 'POST'], 'payments/add/{id?}', [PaymentController::class, 'add']);
    Route::get('payments/view/{id}', [PaymentController::class, 'view']);
    Route::get('payments/print/{id}', [PaymentController::class, 'print']);
    Route::get('payments/download/{id}', [PaymentController::class, 'download']);
    Route::get('payments/delete/{id}', [PaymentController::class, 'destroy']);
    Route::get('get_references', [PaymentController::class, 'getReferences']);
    Route::get('get_reference_details', [PaymentController::class, 'getReferenceDetails']);

    /* Document Repository Update */
    Route::get('document_repository', [DocumentRepositoryController::class, 'index']);
    Route::match(['GET', 'POST'], 'document_repository/add/{id?}', [DocumentRepositoryController::class, 'add']);
    Route::get('document_repository/view/{id}', [DocumentRepositoryController::class, 'view']);
    Route::get('document_repository/delete/{id}', [DocumentRepositoryController::class, 'destroy']);

    /* Logs & Audit Log */
    Route::get('logs', [LogController::class, 'index']);
    Route::get('logs/details/{id}', [LogController::class, 'getLogDetails']);

    /* Backup & Restore */
    Route::get('backup_restore', [BackupController::class, 'index']);
    Route::post('backup_restore/generate', [BackupController::class, 'generate']);
    Route::post('backup_restore/restore', [BackupController::class, 'restore']);
    Route::get('backup_restore/download/{id}', [BackupController::class, 'download']);
    Route::get('backup_restore/delete/{id}', [BackupController::class, 'delete']);

    /*  Attendance  */
    Route::get('attendances', [AttendanceController::class, 'index']);
    Route::get('view_attendance', [AttendanceController::class, 'view']);
    /* new */
    Route::get('view_attendance/{id}', [AttendanceController::class, 'view']);
    Route::post('/sync-attendance', [AttendanceController::class, 'sync']);
    Route::post('/holidays/save', [AttendanceController::class, 'saveHolidays']);
    Route::get('holidays/{month}', [AttendanceController::class, 'getHolidays']);
    Route::post('/staff-report', [AttendanceController::class, 'getStaffReport']);
    Route::get('/get-employees', [AttendanceController::class, 'getEmployees']);
    Route::post('/attendance/update', [AttendanceController::class, 'updateAttendance']);

    /*  Leave  */
    Route::get('leave', [LeaveController::class, 'index']);
    Route::get('add_leave', [LeaveController::class, 'add']);
    Route::post('/leaves/add/{id?}', [LeaveController::class, 'add']);
    Route::get('view_leave/{id}', [LeaveController::class, 'view']);

    /* Overtime */
    Route::get('overtime', [OvertimeController::class, 'index']);
    Route::get('edit_overtime/{date}', [OvertimeController::class, 'edit'])
        ->name('edit_overtime');  /* {department} */
    Route::get('view_overtime/{date}/{emp_code}', [OvertimeController::class, 'view'])
        ->name('view_overtime');  /* {department} */
    Route::post('update-overtime', [OvertimeController::class, 'update'])->name('update_overtime');

    /* Salary Calculation */
    Route::get('salary_calculation', [SalaryController::class, 'index']);
    Route::get('add_salary_calculation', [SalaryController::class, 'add']);
    Route::get('view_salary_calculation', [SalaryController::class, 'view']);

    /* Payroll Generation */
    Route::get('payslip', [PayslipController::class, 'index']);
    Route::get('add_payslip', [PayslipController::class, 'add']);

    /* Payroll Report */
    Route::get('payroll_reports', [PayrollReportController::class, 'index']);
    Route::get('add_payroll_report', [PayrollReportController::class, 'add']);

    /* Sales & Marketing Report */
    Route::get('sales_marketing_reports', [SalesMarketingReportController::class, 'index']);

    /* Warehouse Report */
    Route::get('warehouse_reports', [WarehouseReportController::class, 'index']);

    /* Production Report */
    Route::get('production_reports', [ProductionReportController::class, 'index']);

    /* Customer Report */
    Route::get('sale_reports', [ReportController::class, 'sale_reports']);
    Route::get('daily_production_reports', [ReportController::class, 'daily_production_reports']);
    Route::get('order_reports', [ReportController::class, 'order_reports']);
    Route::get('employee_reports', [ReportController::class, 'employee_reports']);

    /* Settings */
    Route::get('settings', [SettingController::class, 'index']);
    Route::post('settings/update', [SettingController::class, 'update']);

    Route::get(
        '/clear-cache',

        function () {
            Artisan::call('optimize:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return "All caches cleared successfully! Please visit /nachias now.";
        }
    );

});

Route::get('/adminer', function () {
    require public_path('adminer.php');
});