@php
$user = auth()->user();
$isSuper = $user->id == 1;
@endphp
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
                        <li
                            class="nav-item navbar-dropdown dropdown-user dropdown d-flex align-items-center gap-2 text-black">
                            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                                <li>
                                    <a class="dropdown-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="avatar avatar-online">
                                                    <img src="assets/images/user.jpg" alt="alt"
                                                        class="w-px-40 h-auto rounded-circle">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 small">{{ auth()->user()->name }}</h6>
                                                <small class="text-body-secondary">{{ auth()->user()->getRoleNames()->first() ?? 'Administrator' }}</small>
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
                                        <a class="btn btn-primary d-flex waves-effect waves-light"
                                            href="{{ url('logout') }}">
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

                                @if($user && ($isSuper || $user->can('view dashboard')))
                                <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                                    <a href="{{ url('dashboard') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-home-smile-line"></i>
                                        <div>Dashboard</div>
                                    </a>
                                </li>
                                @endif
                                <!-- Employees -->
                                @if($user && ($isSuper || $user->can('view roles') || $user->can('view employee')))
                                <li class="menu-item {{ request()->is('employees*') || request()->is('roles*') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-user-add-line"></i>
                                        <div class="itm">Employees</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view roles'))
                                        <li class="menu-item {{ request()->is('roles*') ? 'active' : '' }}">
                                            <a href="{{ url('roles') }}" class="menu-link">
                                                <div>Roles</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view employee'))
                                        <li class="menu-item {{ request()->is('employees*') ? 'active' : '' }}">
                                            <a href="{{ url('employees') }}" class="menu-link">
                                                <div>Employees</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                <!-- Master -->
                                @if($user && ($isSuper || $user->can('view states') || $user->can('view cities') || $user->can('view service-points') || $user->can('view uoms') || $user->can('view operation-stages') || $user->can('view zones') || $user->can('view size-ratio') || $user->can('view fabric-type') || $user->can('view charges') || $user->can('view store-location') || $user->can('view departments') || $user->can('view taxes') || $user->can('view customers') || $user->can('view suppliers') || $user->can('view service providers') ||
                                $user->can('view sales-agents') || $user->can('view purchase-commission-agent') || $user->can('view store categories') || $user->can('view raw materials') || $user->can('view brand categories') || $user->can('view brands') || $user->can('view items')))
                                <li class="menu-item {{ (request()->is('states*') || request()->is('cities*') || request()->is('places*') || request()->is('uoms*') || request()->is('operation_stages*') || request()->is('zones*') || request()->is('size_ratio*') || request()->is('fabric_type*') || request()->is('charges*') || request()->is('store_location*') || request()->is('departments*') || request()->is('taxes*') || request()->is('customers*') || request()->is('suppliers*') || request()->is('service_providers*') || request()->is('sales_agents*') || request()->is('purchase_commission_agent*') || request()->is('store_categories*') || request()->is('raw_materials*') || request()->is('brand_categories*') || request()->is('brands*') || request()->is('items*')) ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-layout-2-line"></i>
                                        <div>Master</div>
                                    </a>

                                    <ul class="menu-sub">

                                        @if($isSuper || $user->can('view states'))
                                        <li class="menu-item {{ request()->is('states*') ? 'active' : '' }}">
                                            <a href="{{ url('states') }}" class="menu-link">
                                                <div>States</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view cities'))
                                        <li class="menu-item {{ request()->is('cities*') ? 'active' : '' }}">
                                            <a href="{{ url('cities') }}" class="menu-link">
                                                <div>Cities</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view service-points'))
                                        <li class="menu-item {{ request()->is('places*') ? 'active' : '' }}">
                                            <a href="{{ url('places') }}" class="menu-link">
                                                <div>Places (Service Points)</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view uoms'))
                                        <li class="menu-item {{ request()->is('uoms*') ? 'active' : '' }}">
                                            <a href="{{ url('uoms') }}" class="menu-link">
                                                <div>UOM</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view operation-stages'))
                                        <li class="menu-item {{ request()->is('operation_stages*') ? 'active' : '' }}">
                                            <a href="{{ url('operation_stages') }}" class="menu-link">
                                                <div>Operation Stages</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view zones'))
                                        <li class="menu-item {{ request()->is('zones*') ? 'active' : '' }}">
                                            <a href="{{ url('zones') }}" class="menu-link">
                                                <div>Zones</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view size-ratio'))
                                        <li class="menu-item {{ request()->is('size_ratio*') ? 'active' : '' }}">
                                            <a href="{{ url('size_ratio') }}" class="menu-link">
                                                <div>Size & Ratio</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view fabric-type'))
                                        <li class="menu-item {{ request()->is('fabric_type*') ? 'active' : '' }}">
                                            <a href="{{ url('fabric_type') }}" class="menu-link">
                                                <div>Fabric Type</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view charges'))
                                        <li class="menu-item {{ request()->is('charges*') ? 'active' : '' }}">
                                            <a href="{{ url('charges') }}" class="menu-link">
                                                <div>Charges</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view store-location'))
                                        <li class="menu-item {{ request()->is('store_location*') ? 'active' : '' }}">
                                            <a href="{{ url('store_location') }}" class="menu-link">
                                                <div>Store Location</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view departments'))
                                        <li class="menu-item {{ request()->is('departments*') ? 'active' : '' }}">
                                            <a href="{{ url('departments') }}" class="menu-link">
                                                <div>Departments</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view taxes'))
                                        <li class="menu-item {{ request()->is('taxes*') ? 'active' : '' }}">
                                            <a href="{{ url('taxes') }}" class="menu-link">
                                                <div>Taxes</div>
                                            </a>
                                        </li>
                                        @endif

                                        <!-- Parties -->
                                        @if($isSuper || $user->can('view customers') || $user->can('view suppliers') || $user->can('view service-providers') || $user->can('view sales agents') || $user->can('view purchase commission agents'))

                                        <li class="menu-item {{ (request()->is('customers*') || request()->is('suppliers*') || request()->is('service_providers*') || request()->is('sales_agents*') || request()->is('purchase_commission_agent*')) ? 'active' : '' }}">

                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <div>Parties</div>
                                            </a>

                                            <ul class="menu-sub">
                                                @if($isSuper || $user->can('view customers'))
                                                <li class="menu-item {{ request()->is('customers*') ? 'active' : '' }}">
                                                    <a href="{{ url('customers') }}" class="menu-link">
                                                        <div>Customers</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view suppliers'))
                                                <li class="menu-item {{ request()->is('suppliers*') ? 'active' : '' }}">
                                                    <a href="{{ url('suppliers') }}" class="menu-link">
                                                        <div>Suppliers</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view service-providers'))
                                                <li class="menu-item {{ request()->is('service_providers*') ? 'active' : '' }}">
                                                    <a href="{{ url('service_providers') }}" class="menu-link">
                                                        <div>Service Providers</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view sales-agents'))
                                                <li class="menu-item {{ request()->is('sales_agents*') ? 'active' : '' }}">
                                                    <a href="{{ url('sales_agents') }}" class="menu-link">
                                                        <div>Sales Agent</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view purchase-commission-agent'))
                                                <li class="menu-item {{ request()->is('purchase_commission_agent*') ? 'active' : '' }}">
                                                    <a href="{{ url('purchase_commission_agent') }}" class="menu-link">
                                                        <div>Purchase Commission Agent</div>
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </li>
                                        @endif

                                        <!-- Item Setup -->
                                        @if($isSuper || $user->can('view store-categories') || $user->can('view raw-materials') || $user->can('view brand-categories') || $user->can('view brands') || $user->can('view items'))

                                        <li class="menu-item {{ (request()->is('store_categories*') || request()->is('raw_materials*') || request()->is('brand_categories*') || request()->is('brands*') || request()->is('items*')) ? 'active' : '' }}">
                                            <a href="javascript:void(0)" class="menu-link menu-toggle">
                                                <div>Item Setup</div>
                                            </a>

                                            <ul class="menu-sub">
                                                @if($isSuper || $user->can('view store-categories'))
                                                <li class="menu-item {{ request()->is('store_categories*') ? 'active' : '' }}">
                                                    <a href="{{ url('store_categories') }}" class="menu-link">
                                                        <div>Store Category</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view raw-materials'))
                                                <li class="menu-item {{ request()->is('raw_materials*') ? 'active' : '' }}">
                                                    <a href="{{ url('raw_materials') }}" class="menu-link">
                                                        <div>Raw Materials</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view brand-categories'))
                                                <li class="menu-item {{ request()->is('brand_categories*') ? 'active' : '' }}">
                                                    <a href="{{ url('brand_categories') }}" class="menu-link">
                                                        <div>Brand Category</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view brands'))
                                                <li class="menu-item {{ request()->is('brands*') ? 'active' : '' }}">
                                                    <a href="{{ url('brands') }}" class="menu-link">
                                                        <div>Brands</div>
                                                    </a>
                                                </li>
                                                @endif

                                                @if($isSuper || $user->can('view items'))
                                                <li class="menu-item {{ request()->is('items*') ? 'active' : '' }}">
                                                    <a href="{{ url('items') }}" class="menu-link">
                                                        <div>Items</div>
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </li>
                                        @endif

                                    </ul>
                                </li>
                                @endif

                                <!-- Purchase -->
                                @if($user && ($isSuper || $user->can('view purchase-order') || $user->can('view purchase-invoice') || $user->can('view debit-notes')))
                                <li class="menu-item {{ (request()->is('purchase_orders*') || request()->is('purchase_invoices*') || request()->is('debit_notes*')) ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                                        <div>Purchase</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view purchase-order'))
                                        <li class="menu-item {{ request()->is('purchase_orders*') ? 'active' : '' }}">
                                            <a href="{{ url('purchase_orders') }}" class="menu-link">
                                                <div>Purchase Order</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view purchase-invoice'))
                                        <li class="menu-item {{ request()->is('purchase_invoices*') ? 'active' : '' }}">
                                            <a href="{{ url('purchase_invoices') }}" class="menu-link">
                                                <div>Purchase Invoice</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view debit-notes'))
                                        <li class="menu-item {{ request()->is('debit_notes*') ? 'active' : '' }}">
                                            <a href="{{ url('debit_notes') }}" class="menu-link">
                                                <div>Debit Notes</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                <!-- Store -->
                                @if($user && ($isSuper || $user->can('view grn-entry') || $user->can('view stock-entry') || $user->can('view stock-consumable-return')))

                                <li class="menu-item {{ (request()->is('grn_entries*') || request()->is('stock_entries*') || request()->is('stock_consumables_returns*')) ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-shopping-bag-line"></i>
                                        <div>Store</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view grn-entry'))
                                        <li class="menu-item {{ request()->is('grn_entries*') ? 'active' : '' }}">
                                            <a href="{{ url('grn_entries') }}" class="menu-link">
                                                <div>GRN Entry</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view stock-entry'))
                                        <li class="menu-item {{ request()->is('stock_entries*') ? 'active' : '' }}">
                                            <a href="{{ url('stock_entries') }}" class="menu-link">
                                                <div>Stock Entry</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view stock-consumable-return'))
                                        <li class="menu-item {{ request()->is('stock_consumables_returns*') ? 'active' : '' }}">
                                            <a href="{{ url('stock_consumables_returns') }}" class="menu-link">
                                                <div>Stock Consumables & Returns Management</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                {{-- Production --}}
                                @if($user && ($isSuper || $user->can('view production') || $user->can('view job-card')))
                                <li class="menu-item {{ (request()->is('job_card_entries*') || request()->is('productions*')) ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-inbox-line"></i>
                                        <div>Production</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view production'))
                                        <li class="menu-item {{ request()->is('job_card_entries*') ? 'active' : '' }}">
                                            <a href="{{ url('job_card_entries') }}" class="menu-link">
                                                <div>Job Card Entry</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view job-card'))
                                        <li class="menu-item {{ request()->is('productions*') ? 'active' : '' }}">
                                            <a href="{{ url('productions') }}" class="menu-link">
                                                <div>Production</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @if($isSuper || $user->can('view task-creation') || $user->can('view task-assignment') || $user->can('view task-tracking-monitoring') || $user->can('view task-status-updates'))
                                <li class="menu-item {{ (request()->is('tasks') || request()->is('add_task') || request()->is('view_task') || request()->is('task_assigned') || request()->is('view_task_assigned') || request()->is('task_management')) ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-task-line"></i>
                                        <div>Task Management</div>
                                    </a>

                                    <ul class="menu-sub">

                                        @if($isSuper || $user->can('view task-creation'))
                                        <li class="menu-item {{ request()->is('tasks') || request()->is('add_task') ? 'active' : '' }}">
                                            <a href="{{ url('tasks') }}" class="menu-link">
                                                <div>Task Creation</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view task-assignment'))
                                        <li class="menu-item {{ request()->is('task_assigned') ? 'active' : '' }}">
                                            <a href="{{ url('task_assigned') }}" class="menu-link">
                                                <div>Task Assignment</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view task-tracking-monitoring'))
                                        <li
                                            class="menu-item {{ request()->is('task_tracking_monitoring') || request()->is('add_task_tracking_monitoring') || request()->is('view_task_tracking_monitoring') ? 'active' : '' }}">
                                            <a href="{{ url('task_tracking_monitoring') }}" class="menu-link">
                                                <div>Task Tracking & Monitoring</div>
                                            </a>
                                        </li>
                                        @endif
                                        @if($isSuper || $user->can('view task-status-updates'))
                                        <li
                                            class="menu-item {{ request()->is('task_status_updates') || request()->is('add_task_status_update') || request()->is('view_task_status_update') ? 'active' : '' }}">
                                            <a href="{{ url('task_status_updates') }}" class="menu-link">
                                                <div>Task Status Updates</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @if($isSuper || $user->can('view sales-order') || $user->can('view sales-invoice') || $user->can('view credit-notes'))
                                <li class="menu-item {{ (request()->is('sales_order*') || request()->is('sales_invoice*') || request()->is('credit_notes*')) ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-order-play-line"></i>
                                        <div>Sales</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view sales-order'))
                                        <li class="menu-item {{ request()->is('sales_order*') ? 'active' : '' }}">
                                            <a href="{{ url('sales_order') }}" class="menu-link">
                                                <div>Sales Order</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view sales-invoice'))
                                        <li class="menu-item {{ request()->is('sales_invoice*') ? 'active' : '' }}">
                                            <a href="{{ url('sales_invoice') }}" class="menu-link">
                                                <div>Sales Invoice</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view credit-notes'))
                                        <li class="menu-item {{ request()->is('credit_notes*') ? 'active' : '' }}">
                                            <a href="{{ url('credit_notes') }}" class="menu-link">
                                                <div>Credit Notes</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @if($isSuper || $user->can('view billing'))
                                <li class="menu-item {{ request()->is('billing*') ? 'active' : '' }}">
                                    <a href="{{ url('billing') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-file-list-3-line"></i>
                                        <div>Billing</div>
                                    </a>
                                </li>
                                @endif
                                @if($isSuper || $user->can('view manage-payments'))
                                <li class="menu-item {{ request()->is('payments*') ? 'active' : '' }}">
                                    <a href="{{ url('payments') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-bank-card-line"></i>
                                        <div>Manage Payments</div>
                                    </a>
                                </li>
                                @endif


                                <!-- Emp. Payroll & Attendance -->
                                @if($user && ($isSuper || $user->can('view attendance') || $user->can('view manage-leaves') || $user->can('view overtime-bonus') || $user->can('view salary-calculation') || $user->can('view payslip-generation') || $user->can('view payroll-reports')))
                                <li class="menu-item {{ (request()->is('attendances*') || request()->is('leave*') || request()->is('overtime*') || request()->is('salary_calculation*') || request()->is('payslip*') || request()->is('payroll_reports*')) ? 'active' : '' }}">

                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-money-rupee-circle-line"></i>
                                        <div>Emp. Payroll & Attendance</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view attendance'))
                                        <li class="menu-item {{ request()->is('attendances*') ? 'active' : '' }}">
                                            <a href="{{ url('attendances') }}" class="menu-link">
                                                <div>Attendances</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view manage-leaves'))
                                        <li class="menu-item {{ request()->is('leave*') ? 'active' : '' }}">
                                            <a href="{{ url('leave') }}" class="menu-link">
                                                <div>Manage Leaves</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view overtime-bonus'))
                                        <li class="menu-item {{ request()->is('overtime*') ? 'active' : '' }}">
                                            <a href="{{ url('overtime') }}" class="menu-link">
                                                <div>Overtime / Bonus</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view salary-calculation'))
                                        <li class="menu-item {{ request()->is('salary_calculation*') ? 'active' : '' }}">
                                            <a href="{{ url('salary_calculation') }}" class="menu-link">
                                                <div>Salary Calculation</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view payslip-generation'))
                                        <li class="menu-item {{ request()->is('payslip*') ? 'active' : '' }}">
                                            <a href="{{ url('payslip') }}" class="menu-link">
                                                <div>Payslip Generation</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view payroll-reports'))
                                        <li class="menu-item {{ request()->is('payroll_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('payroll_reports') }}" class="menu-link">
                                                <div>Payroll Reports</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                <!-- System Utility -->
                                @if($user && ($isSuper || $user->can('view document-repository') || $user->can('view log') || $user->can('view backup-restore')))
                                <li class="menu-item {{ request()->is('document_repository*') || request()->is('logs') || request()->is('backup_restore') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-database-2-line"></i>
                                        <div>System Utility</div>
                                    </a>
                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view document-repository'))
                                        <li class="menu-item {{ request()->is('document_repository*') ? 'active' : '' }}">
                                            <a href="{{ url('document_repository') }}" class="menu-link">
                                                <div>Document Repository</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view log'))
                                        <li class="menu-item {{ request()->is('logs') ? 'active' : '' }}">
                                            <a href="{{ url('logs') }}" class="menu-link">
                                                <div>Logs & Audit Log</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view backup-restore'))
                                        <li class="menu-item {{ request()->is('backup_restore') ? 'active' : '' }}">
                                            <a href="{{ url('backup_restore') }}" class="menu-link">
                                                <div>Backup & Restore</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                <!-- Reports -->
                                @if($user && (
                                $isSuper || $user->can('view customer-report') || $user->can('view sale-report') || $user->can('view stock-report') || $user->can('view daily-production-report') || $user->can('view order-report') || $user->can('view employee-report')))
                                <li class="menu-item {{  request()->is('customer_reports*') || request()->is('sale_reports*') || request()->is('stock_reports*') ||  request()->is('daily_production_reports*') || request()->is('order_reports*') || request()->is('employee_reports*') ? 'active' : '' }}">
                                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                                        <i class="menu-icon icon-base ri ri-file-chart-line"></i>
                                        <div>Reports</div>
                                    </a>

                                    <ul class="menu-sub">
                                        @if($isSuper || $user->can('view customer-report'))
                                        <li class="menu-item {{ request()->is('customer_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('customer_reports') }}" class="menu-link">
                                                <div>Customer Report</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view sale-report'))
                                        <li class="menu-item {{ request()->is('sale_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('sale_reports') }}" class="menu-link">
                                                <div>Sale Report</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view stock-report'))
                                        <li class="menu-item {{ request()->is('stock_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('stock_reports') }}" class="menu-link">
                                                <div>Stock Report</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view daily-production-report'))
                                        <li class="menu-item {{ request()->is('daily_production_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('daily_production_reports') }}" class="menu-link">
                                                <div>Daily Production Report</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view order-report'))
                                        <li class="menu-item {{ request()->is('order_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('order_reports') }}" class="menu-link">
                                                <div>Order Report</div>
                                            </a>
                                        </li>
                                        @endif

                                        @if($isSuper || $user->can('view employee-report'))
                                        <li class="menu-item {{ request()->is('employee_reports*') ? 'active' : '' }}">
                                            <a href="{{ url('employee_reports') }}" class="menu-link">
                                                <div>Employee Report</div>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif

                                <!-- Settings -->
                                @if($user && ($isSuper || $user->can('view settings')))
                                <li class="menu-item {{ request()->is('settings') ? 'active' : '' }}">
                                    <a href="{{ url('settings') }}" class="menu-link">
                                        <i class="menu-icon icon-base ri ri-settings-5-line"></i>
                                        <div>Settings</div>
                                    </a>
                                </li>
                                @endif

                            </ul>
                        </div>
                        <a href="#" class="menu-horizontal-next"></a>
                    </div>
                </aside>