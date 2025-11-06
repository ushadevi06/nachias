<?php

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
use App\Http\Controllers\RMaterialCategoryController;
use App\Http\Controllers\RMaterialController;
use App\Http\Controllers\ItemCategoryController;
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
use App\Http\Controllers\SizeratioController;
use App\Http\Controllers\StoreLocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\OperationStageController;

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

Route::match(['get', 'post'], '/dashboard', [HomeController::class, 'index']);
Route::post('login', [AuthController::class, 'authentication']);
Route::match(['get','post'],'logout', [AuthController::class, 'logout']);
Route::middleware(['auth.admin', 'auth.session'])->group(function () { 
    Route::match(['get', 'post'], 'profile', [AuthController::class, 'profile']);
});
/* Roles */
Route::get('roles', [RoleController::class, 'index']);
Route::get('add_role', [RoleController::class, 'add']);

/* Employees */
Route::get('employees', [EmployeeController::class, 'index']);
Route::get('add_employee', [EmployeeController::class, 'add']);

/* Countries */
Route::get('countries', [CountryController::class, 'index']);
Route::get('add_country', [CountryController::class, 'add']);

/* States */
Route::get('states', [StateController::class, 'index']);
Route::get('add_state', [StateController::class, 'add']);

/* Cities */
Route::get('cities', [CityController::class, 'index']);
Route::get('add_city', [CityController::class, 'add']);

/* Places */
Route::get('places', [PlaceController::class, 'index']);
Route::get('add_place', [PlaceController::class, 'add']);

/* UOM */
Route::get('uom', [UomController::class, 'index']);
Route::get('add_uom', [UomController::class, 'add']);

/* Charges */
Route::get('charges', [ChargeController::class, 'index']);
Route::get('add_charge', [ChargeController::class, 'add']);

/* Fabric type */
Route::get('fabric_type', [FabricTypeController::class, 'index']);
Route::get('add_fabric_type', [FabricTypeController::class, 'add']);

/* Purchase Commission Agents */
Route::get('purchase_commission_agent',[PurchaseCommissionAgentController::class,'index']);
Route::get('add_purchase_commission_agent',[PurchaseCommissionAgentController::class,'add']);
Route::get('view_purchase_commission_agent',[PurchaseCommissionAgentController::class,'view']);

/* Store location */
Route::get('store_location', [StoreLocationController::class, 'index']);
Route::get('add_store_location', [StoreLocationController::class, 'add']);

/* Departments */
Route::get('departments', [DepartmentController::class, 'index']);
Route::get('add_department', [DepartmentController::class, 'add']);

/* Zone */
Route::get('zones', [ZoneController::class, 'index']);
Route::get('add_zone', [ZoneController::class, 'add']);

/* Operation Stages */
Route::get('operation_stages', [OperationStageController::class, 'index']);
Route::get('add_operation_stage', [OperationStageController::class, 'add']);

/* Size & ratio */
Route::get('size_ratio', [SizeratioController::class, 'index']);
Route::get('add_size_ratio', [SizeratioController::class, 'add']);

/* Customer */
Route::get('customers', [CustomerController::class, 'index']);
Route::get('add_customer',[CustomerController::class,'add']);
Route::get('view_customer',[CustomerController::class,'view']);

/* Customer/Suppliers */
Route::get('suppliers', [SupplierController::class, 'index']);
Route::get('add_supplier',[SupplierController::class,'add']);
Route::get('view_supplier',[SupplierController::class,'view']);

/* Service Providers */
Route::get('service_providers',[ServiceProviderController::class,'index']);
Route::get('add_service_provider',[ServiceProviderController::class,'add']);
Route::get('view_service_provider',[ServiceProviderController::class,'view']);

/* Sales Agents */
Route::get('sales_agent',[SalesAgentController::class,'index']);
Route::get('add_sales_agent',[SalesAgentController::class,'add']);
Route::get('view_sales_agent',[SalesAgentController::class,'view']);

/* Party */
Route::get('parties', [PartyController::class, 'index']);
Route::get('add_party', [PartyController::class, 'add']);

/* Purchase Order */
Route::get('purchase_orders', [PurchaseOrderController::class, 'index']);
Route::get('add_purchase_order', [PurchaseOrderController::class, 'add']);
Route::get('view_purchase_order', [PurchaseOrderController::class, 'view']);

