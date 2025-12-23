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
            'dashboard' => ['view'],

            'roles' => ['create', 'edit', 'delete', 'view'],
            'employee' => ['create', 'edit', 'delete', 'view'],

            'states' => ['create', 'edit', 'delete', 'view'],
            'cities' => ['create', 'edit', 'delete', 'view'],
            'service-points' => ['create', 'edit', 'delete', 'view'],
            'uoms' => ['create', 'edit', 'delete', 'view'],
            'operation-stages' => ['create', 'edit', 'delete', 'view'],
            'zones' => ['create', 'edit', 'delete', 'view'],
            'size-ratio' => ['create', 'edit', 'delete', 'view'],
            'fabric-type' => ['create', 'edit', 'delete', 'view'],
            'charges' => ['create', 'edit', 'delete', 'view'],
            'store-location' => ['create', 'edit', 'delete', 'view'],

            'departments' => ['create', 'edit', 'delete', 'view'],
            'taxes' => ['create', 'edit', 'delete', 'view'],
            'customers' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'suppliers' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'service-providers' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'sales-agents' => ['create', 'edit', 'delete', 'view', 'view_details'],
            'purchase-commission-agent' => ['create', 'edit', 'delete', 'view', 'view_details'],

            'store-categories' => ['create', 'edit', 'delete', 'view'],
            'raw-materials' => ['create', 'edit', 'delete', 'view'],
            'brand-categories' => ['create', 'edit', 'delete', 'view'],
            'brands' => ['create', 'edit', 'delete', 'view'],
            'items' => ['create', 'edit', 'delete', 'view', 'view_details'],

            'purchase-order' => ['create', 'edit', 'delete', 'view'],
            'purchase-invoice' => ['create', 'edit', 'delete', 'view'],
            'debit-notes' => ['create', 'edit', 'delete', 'view'],
            'grn-entry' => ['create', 'edit', 'delete', 'view'],
            'stock-entry' => ['create', 'edit', 'delete', 'view'],
            'stock-consumable-return' => ['create', 'edit', 'delete', 'view'],

            'sales-order' => ['create', 'edit', 'delete', 'view'],
            'sales-invoice' => ['create', 'edit', 'delete', 'view'],
            'credit-notes' => ['create', 'edit', 'delete', 'view'],

            'job-card' => ['create', 'edit', 'delete', 'view'],
            'production' => ['create', 'edit', 'delete', 'view'],

            'task-creation' => ['create', 'edit', 'delete', 'view'],
            'task-assignment' => ['create', 'edit', 'delete', 'view'],
            'task-tracking-monitoring' => ['create', 'edit', 'delete', 'view'],
            'task-status-updates' => ['create', 'edit', 'delete', 'view'],

            'billing' => ['create', 'edit', 'delete', 'view'],
            'manage-payments' => ['create', 'edit', 'delete', 'view'],

            'attendance' => ['create', 'edit', 'delete', 'view'],
            'manage-leaves' => ['create', 'edit', 'delete', 'view'],
            'overtime-bonus' => ['create', 'edit', 'delete', 'view'],
            'salary-calculation' => ['create', 'edit', 'delete', 'view'],
            'payslip-generation' => ['create', 'edit', 'delete', 'view'],
            'payroll-reports' => ['create', 'edit', 'delete', 'view'],

            'document-repository' => ['create', 'edit', 'delete', 'view'],

            'log' => ['view'],
            'backup-restore' => ['view'],

            'customer-report' => ['view'],
            'sale-report' => ['view'],
            'stock-report' => ['view'],
            'daily-production-report' => ['view'],
            'order-report' => ['view'],
            'employee-report' => ['view'],


            'settings' => ['edit','view'],
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

        $this->command->info('✅ All permissions seeded successfully!');
    }
}
