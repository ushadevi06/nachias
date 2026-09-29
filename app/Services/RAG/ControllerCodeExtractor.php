<?php

namespace App\Services\RAG;

use Illuminate\Support\Str;

class ControllerCodeExtractor
{
    /**
     * Map of controller class names to ERP module and screen names.
     */
    protected array $controllerMap = [
        'PurchaseOrderController' => ['module' => 'Purchase', 'screen' => 'Purchase Orders', 'url' => '/purchase_orders'],
        'PurchaseInvoiceController' => ['module' => 'Purchase', 'screen' => 'Purchase Invoices', 'url' => '/purchase_invoices'],
        'PurchaseCommissionAgentController' => ['module' => 'Purchase', 'screen' => 'Purchase Commission Agents', 'url' => '/purchase_commission_agents'],
        'PurchaseReportController' => ['module' => 'Reports', 'screen' => 'Purchase Reports', 'url' => '/purchase_reports'],
        'SalesOrderController' => ['module' => 'Sales', 'screen' => 'Sales Orders', 'url' => '/sales_orders'],
        'SalesInvoiceController' => ['module' => 'Sales', 'screen' => 'Sales Invoices', 'url' => '/sales_invoices'],
        'OldSalesInvoiceController' => ['module' => 'Sales', 'screen' => 'Old Sales Invoices', 'url' => '/old_sales_invoices'],
        'SalesAgentController' => ['module' => 'Sales', 'screen' => 'Sales Agents', 'url' => '/sales_agents'],
        'SalesMarketingReportController' => ['module' => 'Reports', 'screen' => 'Sales & Marketing Reports', 'url' => '/sales_marketing_reports'],
        'GRNEntryController' => ['module' => 'Store', 'screen' => 'GRN Entry', 'url' => '/grn_entries'],
        'StockEntryController' => ['module' => 'Store', 'screen' => 'Stock Entry', 'url' => '/stock_entries'],
        'StockConsumableReturnController' => ['module' => 'Store', 'screen' => 'Stock Consumables Returns', 'url' => '/stock_consumables_returns'],
        'ItemController' => ['module' => 'Master', 'screen' => 'Items', 'url' => '/items'],
        'RawMaterialController' => ['module' => 'Master', 'screen' => 'Raw Materials', 'url' => '/raw_materials'],
        'ItemPriceController' => ['module' => 'Master', 'screen' => 'Item Prices', 'url' => '/item_prices'],
        'WarehouseController' => ['module' => 'Master', 'screen' => 'Warehouses', 'url' => '/warehouses'],
        'WarehouseReportController' => ['module' => 'Reports', 'screen' => 'Warehouse Reports', 'url' => '/warehouse_reports'],
        'StoreController' => ['module' => 'Master', 'screen' => 'Stores', 'url' => '/stores'],
        'StoreLocationController' => ['module' => 'Master', 'screen' => 'Store Locations', 'url' => '/store_locations'],
        'StoreCategoryController' => ['module' => 'Master', 'screen' => 'Store Categories', 'url' => '/store_categories'],
        'JobCardEntryController' => ['module' => 'Production', 'screen' => 'Job Card Entry', 'url' => '/job_card_entries'],
        'ProductionReceiptController' => ['module' => 'Production', 'screen' => 'Production Receipts', 'url' => '/production_receipts'],
        'ProductionReportController' => ['module' => 'Reports', 'screen' => 'Production Reports', 'url' => '/production_reports'],
        'ProductionServiceController' => ['module' => 'Production', 'screen' => 'Production Services', 'url' => '/production_services'],
        'ProcessGroupController' => ['module' => 'Production', 'screen' => 'Process Groups', 'url' => '/process_groups'],
        'StandardConsumptionController' => ['module' => 'Production', 'screen' => 'Standard Consumption', 'url' => '/standard_consumptions'],
        'OperationStageController' => ['module' => 'Production', 'screen' => 'Operation Stages', 'url' => '/operation_stages'],
        'CustomerController' => ['module' => 'Master', 'screen' => 'Customers', 'url' => '/customers'],
        'SupplierController' => ['module' => 'Master', 'screen' => 'Suppliers', 'url' => '/suppliers'],
        'RetailerController' => ['module' => 'Master', 'screen' => 'Retailers', 'url' => '/retailers'],
        'BillingController' => ['module' => 'Billing', 'screen' => 'Billing', 'url' => '/billing'],
        'PaymentController' => ['module' => 'Manage Payments', 'screen' => 'Manage Payments', 'url' => '/payments'],
        'DebitNoteController' => ['module' => 'Manage Payments', 'screen' => 'Debit Notes', 'url' => '/debit_notes'],
        'CreditNoteController' => ['module' => 'Sales', 'screen' => 'Credit Notes', 'url' => '/credit_notes'],
        'AttendanceController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Attendance Management', 'url' => '/attendance'],
        'EmployeeAttendanceController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Employee Attendance', 'url' => '/employee_attendance'],
        'EmployeeController' => ['module' => 'Employees', 'screen' => 'Employees', 'url' => '/employees'],
        'SalaryController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Monthly Payroll', 'url' => '/monthly_payroll'],
        'LeaveController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Leave Management', 'url' => '/leave'],
        'OvertimeController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Overtime', 'url' => '/overtime'],
        'PayrollReportController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Payroll Reports', 'url' => '/payroll_reports'],
        'ShiftController' => ['module' => 'Emp. Payroll & Attendance', 'screen' => 'Shifts', 'url' => '/shifts'],
        'BrandController' => ['module' => 'Master', 'screen' => 'Brands', 'url' => '/brands'],
        'BrandCategoryController' => ['module' => 'Master', 'screen' => 'Brand Categories', 'url' => '/brand_categories'],
        'StyleController' => ['module' => 'Master', 'screen' => 'Styles', 'url' => '/styles'],
        'ColorController' => ['module' => 'Master', 'screen' => 'Colors', 'url' => '/colors'],
        'UomController' => ['module' => 'Master', 'screen' => 'Units of Measurement (UOM)', 'url' => '/uoms'],
        'SeasonController' => ['module' => 'Master', 'screen' => 'Seasons', 'url' => '/seasons'],
        'StateController' => ['module' => 'Master', 'screen' => 'States', 'url' => '/states'],
        'CityController' => ['module' => 'Master', 'screen' => 'Cities', 'url' => '/cities'],
        'PlaceController' => ['module' => 'Master', 'screen' => 'Places', 'url' => '/places'],
        'ZoneController' => ['module' => 'Master', 'screen' => 'Zones', 'url' => '/zones'],
        'CountryController' => ['module' => 'Master', 'screen' => 'Countries', 'url' => '/countries'],
        'DepartmentController' => ['module' => 'Employees', 'screen' => 'Departments', 'url' => '/departments'],
        'RoleController' => ['module' => 'Employees', 'screen' => 'Roles & Permissions', 'url' => '/roles'],
        'FabricTypeController' => ['module' => 'Master', 'screen' => 'Fabric Types', 'url' => '/fabric_types'],
        'FabricSizeController' => ['module' => 'Master', 'screen' => 'Fabric Sizes', 'url' => '/fabric_sizes'],
        'CollarTypeController' => ['module' => 'Master', 'screen' => 'Collar Types', 'url' => '/collar_types'],
        'CuffTypeController' => ['module' => 'Master', 'screen' => 'Cuff Types', 'url' => '/cuff_types'],
        'PocketTypeController' => ['module' => 'Master', 'screen' => 'Pocket Types', 'url' => '/pocket_types'],
        'PattiTypeController' => ['module' => 'Master', 'screen' => 'Patti Types', 'url' => '/patti_types'],
        'FitController' => ['module' => 'Master', 'screen' => 'Fits', 'url' => '/fits'],
        'BottomCutController' => ['module' => 'Master', 'screen' => 'Bottom Cuts', 'url' => '/bottom_cuts'],
        'ShippingMethodController' => ['module' => 'Master', 'screen' => 'Shipping Methods', 'url' => '/shipping_methods'],
        'TransportModeController' => ['module' => 'Master', 'screen' => 'Transport Modes', 'url' => '/transport_modes'],
        'TaxController' => ['module' => 'Master', 'screen' => 'Taxes', 'url' => '/taxes'],
        'TaxTypeController' => ['module' => 'Master', 'screen' => 'Tax Types', 'url' => '/tax_types'],
        'TaxProCredentialController' => ['module' => 'System Utility', 'screen' => 'TaxPro Credentials', 'url' => '/taxpro-credentials'],
        'TicketManagementController' => ['module' => 'System Utility', 'screen' => 'Ticket Management', 'url' => '/ticket_management'],
        'TaskManagementController' => ['module' => 'System Utility', 'screen' => 'Task Management', 'url' => '/task_management'],
        'SettingController' => ['module' => 'System Utility', 'screen' => 'Settings', 'url' => '/settings'],
        'BackupController' => ['module' => 'System Utility', 'screen' => 'Database Backup', 'url' => '/backup'],
        'LogController' => ['module' => 'System Utility', 'screen' => 'Activity Logs', 'url' => '/logs'],
        'DocumentRepositoryController' => ['module' => 'System Utility', 'screen' => 'Document Repository', 'url' => '/document_repository'],
        'ServiceProviderController' => ['module' => 'Master', 'screen' => 'Service Providers', 'url' => '/service_providers'],
        'FgMinStockController' => ['module' => 'Store', 'screen' => 'FG Min Stock', 'url' => '/fg_min_stocks'],
        'CoreMaterialPlannerSettingController' => ['module' => 'Master', 'screen' => 'Material Planner Settings', 'url' => '/material_planner_settings'],
    ];

    /**
     * Extract structured knowledge chunks from all active Laravel controllers.
     *
     * @return array<int, array>
     */
    public function extract(): array
    {
        $controllersPath = app_path('Http/Controllers');
        if (!is_dir($controllersPath)) {
            return [];
        }

        $files = glob($controllersPath . '/*.php');
        $chunks = [];

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip base abstract controller or temporary versions
            if ($filename === 'Controller.php' || str_contains($filename, '-new') || str_contains($filename, '-05-08')) {
                continue;
            }

            $className = str_replace('.php', '', $filename);
            $parsedChunk = $this->parseControllerFile($file, $className);
            if ($parsedChunk) {
                $chunks[] = $parsedChunk;
            }
        }

        return $chunks;
    }

    /**
     * Parse a single controller file into an authoritative RAG knowledge chunk.
     */
    protected function parseControllerFile(string $filePath, string $className): ?array
    {
        $content = file_get_contents($filePath);
        if (empty($content)) {
            return null;
        }

        $metaInfo = $this->controllerMap[$className] ?? [
            'module' => $this->guessModule($className),
            'screen' => $this->guessScreenName($className),
            'url' => '/' . Str::snake(str_replace('Controller', '', $className)),
        ];

        $module = $metaInfo['module'];
        $screen = $metaInfo['screen'];
        $url = $metaInfo['url'];

        // 1. Extract Eloquent Models used
        $models = [];
        if (preg_match_all('/use\s+App\\\\Models\\\\([A-Za-z0-9_]+);/', $content, $modelMatches)) {
            $models = array_unique($modelMatches[1]);
        }

        // 2. Extract Permissions checked
        $permissions = [];
        if (preg_match_all('/(?:can|hasPermissionTo)\([\'"]([^\'"]+)[\'"]\)/', $content, $permMatches)) {
            $permissions = array_unique($permMatches[1]);
        }

        // 3. Extract Public Methods
        $methods = $this->extractMethods($content);

        // 4. Extract Validation Rules
        $validationRules = $this->extractValidationRules($content);

        // 5. Extract Status Transitions & Business Rules
        $statusFlows = $this->extractStatusFlows($content);

        // 6. Build Rich Content Markdown
        $doc = [];
        $doc[] = "### Controller: {$className}";
        $doc[] = "**ERP Module:** {$module}";
        $doc[] = "**Primary Screen:** {$screen}";
        $doc[] = "**Base URL:** `{$url}`";
        if (!empty($models)) {
            $doc[] = "**Related Database Models:** " . implode(', ', array_map(fn($m) => "`{$m}`", $models));
        }
        if (!empty($permissions)) {
            $doc[] = "**Required Permissions:** " . implode(', ', array_map(fn($p) => "`{$p}`", $permissions));
        }

        $doc[] = "\n#### Controller Actions & Endpoints:";
        foreach ($methods as $methodName => $methodInfo) {
            $doc[] = "- **`{$methodName}()`**: {$methodInfo['description']}";
            if (!empty($methodInfo['actions'])) {
                foreach ($methodInfo['actions'] as $act) {
                    $doc[] = "  * {$act}";
                }
            }
        }

        if (!empty($validationRules)) {
            $doc[] = "\n#### Validation Rules & Required Form Fields:";
            foreach ($validationRules as $field => $ruleStr) {
                $doc[] = "- **`{$field}`**: `{$ruleStr}`";
            }
        }

        if (!empty($statusFlows)) {
            $doc[] = "\n#### Document Status Transitions & Business Flow Rules:";
            foreach ($statusFlows as $flow) {
                $doc[] = "- {$flow}";
            }
        }

        $markdownContent = implode("\n", $doc);

        // Build search keywords
        $keywords = [
            $className,
            $screen,
            $module,
            $url,
            str_replace('Controller', '', $className),
            'controller logic',
            'backend code',
            'validation rules',
            'actions',
            'endpoints',
        ];
        foreach ($models as $m) {
            $keywords[] = $m;
        }
        foreach (array_keys($methods) as $mName) {
            $keywords[] = $mName;
        }
        foreach (array_keys($validationRules) as $fName) {
            $keywords[] = $fName;
        }

        return [
            'source_type' => 'controller',
            'module' => $module,
            'screen' => $screen,
            'title' => "Controller: {$className} ({$screen} Backend Logic)",
            'keywords' => implode(', ', array_unique(array_filter($keywords))),
            'url' => $url,
            'menu_path' => "{$module} > {$screen}",
            'content' => $markdownContent,
            'meta' => [
                'class' => $className,
                'module' => $module,
                'screen' => $screen,
                'url' => $url,
                'models' => $models,
                'permissions' => $permissions,
                'methods' => array_keys($methods),
            ],
        ];
    }

    /**
     * Extract public methods and their behavioral descriptions.
     */
    protected function extractMethods(string $content): array
    {
        $methods = [];
        $pattern = '/public\s+function\s+([a-zA-Z0-9_]+)\s*\(([^)]*)\)/';

        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $methodName = $matches[1][$i][0];
                $params = trim($matches[2][$i][0]);
                $offset = $matches[0][$i][1];

                // Skip constructor and magic methods
                if (str_starts_with($methodName, '__')) {
                    continue;
                }

                // Get approximate method body slice
                $endOffset = ($i + 1 < $count) ? $matches[0][$i + 1][1] : strlen($content);
                $body = substr($content, $offset, min(2500, $endOffset - $offset));

                $desc = $this->summarizeMethod($methodName, $params, $body);
                $actions = $this->extractMethodActions($body);

                $methods[$methodName] = [
                    'params' => $params,
                    'description' => $desc,
                    'actions' => $actions,
                ];
            }
        }

        return $methods;
    }

    /**
     * Provide clear human-readable summary for a controller method.
     */
    protected function summarizeMethod(string $name, string $params, string $body): string
    {
        switch ($name) {
            case 'index':
                return 'Displays list view / datatable of records with filtering, searching, and pagination.';
            case 'add':
            case 'create':
                return 'Renders add / create form for new records or edit form when ID is supplied.';
            case 'store':
                return 'Validates input, creates new record with child items in database, and redirects with status.';
            case 'edit':
                return 'Renders edit form populated with existing record data.';
            case 'update':
                return 'Validates submitted modifications and updates the existing record in database.';
            case 'destroy':
            case 'delete':
                return 'Soft deletes or removes the record from the database after checking constraints.';
            case 'view':
            case 'view_details':
            case 'show':
                return 'Displays comprehensive details, line items, and audit history for the selected record.';
            case 'statusUpdate':
            case 'updateStatus':
            case 'changeStatus':
                return 'Handles workflow status changes (e.g. Draft, Approved, Dispatched, Received, Completed).';
            case 'ajax':
            case 'getDetails':
            case 'getItems':
                return 'Provides JSON response for dynamic dropdowns, dependent select boxes, or calculations.';
            case 'export':
            case 'exportExcel':
                return 'Exports filtered record data into Excel / CSV format.';
            case 'pdf':
            case 'print':
                return 'Generates printable PDF invoice, purchase order, or delivery challan document.';
            default:
                if (str_starts_with($name, 'get')) {
                    return 'Retrieves data for ' . Str::headline(substr($name, 3)) . '.';
                }
                return 'Performs ' . Str::headline($name) . ' action for this ERP feature.';
        }
    }

    /**
     * Extract key logic actions from method body.
     */
    protected function extractMethodActions(string $body): array
    {
        $actions = [];

        if (str_contains($body, 'DB::transaction') || str_contains($body, 'DB::beginTransaction')) {
            $actions[] = 'Executes database transaction to guarantee data integrity across parent and item tables.';
        }
        if (str_contains($body, 'unauthorizedRedirect') || str_contains($body, 'can(')) {
            $actions[] = 'Enforces role-based permission checks before granting access.';
        }
        if (preg_match('/view\([\'"]([^\'"]+)[\'"]\)/', $body, $vm)) {
            $actions[] = "Renders view template: `{$vm[1]}`.";
        }
        if (preg_match('/redirect\([\'"]([^\'"]+)[\'"]\)/', $body, $rm)) {
            $actions[] = "Redirects to: `{$rm[1]}`.";
        }
        if (str_contains($body, 'response()->json')) {
            $actions[] = 'Returns JSON response for asynchronous frontend operations.';
        }

        return $actions;
    }

    /**
     * Extract validation rules from controller code.
     */
    protected function extractValidationRules(string $content): array
    {
        $rules = [];

        // Match $rules = [ ... ];
        if (preg_match('/\$rules\s*=\s*\[(.*?)\];/s', $content, $m)) {
            $rulesText = $m[1];
            $lines = explode("\n", $rulesText);
            foreach ($lines as $line) {
                if (preg_match('/[\'"]([a-zA-Z0-9_\.\*]+)[\'"]\s*=>\s*(\[[^\]]+\]|[\'"][^\'"]+[\'"])/', $line, $rm)) {
                    $field = $rm[1];
                    $rule = trim($rm[2], "[]'\" ");
                    $rule = preg_replace('/\s+/', ' ', $rule);
                    $rules[$field] = $rule;
                }
            }
        }

        // Match $request->validate([ ... ]);
        if (preg_match('/\$request->validate\(\s*\[(.*?)\]\s*\);/s', $content, $m2)) {
            $rulesText = $m2[1];
            $lines = explode("\n", $rulesText);
            foreach ($lines as $line) {
                if (preg_match('/[\'"]([a-zA-Z0-9_\.\*]+)[\'"]\s*=>\s*(\[[^\]]+\]|[\'"][^\'"]+[\'"])/', $line, $rm)) {
                    $field = $rm[1];
                    $rule = trim($rm[2], "[]'\" ");
                    $rule = preg_replace('/\s+/', ' ', $rule);
                    if (!isset($rules[$field])) {
                        $rules[$field] = $rule;
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Extract status transitions and conditional business logic rules.
     */
    protected function extractStatusFlows(string $content): array
    {
        $flows = [];

        if (str_contains($content, 'Draft') && str_contains($content, 'Approved')) {
            $flows[] = 'Supports status workflow: **Draft** -> **Approved** (Dispatched / Received / Completed).';
        }
        if (str_contains($content, 'Only Draft') || str_contains($content, "status !== 'Draft'")) {
            $flows[] = 'Strict editing rule: Only **Draft** documents can be modified; approved or completed documents are locked.';
        }
        if (str_contains($content, 'po_self_close') || str_contains($content, 'is_self_closed')) {
            $flows[] = 'Allows authorized users to trigger **Self Close** on partial or completed orders.';
        }
        if (str_contains($content, 'commission') && str_contains($content, 'commission_agent')) {
            $flows[] = 'Automatically calculates agent commission percentage and amount on line items.';
        }
        if (str_contains($content, 'tax_amount') || str_contains($content, 'cgst') || str_contains($content, 'sgst') || str_contains($content, 'igst')) {
            $flows[] = 'Computes GST split: SGST + CGST for intra-state transactions, or IGST for inter-state transactions.';
        }

        return $flows;
    }

    protected function guessModule(string $className): string
    {
        $name = str_replace('Controller', '', $className);
        if (str_contains($name, 'Purchase')) return 'Purchase';
        if (str_contains($name, 'Sale') || str_contains($name, 'CreditNote')) return 'Sales';
        if (str_contains($name, 'Stock') || str_contains($name, 'GRN') || str_contains($name, 'Warehouse') || str_contains($name, 'Item')) return 'Store';
        if (str_contains($name, 'JobCard') || str_contains($name, 'Production')) return 'Production';
        if (str_contains($name, 'Employee') || str_contains($name, 'Attendance') || str_contains($name, 'Salary') || str_contains($name, 'Leave') || str_contains($name, 'Payroll')) return 'Emp. Payroll & Attendance';
        if (str_contains($name, 'Billing') || str_contains($name, 'Payment') || str_contains($name, 'DebitNote')) return 'Manage Payments';
        if (str_contains($name, 'Report')) return 'Reports';
        return 'Master';
    }

    protected function guessScreenName(string $className): string
    {
        $name = str_replace('Controller', '', $className);
        return Str::headline(Str::plural($name));
    }
}
