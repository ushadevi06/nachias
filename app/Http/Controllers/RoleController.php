<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('roles/view');
    }
    public function add()
    {
        $modules = [
            'Dashboard' => ['Dashboard'],

            'Employees' => [
                'Employees',
                'Roles',
            ],

            'Master' => [
                'Countries',
                'States',
                'Cities',
                'Places (Service Points)',
                'UOM',
                'Zones',
                'Size & Ratio',
                'Fabric Type',
                'Charges',
                'Store Location',
                'Customers',
                'Suppliers',
                'Service Providers',
                'Sales Agent',
                'Purchase Commission Agent',
                'Raw Materials',
                'Items',
            ],

            'Purchase' => [
                'Purchase Order',
                'Purchase Invoice',
                'Debit Notes',
            ],

            'Store' => [
                'GRN Entry',
                'Stock Entry',
                'Stock Consumables & Returns Management',
            ],

            'Production' => [
                'Job Card Entry',
                'Production Entry',
            ],

            'Task Management' => [
                'Task Creation',
                'Task Assignment',
                'Task Tracking & Monitoring',
                'Task Status Updates',
            ],

            'Sales' => [
                'Sales Order',
                'Sales Invoice',
                'Credit Notes',
            ],

            'Billing' => ['Billing'],

            'Manage Payments' => ['Manage Payments'],

            'Emp. Payroll & Attendance' => [
                'Attendances',
                'Leaves',
                'Overtime / Bonus',
                'Salary Calculation',
                'Payslip Generation',
                'Payroll Reports',
            ],

            'System Utility' => [
                'Document Repository',
                'Logs & Audit Log',
                'Backup & Restore',
            ],

            'Reports' => [
                'Customer Report',
                'Sale Report',
                'Stock Report',
                'Daily Production Report',
                'Order Report',
                'Employee Report',
            ],

            'Settings' => ['Settings'],
        ];

        return view('roles.add_role', compact('modules'));
    }

}