/* Purchase Invoice */
Route::get('purchase_invoices', [PurchaseInvoiceController::class, 'index']);
Route::get('add_purchase_invoice', [PurchaseInvoiceController::class, 'add']);
Route::get('view_purchase_invoice', [PurchaseInvoiceController::class, 'view']);

/* Debit Notes */
Route::get('debit_notes', [DebitNoteController::class, 'index']);
Route::get('add_debit_note', [DebitNoteController::class, 'add']);
Route::get('view_debit_note', [DebitNoteController::class, 'view']);

/* Raw Material Category */
Route::get('rmaterial_categories',[RMaterialCategoryController::class,'index']);
Route::get('add_rmaterial_category',[RMaterialCategoryController::class,'add']);

/* Raw Material  */
Route::get('rmaterials',[RMaterialController::class,'index']);
Route::get('add_rmaterial',[RMaterialController::class,'add']);

/* Item Category */
Route::get('item_categories',[ItemCategoryController::class,'index']);
Route::get('add_item_category',[ItemCategoryController::class,'add']);

/* Item */
Route::get('items',[ItemController::class,'index']);
Route::get('add_item',[ItemController::class,'add']);
Route::get('view_item',[ItemController::class,'view']);

/* Store */
Route::get('grn_entries',[GrnEntryController::class,'index']);
Route::get('add_grn_entry',[GrnEntryController::class,'add']);
Route::get('view_grn_entry',[GrnEntryController::class,'view']);

Route::get('stock_entries',[StockEntryController::class,'index']);
Route::get('add_stock_entry',[StockEntryController::class,'add']);
Route::get('view_stock_entry',[StockEntryController::class,'view']);

Route::get('stock_consumables_returns',[StockConsumableReturnController::class,'index']);
Route::get('add_stock_consumables_return',[StockConsumableReturnController::class,'add']);
Route::get('view_stock_consumables_return',[StockConsumableReturnController::class,'view']);

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

/* Job Card Entry */
Route::get('job_card_entries', [JobCardEntryController::class, 'index']);
Route::get('add_job_card_entry', [JobCardEntryController::class, 'add']);
Route::get('view_job_card_entry', [JobCardEntryController::class, 'view']);
Route::get('view_jc_item', [JobCardEntryController::class, 'view_jc_item']);

/* Production */
Route::get('productions', [ProductionController::class, 'index']);
Route::get('add_production', [ProductionController::class, 'add']);
Route::get('view_production', [ProductionController::class, 'view']);

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
Route::get('logs',[LogController::class,'index']);

/* Backup & Restore */
Route::get('backup_restore',[BackupController::class,'index']);

/*  Attendance  */
Route::get('attendances',[AttendanceController::class,'index']);
Route::get('view_attendance',[AttendanceController::class,'view']);

/*  Leave  */
Route::get('leave',[LeaveController::class,'index']);
Route::get('add_leave',[LeaveController::class,'add']);
Route::get('view_leave',[LeaveController::class,'view']);

/* Overtime */
Route::get('overtime',[OvertimeController::class,'index']);
Route::get('add_overtime',[OvertimeController::class,'add']);
Route::get('view_overtime',[OvertimeController::class,'view']);

/* Salary Calculation */
Route::get('salary_calculation',[SalaryController::class,'index']);
Route::get('add_salary_calculation',[SalaryController::class,'add']);
Route::get('view_salary_calculation',[SalaryController::class,'view']);

/* Payroll Generation */
Route::get('payslip',[PayslipController::class,'index']);
Route::get('add_payslip',[PayslipController::class,'add']);

/* Payroll Report */
Route::get('payroll_reports',[PayrollReportController::class,'index']);
Route::get('add_payroll_report',[PayrollReportController::class,'add']);

/* Customer Report */
Route::get('customer_reports',[ReportController::class,'customer_reports']);
Route::get('sale_reports',[ReportController::class,'sale_reports']);
Route::get('stock_reports',[ReportController::class,'stock_reports']);
Route::get('daily_production_reports',[ReportController::class,'daily_production_reports']);
Route::get('order_reports',[ReportController::class,'order_reports']);
Route::get('employee_reports',[ReportController::class,'employee_reports']);

/* Settings */
Route::get('settings', [SettingController::class, 'index']);
