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
use App\Http\Controllers\SalesAgentController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\BrandCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GrnEntryController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockConsumableReturnController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\SaleInvoiceController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\JobCardEntryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TaskCreationController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskTrackingMonitoringController;
use App\Http\Controllers\TaskStatusUpdateController;
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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});
Route::get('/update_page', function () {
    return view('update_page');
});
Route::post('login', [AuthController::class, 'authentication']);
Route::middleware(['auth.admin', 'auth.session', 'role.active','employee.active'])->group(function () {
    Route::match(['get', 'post'], '/dashboard', [HomeController::class, 'index']);
    Route::match(['get', 'post'], 'profile', [AuthController::class, 'profile']);
    Route::match(['get', 'post'], 'logout', [AuthController::class, 'logout']);

    /* Roles */
    Route::get('/roles', [RoleController::class, 'index']);
    Route::match(['GET', 'POST'], '/roles/add/{id?}', [RoleController::class, 'add']);
    Route::post('roles/status/{id}', [RoleController::class, 'updateStatus']);
    Route::get('/roles/delete/{id}', [RoleController::class, 'destroy']);
    
    /* Ajax */
    Route::get('get-cities/{state_id}', [AjaxController::class, 'fetchCities']);
    Route::get('/get-places/{city_id}', [AjaxController::class, 'fetchPlaces']);
    Route::get('/raw-materials-by-category/{categoryId}', [AjaxController::class, 'getRawMaterialsByCategory']);
    Route::get('/get_charges', [AjaxController::class, 'getCharges']);
    Route::get('get-materials-by-category/{category_id}', [AjaxController::class, 'getMaterialsByCategory']);

    /* Employees */
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('employees/add/{id?}', [EmployeeController::class, 'add']);
    Route::post('employees/add/{id?}', [EmployeeController::class, 'add']);
    Route::post('employees/status/{id}', [EmployeeController::class, 'updateStatus']);
    Route::get('employees/delete/{id}', [EmployeeController::class, 'destroy']);

    /* States */
    Route::get('/states', [StateController::class, 'index']);
    Route::match(['get', 'post'], '/states/add/{id?}', [StateController::class, 'add']);
    Route::post('state/status/{id}', [StateController::class, 'updateStatus']);
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
    Route::match(['get','post'], '/uoms/add/{id?}', [UomController::class, 'add']);
    Route::get('/uoms/delete/{id}', [UomController::class, 'destroy']);
    Route::post('/uoms/status/{id}', [UomController::class, 'updateStatus']);

    /* Colors */
    Route::get('/colors', [ColorController::class, 'index']);
    Route::match(['get','post'], '/colors/add/{id?}', [ColorController::class, 'add']);
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

    /* Purchase Commission Agents */
    Route::get('purchase_commission_agent', [PurchaseCommissionAgentController::class, 'index']);
    Route::match(['GET', 'POST'], '/purchase_commission_agent/add/{id?}', [PurchaseCommissionAgentController::class, 'add']);
    Route::get('view_purchase_commission_agent/{id}', [PurchaseCommissionAgentController::class, 'view']);
    Route::post('purchase_commission_agent/status/{id}', [PurchaseCommissionAgentController::class, 'updateStatus']);
    Route::delete('purchase_commission_agent/delete/{id}', [PurchaseCommissionAgentController::class, 'destroy']);

    /* Store location */
    Route::get('/store_location', [StorelocationController::class, 'index']);
    Route::match(['GET', 'POST'], '/store_location/add/{id?}', [StorelocationController::class, 'add']);
    Route::get('/store_location/delete/{id}', [StorelocationController::class, 'destroy']);
    Route::post('/store_location/status/{id}', [StorelocationController::class, 'updateStatus']);

    /* Departments */
    Route::post('/department/status/{id}', [DepartmentController::class, 'updateStatus']);

    /* Styles */
    Route::get('/styles', [StyleController::class, 'index']);
    Route::match(['GET', 'POST'], '/styles/add/{id?}', [StyleController::class, 'add']);
    Route::get('/styles/delete/{id}', [StyleController::class, 'destroy']);
    Route::post('/styles/status/{id}', [StyleController::class, 'updateStatus']);

    /* Tax Types */
    Route::get('tax_types', [TaxTypeController::class, 'index']);
    Route::get('taxes/add_type', [TaxTypeController::class, 'add']);

    /* Tax */
    Route::get('/taxes', [TaxController::class, 'index']);
    Route::match(['GET', 'POST'], '/taxes/add/{id?}', [TaxController::class, 'add']);
    Route::get('/tax/delete/{id}', [TaxController::class, 'destroy']);
    Route::post('/tax/status/{id}', [TaxController::class, 'updateStatus']);

    /* Fit */
    Route::get('fit', [FitController::class, 'index']);
    Route::get('fit/add', [FitController::class, 'add']);

    /* Patti Type */
    Route::get('patti_type', [PattiTypeController::class, 'index']);
    Route::get('patti_type/add', [PattiTypeController::class, 'add']);

    /* Collar Type */
    Route::get('collar_type', [CollarTypeController::class, 'index']);
    Route::get('collar_type/add', [CollarTypeController::class, 'add']);

    /* Cuff Type */
    Route::get('cuff_type', [CuffTypeController::class, 'index']);
    Route::get('cuff_type/add', [CuffTypeController::class, 'add']);

    /* Pocket Type */
    Route::get('pocket_type', [PocketTypeController::class, 'index']);
    Route::get('pocket_type/add', [PocketTypeController::class, 'add']);

    /* Bottom Cut */
    Route::get('bottom_cut', [BottomCutController::class, 'index']);
    Route::get('bottom_cut/add', [BottomCutController::class, 'add']);

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
    Route::match(['GET','POST'], 'customers/add/{id?}', [CustomerController::class, 'add']);
    Route::post('customers/status/{id}', [CustomerController::class, 'updateStatus']);
    Route::get('view_customer/{id}', [CustomerController::class, 'view']);
    Route::get('customers/delete/{id}', [CustomerController::class, 'destroy']);


    /* Customer/Suppliers */
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::match(['GET', 'POST'], '/suppliers/add/{id?}', [SupplierController::class, 'add']);
    Route::get('/supplier/delete/{id}', [SupplierController::class, 'destroy']);
    Route::post('/supplier/status/{id}', [SupplierController::class, 'updateStatus']);
    Route::get('/view_supplier/{id}', [SupplierController::class, 'view']);

    /* Service Providers */
    Route::get('service_providers', [ServiceProviderController::class, 'index']);
    Route::match(['get','post'],'service_providers/add', [ServiceProviderController::class, 'add']);
    Route::post('/service_provider/status/{id}', [ServiceProviderController::class, 'updateStatus']);
    Route::match(['get','post'],'service_providers/add/{id}', [ServiceProviderController::class, 'add']);
    Route::delete('service_provider/delete/{id}', [ServiceProviderController::class, 'destroy']);

    /* Sales Agents */
    Route::get('sales_agents', [SalesAgentController::class, 'index']);
    Route::match(['GET', 'POST'], 'sales_agents/add/{id?}', [SalesAgentController::class, 'add']);
    Route::get('sales_agent/{id}', [SalesAgentController::class, 'show']);
    Route::get('sales_agent/delete/{id}', [SalesAgentController::class, 'destroy']);
    Route::post('sales_agent/status/{id}', [SalesAgentController::class, 'updateStatus']);


    /* Purchase Order */
    Route::get('/purchase_orders', [PurchaseOrderController::class, 'index'])->name('purchase_orders.index');
    Route::get('/purchase_orders/add/{id?}', [PurchaseOrderController::class, 'add'])->name('purchase_orders.add');
    Route::post('/purchase_orders/add/{id?}', [PurchaseOrderController::class, 'add']);
    Route::get('/purchase_orders/view/{id}', [PurchaseOrderController::class, 'view'])->name('purchase_orders.view');
    Route::get('/purchase_orders/delete/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase_orders.delete');
    Route::post('/purchase_orders/status/{id}', [PurchaseOrderController::class, 'updateStatus'])->name('purchase_orders.status');

    /* Purchase Invoice */
    Route::get('purchase_invoices', [PurchaseInvoiceController::class, 'index']);
    Route::get('purchase_invoices/add/{id?}', [PurchaseInvoiceController::class, 'add']);
    Route::post('purchase_invoices/add/{id?}', [PurchaseInvoiceController::class, 'add']);
    Route::get('purchase_invoices/view/{id}', [PurchaseInvoiceController::class, 'view']);
    Route::get('purchase_invoices/delete/{id}', [PurchaseInvoiceController::class, 'destroy']);
    Route::post('purchase_invoices/update-status/{id}', [PurchaseInvoiceController::class, 'updateStatus']);
    Route::get('purchase_invoices/get-po-details/{id}', [PurchaseInvoiceController::class, 'getPurchaseOrderDetails']);
    Route::get('purchase_invoices/get-items/{id}', [PurchaseInvoiceController::class, 'getInvoiceItems']);
    Route::get('purchase_invoices/download-pdf/{id}', [PurchaseInvoiceController::class, 'downloadPdf']);
    Route::delete('purchase_invoices/delete-charge/{id}', [PurchaseInvoiceController::class, 'deleteCharge']);

    /* Debit Notes */
    Route::get('debit_notes', [DebitNoteController::class, 'index']);
    Route::get('add_debit_note', [DebitNoteController::class, 'add']);
    Route::get('view_debit_note', [DebitNoteController::class, 'view']);

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

    /* Grn Entry */
    Route::get('grn_entries', [GrnEntryController::class, 'index']);
    Route::match(['get', 'post'], 'grn_entries/add/{id?}', [GrnEntryController::class, 'add']);
    Route::delete('grn_entries/delete/{id}', [GrnEntryController::class, 'destroy']);
    Route::post('grn_entries/status/{id}', [GrnEntryController::class, 'updateStatus']);
    Route::get('grn_entries/get-invoice-details/{id}', [GrnEntryController::class, 'getInvoiceDetails']);
    Route::get('grn_entries/view/{id}', [GrnEntryController::class, 'view']);

    /* Stock Entry */
    Route::get('stock_entries/get-grn-items/{id}', [StockEntryController::class, 'getGrnEntryItems']);
    Route::get('stock_entries', [StockEntryController::class, 'index']);
    Route::match(['get', 'post'], 'stock_entries/add/{id?}', [StockEntryController::class, 'add']);
    Route::get('stock_entries/view/{id}', [StockEntryController::class, 'view']);

    /* Store */
    // Route::get('grn_entries', [GrnEntryController::class, 'index']);
    // Route::get('add_grn_entry', [GrnEntryController::class, 'add']);
    // Route::get('view_grn_entry', [GrnEntryController::class, 'view']);

    // Route::get('stock_entries', [StockEntryController::class, 'index']);
    // Route::get('add_stock_entry', [StockEntryController::class, 'add']);
    // Route::get('view_stock_entry', [StockEntryController::class, 'view']);

    Route::get('stock_consumables_returns', [StockConsumableReturnController::class, 'index']);
    Route::get('add_stock_consumables_return', [StockConsumableReturnController::class, 'add']);
    Route::get('view_stock_consumables_return', [StockConsumableReturnController::class, 'view']);

    /* Sales Order */
    Route::get('sales_order', [SaleOrderController::class, 'index']);
    Route::get('add_sale_order', [SaleOrderController::class, 'add']);
    Route::get('view_sale_order', [SaleOrderController::class, 'view']);

    /* Sales Invoice */
    Route::get('sales_invoice', [SaleInvoiceController::class, 'index']);
    Route::get('add_sale_invoice', [SaleInvoiceController::class, 'add']);
    Route::get('view_sale_invoice', [SaleInvoiceController::class, 'view']);

    /* Credit Notes */
    Route::get('credit_notes', [CreditNoteController::class, 'index']);
    Route::get('add_credit_note', [CreditNoteController::class, 'add']);
    Route::get('view_credit_note', [CreditNoteController::class, 'view']);

    /* Debit Notes */
    Route::get('debit_notes', [DebitNoteController::class, 'index']);
    Route::match(['GET', 'POST'], 'debit_notes/add/{id?}', [DebitNoteController::class, 'add']);
    Route::get('debit_notes/view/{id}', [DebitNoteController::class, 'view']);
    Route::get('debit_notes/delete/{id}', [DebitNoteController::class, 'destroy']);
    Route::get('debit_notes/get-invoice-details/{id}', [DebitNoteController::class, 'getInvoiceDetails']);

    /* Job Card Entry */
    Route::get('job_card_entries', [JobCardEntryController::class, 'index']);
    Route::get('add_job_card_entry', [JobCardEntryController::class, 'add']);
    Route::get('view_job_card_entry', [JobCardEntryController::class, 'view']);
    Route::get('view_jc_item', [JobCardEntryController::class, 'view_jc_item']);

    /* Production */
    Route::get('productions', [ProductionController::class, 'index']);
    Route::get('add_production', [ProductionController::class, 'add']);
    Route::get('view_production', [ProductionController::class, 'view']);
    Route::get('task_management', [TaskManagementController::class, 'index']);
    Route::get('add_task_management', [TaskManagementController::class, 'add']);

    /* Billing */
    Route::get('billing', [BillingController::class, 'index']);
    Route::get('add_billing', [BillingController::class, 'add']);
    Route::get('view_billing', [BillingController::class, 'view']);
    Route::get('billing_invoice', [BillingController::class, 'billing_invoice']);

    /* Payments */
    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('add_payment', [PaymentController::class, 'add']);
    Route::get('view_payment', [PaymentController::class, 'view']);

    /* Task Creation */
    Route::get('task_creation', [TaskCreationController::class, 'index']);
    Route::get('add_task_creation', [TaskCreationController::class, 'add']);
    Route::get('view_task_creation', [TaskCreationController::class, 'view']);

    /* Task Assignment */
    Route::get('task_assignment', [TaskAssignmentController::class, 'index']);
    Route::get('add_task_assignment', [TaskAssignmentController::class, 'add']);
    Route::get('view_task_assignment', [TaskAssignmentController::class, 'view']);

    /* Task Tracking & Monitoring */
    Route::get('task_tracking_monitoring', [TaskTrackingMonitoringController::class, 'index']);
    Route::get('add_task_tracking_monitoring', [TaskTrackingMonitoringController::class, 'add']);
    Route::get('view_task_tracking_monitoring', [TaskTrackingMonitoringController::class, 'view']);

    /* Task Status Update */
    Route::get('task_status_updates', [TaskStatusUpdateController::class, 'index']);
    Route::get('add_task_status_update', [TaskStatusUpdateController::class, 'add']);
    Route::get('view_task_status_update', [TaskStatusUpdateController::class, 'view']);

    /* Document Repository Update */
    Route::get('document_repository', [DocumentRepositoryController::class, 'index']);
    Route::get('add_document_repository', [DocumentRepositoryController::class, 'add']);
    Route::get('view_document_repository', [DocumentRepositoryController::class, 'view']);

    /* Logs & Audit Log */
    Route::get('logs', [LogController::class, 'index']);
    Route::get('logs/details/{id}', [LogController::class, 'getLogDetails']);

    /* Backup & Restore */
    Route::get('backup_restore', [BackupController::class, 'index']);

    /*  Attendance  */
    Route::get('attendances', [AttendanceController::class, 'index']);
    Route::get('view_attendance', [AttendanceController::class, 'view']);

    /*  Leave  */
    Route::get('leave', [LeaveController::class, 'index']);
    Route::get('add_leave', [LeaveController::class, 'add']);
    Route::get('view_leave', [LeaveController::class, 'view']);

    /* Overtime */
    Route::get('overtime', [OvertimeController::class, 'index']);
    Route::get('add_overtime', [OvertimeController::class, 'add']);
    Route::get('view_overtime', [OvertimeController::class, 'view']);

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

    /* Customer Report */
    Route::get('customer_reports', [ReportController::class, 'customer_reports']);
    Route::get('sale_reports', [ReportController::class, 'sale_reports']);
    Route::get('stock_reports', [ReportController::class, 'stock_reports']);
    Route::get('daily_production_reports', [ReportController::class, 'daily_production_reports']);
    Route::get('order_reports', [ReportController::class, 'order_reports']);
    Route::get('employee_reports', [ReportController::class, 'employee_reports']);

    /* Settings */
    Route::get('settings', [SettingController::class, 'index']);
    Route::post('settings/update', [SettingController::class, 'update']);

    Route::get('/run-artisan-commands', function () {

        Artisan::call('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
        ]);

        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');

        return "Artisan commands executed successfully";
    });

});

Route::group(['middleware' => ['auth']], function () { Route::get('purchase_invoices/payment-history/{id}', [\App\Http\Controllers\PurchaseInvoiceController::class, 'getPaymentHistory']); });
