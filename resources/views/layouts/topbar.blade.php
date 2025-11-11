<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Layout container -->
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
            <div class="container-xxl">
                <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
                    <a href="{{ url('dashboard') }}" class="app-brand-link gap-2">
                        <span class="app-brand-text demo menu-text text-primary fw-bold ms-1">Nachias</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                        <i class="icon-base ri ri-close-line icon-sm"></i>
                    </a>
                </div>
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0  d-xl-none  ">
                    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                        <i class="icon-base ri ri-menu-line icon-md"></i>
                    </a>
                </div>
                <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown d-flex align-items-center gap-2 text-black">
                            Welcome
                            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                                <li>
                                    <a class="dropdown-item waves-effect" href="javascript:;">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="avatar avatar-online">
                                                    <img src="assets/images/user.jpg" alt="alt"
                                                        class="w-px-40 h-auto rounded-circle">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 small">John Doe</h6>
                                                <small class="text-body-secondary">Admin</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item waves-effect" href="{{ url('profile') }}">
                                        <i class="icon-base ri ri-user-3-line icon-22px me-2"></i>
                                        <span class="align-middle">My Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <div class="d-grid px-4 pt-2 pb-1">
                                        <a class="btn btn-primary d-flex waves-effect waves-light" href="{{ url('logout') }}">
                                            <small class="align-middle">Logout</small>
                                            <i class="icon-base ri ri-logout-box-r-line ms-2 icon-16px"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>
            </div>
        </nav>
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Menu -->
                <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0"
                    style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                    <div class="container-xxl d-flex h-100">
                        <a href="#" class="menu-horizontal-prev disabled"></a>
                        <div class="menu-horizontal-wrapper">
                            <ul class="menu-inner" style="margin-left: 0px;">
                                <!-- Dashboards -->
                                <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                                    <a href="{{ url('dashboard') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-home-smile-line"></i>
                                        <div>Dashboard</div>
                                    </a>
                                </li>
                                <!-- Employees -->
                                <li class="menu-item {{ request()->is('employees') || request()->is('add_employee') || request()->is('roles') || request()->is('add_role') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-user-add-line"></i>
                                        <div class="itm">Employees</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('roles') || request()->is('add_role') ? 'active' : '' }}">
                                            <a href="{{ url('roles') }}" class="menu-link">
                                                <div>Roles</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('employees') || request()->is('add_employee') ? 'active' : '' }}">
                                            <a href="{{ url('employees') }}" class="menu-link">
                                                <div>Employees</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Master -->
                                <li class="menu-item 
                                    {{ request()->is('countries') || request()->is('add_country') || request()->is('states') || request()->is('add_state') || request()->is('cities') || request()->is('add_city') || request()->is('places') || request()->is('add_place') || request()->is('uom') || request()->is('add_uom') || request()->is('operation_stages') || request()->is('add_operation_stage') || request()->is('zones') || request()->is('add_zone') || request()->is('size_ratio') || request()->is('add_size_ratio') || request()->is('fabric_type') || request()->is('add_fabric_type') || request()->is('charges') || request()->is('add_charge') || request()->is('store_location') || request()->is('add_store_location') || request()->is('departments') || request()->is('add_department') || request()->is('suppliers') || request()->is('add_supplier') || request()->is('view_supplier') || request()->is('customers') || request()->is('add_customer') || request()->is('view_customer') || request()->is('service_providers') || request()->is('add_service_provider') || request()->is('view_service_provider')  || request()->is('sales_agent') || request()->is('add_sales_agent') || request()->is('view_sales_agent')
                                    || request()->is('rmaterial_categories') || request()->is('add_rmaterial_category') || request()->is('rmaterials') || request()->is('add_rmaterial') || request()->is('item_categories') || request()->is('add_item_category') || request()->is('items') || request()->is('add_item') || request()->is('view_item') || request()->is('purchase_commission_agent') || request()->is('add_purchase_commission_agent') || request()->is('view_purchase_commission_agent') || request()->is('tax_types') || request()->is('add_tax_type') || request()->is('brands') || request()->is('add_brand') || request()->is('taxes') || request()->is('add_tax') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-layout-2-line"></i>
                                        <div>Master</div>
                                    </a>

                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('countries') || request()->is('add_country') ? 'active' : '' }}">
                                            <a href="{{ url('countries') }}" class="menu-link">
                                                <div>Countries</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('states') || request()->is('add_state') ? 'active' : '' }}">
                                            <a href="{{ url('states') }}" class="menu-link">
                                                <div>States</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('cities') || request()->is('add_city') ? 'active' : '' }}">
                                            <a href="{{ url('cities') }}" class="menu-link">
                                                <div>Cities</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('places') || request()->is('add_place') ? 'active' : '' }}">
                                            <a href="{{ url('places') }}" class="menu-link">
                                                <div>Places (Service Points)</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('uom') || request()->is('add_uom') ? 'active' : '' }}">
                                            <a href="{{ url('uom') }}" class="menu-link">
                                                <div>UOM</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('operation_stages') || request()->is('add_operation_stage') ? 'active' : '' }}">
                                            <a href="{{ url('operation_stages') }}" class="menu-link">
                                                <div>Operation Stages</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('zones') || request()->is('add_zone') ? 'active' : '' }}">
                                            <a href="{{ url('zones') }}" class="menu-link">
                                                <div>Zones</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('size_ratio') || request()->is('add_size_ratio') ? 'active' : '' }}">
                                            <a href="{{ url('size_ratio') }}" class="menu-link">
                                                <div>Size & Ratio</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('fabric_type') || request()->is('add_fabric_type') ? 'active' : '' }}">
                                            <a href="{{ url('fabric_type') }}" class="menu-link">
                                                <div>Fabric Type</div>
                                            </a>
                                        </li>

                                        <li class="menu-item {{ request()->is('charges') || request()->is('add_charge') ? 'active' : '' }}">
                                            <a href="{{ url('charges') }}" class="menu-link">
                                                <div>Charges</div>
                                            </a>
                                        </li>

                                        <li class="menu-item {{ request()->is('store_location') || request()->is('add_store_location') ? 'active' : '' }}">
                                            <a href="{{ url('store_location') }}" class="menu-link">
                                                <div>Store Location</div>
                                            </a>
                                        </li> 
                                        <li class="menu-item {{ request()->is('departments') || request()->is('add_department') ? 'active' : '' }}">
                                            <a href="{{ url('departments') }}" class="menu-link">
                                                <div>Departments</div>
                                            </a>
                                        </li>
                                        {{-- <li class="menu-item {{ request()->is('tax_types') || request()->is('add_tax_type') ? 'active' : '' }}">
                                            <a href="{{ url('tax_types') }}" class="menu-link">
                                                <div>Tax Types</div>
                                            </a>
                                        </li> --}}
                                        <li class="menu-item {{ request()->is('taxes') || request()->is('add_tax') ? 'active' : '' }}">
                                            <a href="{{ url('taxes') }}" class="menu-link">
                                                <div>Taxes</div>
                                            </a>
                                        </li>
                                        <!-- Parties -->
                                        <li class="menu-item 
                                            {{ request()->is('customers') || request()->is('add_customer') || request()->is('view_customer') 
                                            || request()->is('suppliers') || request()->is('add_supplier') || request()->is('view_supplier') || request()->is('service_providers') || request()->is('add_service_provider') || request()->is('view_service_provider') 
                                            || request()->is('sales_agent') || request()->is('add_sales_agent') || request()->is('view_sales_agent') || request()->is('purchase_commission_agent') || request()->is('add_purchase_commission_agent') || request()->is('view_purchase_commission_agent')
                                            ? 'active' : '' }}">
                                            
                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <div>Parties</div>
                                            </a>

                                            <ul class="menu-sub">
                                                <li class="menu-item {{ request()->is('customers') || request()->is('add_customer') || request()->is('view_customer') ? 'active' : '' }}">
                                                    <a href="{{ url('customers') }}" class="menu-link">
                                                        <div>Customers</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('suppliers') || request()->is('add_supplier') || request()->is('view_supplier') ? 'active' : '' }}">
                                                    <a href="{{ url('suppliers') }}" class="menu-link">
                                                        <div>Suppliers</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('service_providers') || request()->is('add_service_provider') || request()->is('view_service_provider') ? 'active' : '' }}">
                                                    <a href="{{ url('service_providers') }}" class="menu-link">
                                                        <div>Service Providers</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('sales_agent') || request()->is('add_sales_agent') || request()->is('view_sales_agent') ? 'active' : '' }}">
                                                    <a href="{{ url('sales_agent') }}" class="menu-link">
                                                        <div>Sales Agent</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('purchase_commission_agent') || request()->is('add_purchase_commission_agent') || request()->is('view_purchase_commission_agent') ? 'active' : '' }}">
                                                    <a href="{{ url('purchase_commission_agent') }}" class="menu-link">
                                                        <div>Purchase Commission Agent</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- Item Setup -->
                                        <li class="menu-item {{ request()->is('rmaterial_categories') || request()->is('add_rmaterial_category') || request()->is('rmaterials') || request()->is('add_rmaterial') || request()->is('item_categories') || request()->is('add_item_category') || request()->is('items') || request()->is('add_item') || request()->is('view_item')  || request()->is('brands') || request()->is('add_brand') ? 'active' : '' }}">
                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <div>Item Setup</div>
                                            </a>
                                            <ul class="menu-sub">
                                                <li class="menu-item {{ request()->is('rmaterial_categories') || request()->is('add_rmaterial_category') ? 'active' : '' }}">
                                                    <a href="{{ url('rmaterial_categories') }}" class="menu-link">
                                                        <div>Store Category</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('rmaterials') || request()->is('add_rmaterial') ? 'active' : '' }}">
                                                    <a href="{{ url('rmaterials') }}" class="menu-link">
                                                        <div>Raw Materials</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('item_categories') || request()->is('add_item_category') ? 'active' : '' }}">
                                                    <a href="{{ url('item_categories') }}" class="menu-link">
                                                        <div>Brand Category</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('brands') || request()->is('add_brand') ? 'active' : '' }}">
                                                    <a href="{{ url('brands') }}" class="menu-link">
                                                        <div>Brands</div>
                                                    </a>
                                                </li>
                                                <li class="menu-item {{ request()->is('items') || request()->is('add_item') || request()->is('view_item') ? 'active' : '' }}">
                                                    <a href="{{ url('items') }}" class="menu-link">
                                                        <div>Items</div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Purchase Order -->
                                <li class="menu-item {{ request()->is('purchase_orders') || request()->is('add_purchase_order') || request()->is('view_purchase_order') || request()->is('purchase_invoices') || request()->is('add_purchase_invoice') || request()->is('view_purchase_invoice') || request()->is('debit_notes') || request()->is('add_debit_note') || request()->is('view_debit_note') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                                        <div>Purchase</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('purchase_orders') || request()->is('add_purchase_order') || request()->is('view_purchase_order') ? 'active' : '' }}">
                                            <a href="{{ url('purchase_orders') }}" class="menu-link">
                                                <div>Purchase Order</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('purchase_invoices') || request()->is('add_purchase_invoice') || request()->is('view_purchase_invoice') ? 'active' : '' }}">
                                            <a href="{{ url('purchase_invoices') }}" class="menu-link">
                                                <div>Purchase Invoice</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('debit_notes') || request()->is('add_debit_note') || request()->is('view_debit_note') ? 'active' : '' }}">
                                            <a href="{{ url('debit_notes') }}" class="menu-link">
                                                <div>Debit Notes</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Store -->
                                <li class="menu-item {{ request()->is('grn_entries') || request()->is('add_grn_entry') || request()->is('view_grn_entry') || request()->is('stock_entries') || request()->is('add_stock_entry')|| request()->is('view_stock_entry') || request()->is('stock_consumables_returns') || request()->is('add_stock_consumables_return') || request()->is('view_stock_consumables_return') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-shopping-bag-line"></i>
                                        <div>Store</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('grn_entries') || request()->is('add_grn_entry') || request()->is('view_grn_entry') ? 'active' : '' }}">
                                            <a href="{{ url('grn_entries') }}" class="menu-link">
                                                <div>GRN Entry</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('stock_entries') || request()->is('add_stock_entry') || request()->is('view_stock_entry') ? 'active' : '' }}">
                                            <a href="{{ url('stock_entries') }}" class="menu-link">
                                                <div>Stock Entry</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('stock_consumables_returns') || request()->is('add_stock_consumables_return') || request()->is('view_stock_consumables_return') ? 'active' : '' }}">
                                            <a href="{{ url('stock_consumables_returns') }}" class="menu-link">
                                                <div>Stock Consumables & Returns Management</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Production -->
                                <li class="menu-item {{ request()->is('job_card_entries') || request()->is('add_job_card_entry') || request()->is('view_job_card_entry') || request()->is('view_jc_item') || request()->is('productions') || request()->is('add_production') || request()->is('view_production')  ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-inbox-line"></i>
                                        <div>Production</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('job_card_entries') || request()->is('add_job_card_entry') || request()->is('view_job_card_entry') || request()->is('view_jc_item') ? 'active' : '' }}">
                                            <a href="{{ url('job_card_entries') }}" class="menu-link">
                                                <div>Job Card  Entry</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('productions') || request()->is('add_production') || request()->is('view_production') ? 'active' : '' }}">
                                            <a href="{{ url('productions') }}" class="menu-link">
                                                <div>Production</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Task Management -->
                                <li class="menu-item {{ request()->is('task_creation') || request()->is('add_task_creation') || request()->is('view_task_creation') || request()->is('task_assignment') || request()->is('add_task_assignment') || request()->is('view_task_assignment') || request()->is('task_tracking_monitoring') || request()->is('add_task_tracking_monitoring')  || request()->is('view_task_tracking_monitoring')|| request()->is('task_status_updates') || request()->is('add_task_status_update') || request()->is('view_task_status_update') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-task-line"></i>
                                        <div>Task Management</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('task_creation') || request()->is('add_task_creation') || request()->is('view_task_creation') ? 'active' : '' }}">
                                            <a href="{{ url('task_creation') }}" class="menu-link">
                                                <div>Task Creation</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('task_assignment') || request()->is('add_task_assignment') || request()->is('view_task_assignment') ? 'active' : '' }}">
                                            <a href="{{ url('task_assignment') }}" class="menu-link">
                                                <div>Task Assignment</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('task_tracking_monitoring') || request()->is('add_task_tracking_monitoring') || request()->is('view_task_tracking_monitoring') ? 'active' : '' }}">
                                            <a href="{{ url('task_tracking_monitoring') }}" class="menu-link">
                                                <div>Task Tracking & Monitoring</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('task_status_updates') || request()->is('add_task_status_update') || request()->is('view_task_status_update') ? 'active' : '' }}">
                                            <a href="{{ url('task_status_updates') }}" class="menu-link">
                                                <div>Task Status Updates</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Sales Order -->
                                <li class="menu-item {{ request()->is('sales_order') || request()->is('add_sale_order') || request()->is('view_sale_order') || request()->is('sales_invoice') || request()->is('add_sale_invoice') || request()->is('view_sale_invoice') || request()->is('credit_notes') || request()->is('add_credit_note') || request()->is('view_credit_note') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-order-play-line"></i>
                                        <div>Sales</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('sales_order') || request()->is('add_sale_order') ||  request()->is('view_sale_order') ? 'active' : '' }}">
                                            <a href="{{ url('sales_order') }}" class="menu-link">
                                                <div>Sales Order</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('sales_invoice') || request()->is('add_sale_invoice') || request()->is('view_sale_invoice') ? 'active' : '' }}">
                                            <a href="{{ url('sales_invoice') }}" class="menu-link">
                                                <div>Sales Invoice</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('credit_notes') || request()->is('add_credit_note') || request()->is('view_credit_notes') ? 'active' : '' }}">
                                            <a href="{{ url('credit_notes') }}" class="menu-link">
                                                <div>Credit Notes</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Billing -->
                                <li class="menu-item {{ request()->is('billing') || request()->is('add_billing') || request()->is('view_billing') ? 'active' : '' }}">
                                    <a href="{{ url('billing') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                                        <div>Billing</div>
                                    </a>
                                </li>
                                <!-- Manage Payments -->
                                <li class="menu-item {{ request()->is('payments') || request()->is('add_payment') ||request()->is('view_payment') ? 'active' : '' }}">
                                    <a href="{{ url('payments') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-bank-card-line"></i>
                                        <div>Manage Payments</div>
                                    </a>
                                </li>
                                <!-- Emp. Payroll & Attendance  -->
                                <li class="menu-item {{ request()->is('attendances') || request()->is('add_attendance') || request()->is('view_attendance') || request()->is('leave') || request()->is('add_leave') || request()->is('view_leave') || request()->is('overtime') || request()->is('add_overtime') || request()->is('view_overtime') || request()->is('salary_calculation') || request()->is('add_salary_calculation') || request()->is('view_salary_calculation') || request()->is('payslip') || request()->is('add_payslip') || request()->is('view_payslip') || request()->is('payroll_reports') || request()->is('add_payroll_report')? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-money-rupee-circle-line"></i>
                                        <div>Emp. Payroll & Attendance</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('attendances') || request()->is('add_attendance') || request()->is('view_attendance') ? 'active' : '' }}">
                                            <a href="{{ url('attendances') }}" class="menu-link">
                                                <div>Attendances</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('leave') || request()->is('add_leave') || request()->is('view_leave') ? 'active' : '' }}">
                                            <a href="{{ url('leave') }}" class="menu-link">
                                                <div>Manage Leaves</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('overtime') || request()->is('add_overtime') || request()->is('view_overtime') ? 'active' : '' }}">
                                            <a href="{{ url('overtime') }}" class="menu-link">
                                                <div>Overtime / Bouns</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('salary_calculation') || request()->is('add_salary_calculation') || request()->is('view_salary_calculation') ? 'active' : '' }}">
                                            <a href="{{ url('salary_calculation') }}" class="menu-link">
                                                <div>Salary Calculation</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('payslip') || request()->is('add_payslip') || request()->is('view_payslip') ? 'active' : '' }}">
                                            <a href="{{ url('payslip') }}" class="menu-link">
                                                <div>Payslip Generation</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('payroll_reports') || request()->is('add_payroll_report') ? 'active' : '' }}">
                                            <a href="{{ url('payroll_reports') }}" class="menu-link">
                                                <div>Payroll Reports</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- System Utility -->
                                <li class="menu-item {{ request()->is('document_repository') || request()->is('add_document_repository')||request()->is('view_document_repository') || request()->is('logs') || request()->is('backup_restore') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-database-2-line"></i>
                                        <div>System Utility</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('document_repository') || request()->is('add_document_repository')||request()->is('view_document_repository') ? 'active' : '' }}">
                                            <a href="{{ url('document_repository') }}" class="menu-link">
                                                <div>Document Repository</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('logs') ? 'active' : '' }}">
                                            <a href="{{ url('logs') }}" class="menu-link">
                                                <div>Logs & Audit Log</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('backup_restore') ? 'active' : '' }}">
                                            <a href="{{ url('backup_restore') }}" class="menu-link">
                                                <div>Backup & Restore</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Reports -->
                                <li class="menu-item {{ request()->is('customer_reports') || request()->is('sale_reports') || request()->is('stock_reports') || request()->is('daily_production_reports') || request()->is('order_reports') || request()->is('employee_reports') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-file-chart-line"></i>
                                        <div>Reports</div>
                                    </a>
                                    <ul class="menu-sub">
                                        <li class="menu-item {{ request()->is('customer_reports') ? 'active' : '' }}">
                                            <a href="{{ url('customer_reports') }}" class="menu-link">
                                                <div>Customer Report</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('sale_reports') ? 'active' : '' }}">
                                            <a href="{{ url('sale_reports') }}" class="menu-link">
                                                <div>Sale Report</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('stock_reports') ? 'active' : '' }}">
                                            <a href="{{ url('stock_reports') }}" class="menu-link">
                                                <div>Stock Report</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('daily_production_reports') ? 'active' : '' }}">
                                            <a href="{{ url('daily_production_reports') }}" class="menu-link">
                                                <div>Daily Production Report</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('order_reports') ? 'active' : '' }}">
                                            <a href="{{ url('order_reports') }}" class="menu-link">
                                                <div>Order Report</div>
                                            </a>
                                        </li>
                                        <li class="menu-item {{ request()->is('employee_reports') ? 'active' : '' }}">
                                            <a href="{{ url('employee_reports') }}" class="menu-link">
                                                <div>Employee Report</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Settings -->
                                <li class="menu-item {{ request()->is('settings') ? 'active' : '' }}">
                                    <a href="{{ url('settings') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-settings-5-line"></i>
                                        <div>Settings</div>
                                    </a>
                                </li>
                            </ul>
                        </div><a href="#" class="menu-horizontal-next"></a>
                    </div>
                </aside>