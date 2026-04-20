<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $modules = [

            /* Employee Module */
            'roles' => ['create', 'edit', 'delete', 'view'],
            'employees' => ['create', 'edit', 'delete', 'view','view_details'],
            
            /* Master */
            'states' => ['create', 'edit', 'delete', 'view'],
            'cities' => ['create', 'edit', 'delete', 'view'],
            'service-points' => ['create', 'edit', 'delete', 'view'],
            'uoms' => ['create', 'edit', 'delete', 'view'],
            'colors' => ['create', 'edit', 'delete', 'view'],
            'operation-stages' => ['create', 'edit', 'delete', 'view'],
            'zones' => ['create', 'edit', 'delete', 'view'],
            'size-ratio' => ['create', 'edit', 'delete', 'view'],
            'fabric-type' => ['create', 'edit', 'delete', 'view'],
            'fabric-sizes' => ['create', 'edit', 'delete', 'view'],
            'charges' => ['create', 'edit', 'delete', 'view'],
            'store-location' => ['create', 'edit', 'delete', 'view'],
            'departments' => ['create', 'edit', 'delete', 'view'],
            'taxes' => ['create', 'edit', 'delete', 'view'],
            'styles' => ['create', 'edit', 'delete', 'view'],
            'stores' => ['create', 'edit', 'delete', 'view'],

            /* Logistics Master */
            'shipping-methods' => ['create', 'edit', 'delete', 'view'],
            'transport-mode' => ['create', 'edit', 'delete', 'view'],

            /* Tailoring Specification Master */
            'fits'  => ['create','edit','delete','view'],
            'patti-types'  => ['create','edit','delete','view'],
            'collar-types'  => ['create','edit','delete','view'],
            'cuff-types'  => ['create','edit','delete','view'],
            'pocket-types'  => ['create','edit','delete','view'],
            'bottom-cuts'  => ['create','edit','delete','view'],
            'process-groups'  => ['create','edit','delete','view'],
            'seasons'  => ['create','edit','delete','view'],

            /* Production Master */
            'shifts'  => ['create','edit','delete','view'],
            'production-services'  => ['create','edit','delete','view'],

            /* Parties */
            'customers' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'suppliers' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'service-providers' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'sales-agents' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'purchase-commission-agent' => ['create', 'edit', 'delete', 'view', 'view_details'],

            /* Item Setup */
            'store-categories' => ['create', 'edit', 'delete', 'view'],
            'raw-materials' => ['create', 'edit', 'delete', 'view'],
            'brand-categories' => ['create', 'edit', 'delete', 'view'],
            'brands' => ['create', 'edit', 'delete', 'view'],
            'items' => ['create', 'edit', 'delete', 'view'],

            /* Purchase */
            'purchase-order' => ['create', 'edit', 'view', 'view_details'],
            'purchase-invoice' => ['create', 'edit', 'view','view_details'],

            /* Store */
            'grn-entry' => ['create', 'edit', 'view','view_details'],
            'stock-entry' => ['create', 'edit', 'view', 'stock_adjustment','stock_adjustment_logs'],
            'debit-notes' => ['create', 'edit', 'view','view_details'],
            'stock-consumable-return' => ['view', 'view_details'],

            /* Production */
            'job-card' => ['create', 'edit', 'view', 'view_details', 'fabric-consumption-pdf', 'work-order-pdf', 'issue-item'],
            'task-management' => ['edit', 'view', 'view_details'],
            'production-receipts' => ['create', 'edit', 'view'],

            /* Sales */
            'sales-order' => ['create', 'edit', 'delete', 'view'],
            'sales-invoice' => ['create', 'edit', 'delete', 'view'],
            'credit-notes' => ['create', 'edit', 'delete', 'view'],

            /* Accounts */
            'billing' => ['create', 'edit', 'view'],
            'manage-payments' => ['create', 'edit', 'delete', 'view'],

            /* Payroll */
            'attendance' => ['create', 'edit', 'delete', 'view'],
            'manage-leaves' => ['create', 'edit', 'delete', 'view'],
            'overtime-bonus' => ['create', 'edit', 'delete', 'view'],
            'salary-calculation' => ['create', 'edit', 'delete', 'view'],
            'payslip-generation' => ['create', 'edit', 'delete', 'view'],
            'payroll-reports' => ['create', 'edit', 'delete', 'view'],

            /* System  Utilities */
            'document-repository' => ['create', 'edit', 'delete', 'view'],
            'log' => ['view'],
            'backup-restore' => ['view'],

            /* Reports */
            'sales-marketing-report' => ['view'],
            'warehouse-report' => ['view'],
            'production-report' => ['view'],

            /* Ticket Management */
            'ticket-management' => ['create', 'edit', 'delete', 'view'],

            /* Settings */
            'settings' => ['edit','view'],

            'dashboard' => [],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    [
                        'name'       => $action . ' ' . $module,
                        'guard_name' => 'web',
                    ],
                    [
                        'module' => $module,
                        'action' => $action,
                        'label'  => ucfirst($action) . ' ' . ucwords(str_replace('-', ' ', $module)),
                    ]
                );
            }
        }
        $dashboardPerms = [ 
            ['action' => 'view-sales-order',       'label' => 'Sales & Order Dashboard'],
            ['action' => 'view-accounts-financial', 'label' => 'Accounts & Financial Dashboard'],
            ['action' => 'view-production',         'label' => 'Production Dashboard'],
            ['action' => 'view-maintenance',         'label' => 'Maintenance Dashboard'],
        ];
        foreach ($dashboardPerms as $dp) {
            Permission::firstOrCreate(
                ['name' => $dp['action'] . ' dashboard', 'guard_name' => 'web'],
                ['module' => 'dashboard', 'action' => $dp['action'], 'label' => $dp['label']]
            );
        }

        $this->command->info('✅ All permissions seeded successfully!');
    }
}
