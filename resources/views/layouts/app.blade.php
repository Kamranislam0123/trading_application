@if(App\Model\LoginCheck::first()->status != 1)
    @if (Auth::check())
        @php
            Auth::logout();
        @endphp
    @endif
  <script>window.location = "{{ route('login_failed') }}";</script>
@endif
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="Juyel Islam Shah 01744711278">
    <title> {{ config('app.name', 'AT International') }} </title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/Ionicons/css/ionicons.min.css') }}">

@yield('style')

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('themes/backend/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/backend/css/custom.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>AP</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Admin</b>Panel</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            @if(Auth::user())
            <h4 class="pull-left" style="color: white; margin-top: 15px; padding-left: 20px">
                {{-- @if (Auth::user()->company_branch_id == 0)
                  <b>  {{ Auth::user()->name }} </b>
                @elseif (Auth::user()->company_branch_id == 0)
                    {{ config('app.name') }}
                @else
                    Your Choice Plus
                @endif --}}
            </h4>
            @endif


            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="">
                        {{-- <a href="https://2aitautomation.com/about2ait" target="_blank" style="color:#fff;">
                            About 2ait
                        </a> --}}
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('img/avatar.png') }}" class="user-image" alt="Avatar">
                            <span class="hidden-xs">{{ auth()->user()->name??'' }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">Sign out</a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>

                <?php
                $subMenu = ['warehouse', 'warehouse.add', 'warehouse.edit',
                    'unit','unit.add','unit.edit'];
                ?>
                @if(Auth::id() != 36)
                
                <!-- Administrator menu -->
                <!-- @can('administrator')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-blue"></i> <span>Administrator</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('warehouse')
                                <li class="{{ Route::currentRouteName() == 'warehouse' ? 'active' : '' }}">
                                    <a href="{{ route('warehouse') }}"><i class="fa fa-circle-o"></i> Warehouse </a>
                                </li>
                            @endcan
                            @can('designation')
                                <li class="{{ Route::currentRouteName() == 'unit' ? 'active' : '' }}">
                                    <a href="{{ route('unit') }}"><i class="fa fa-circle-o"></i> Unit </a>
                                </li>
                            @endcan

                                <li class="{{ Route::currentRouteName() == 'company-branch' ? 'active' : '' }}">
                                    <a href="{{ route('company-branch') }}"><i class="fa fa-circle-o"></i> Company Branch </a>
                                </li>

                        </ul>
                    </li>
                @endcan -->
                
                @endif

                <?php
                $subMenu = ['bank', 'bank.add', 'bank.edit', 'branch', 'branch.add', 'branch.edit',
                    'bank_account', 'bank_account.add', 'bank_account.edit','cash'];
                ?>

                <!-- @can('bank_and_account')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-blue"></i> <span> Bank & Account </span>
                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('bank')
                                <li class="{{ Route::currentRouteName() == 'bank' ? 'active' : '' }}">
                                    <a href="{{ route('bank') }}"><i class="fa fa-circle-o"></i> Bank</a>
                                </li>
                            @endcan
                            @can('branch')
                                <li class="{{ Route::currentRouteName() == 'branch' ? 'active' : '' }}">
                                    <a href="{{ route('branch') }}"><i class="fa fa-circle-o"></i> Branch</a>
                                </li>
                            @endcan
                            @can('account')
                                <li class="{{ Route::currentRouteName() == 'bank_account' ? 'active' : '' }}">
                                    <a href="{{ route('bank_account') }}"><i class="fa fa-circle-o"></i> Account</a>
                                </li>
                            @endcan
                            @can('cash')
                                <li class="{{ Route::currentRouteName() == 'cash' ? 'active' : '' }}">
                                    <a href="{{ route('cash') }}"><i class="fa fa-circle-o"></i> Cash </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan -->

                <?php
                    $subMenu = ['department','department.add','department.edit','designation','designation.add','designation.edit','employee.all', 'employee.add', 'employee.edit', 'employee.details','employee.attendance','report.employee_list'];
                ?>

                @can('hr')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-info"></i> <span>SR</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('department')
                                <li class="{{ Route::currentRouteName() == 'department' ? 'active' : '' }}">
                                    <a href="{{ route('department') }}"><i class="fa fa-circle-o"></i> Department</a>
                                </li>
                            @endcan
                            @can('designation')
                                <li class="{{ Route::currentRouteName() == 'designation' ? 'active' : '' }}">
                                    <a href="{{ route('designation') }}"><i class="fa fa-circle-o"></i> Designation</a>
                                </li>
                            @endcan
                            @can('employee')
                            <li class="{{ Route::currentRouteName() == 'employee.all' ? 'active' : '' }}">
                                <a href="{{ route('employee.all') }}"><i class="fa fa-circle-o"></i> Sales Person</a>
                            </li>
                            @endcan
                            @can('employee_list')
                            <li class="{{ Route::currentRouteName() == 'report.employee_list' ? 'active' : '' }}">
                                <a href="{{ route('report.employee_list') }}"><i class="fa fa-circle-o"></i> Sales Person List</a>
                            </li>
                            @endcan
                            <!-- @can('employee_attendance')
                            <li class="{{ Route::currentRouteName() == 'employee.attendance' ? 'active' : '' }}">
                                <a href="{{ route('employee.attendance') }}"><i class="fa fa-circle-o"></i> Employee Attendance</a>
                            </li>
                            @endcan -->
                        </ul>
                    </li>
                @endcan
                <?php
                $subMenu = ['payroll.salary_update.index', 'payroll.salary_process.index',
                    'payroll.leave.index','payroll.holiday.index','payroll.holiday_add','payroll.holiday_edit'];
                ?>
                
                <!-- Payroll menu -->
                <!-- @can('payroll')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-info"></i> <span>Payroll</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('salary_update')
                        <li class="{{ Route::currentRouteName() == 'payroll.salary_update.index' ? 'active' : '' }}">
                            <a href="{{ route('payroll.salary_update.index') }}"><i class="fa fa-circle-o"></i> Salary Update</a>
                        </li>
                        @endcan
                        @can('salary_process')
                        <li class="{{ Route::currentRouteName() == 'payroll.salary_process.index' ? 'active' : '' }}">
                            <a href="{{ route('payroll.salary_process.index') }}"><i class="fa fa-circle-o"></i> Salary Process</a>
                        </li>
                        @endcan
                        @can('leave')
                        <li class="{{ Route::currentRouteName() == 'payroll.leave.index' ? 'active' : '' }}">
                            <a href="{{ route('payroll.leave.index') }}"><i class="fa fa-circle-o"></i> Leave</a>
                        </li>
                        @endcan
                        @can('holiday')
                        <li class="{{ Route::currentRouteName() == 'payroll.holiday.index' ? 'active' : '' }}">
                            <a href="{{ route('payroll.holiday.index') }}"><i class="fa fa-circle-o"></i> Holiday</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan -->

                <?php
                $subMenu = ['supplier', 'supplier.add', 'supplier.edit', 'product_item', 'product_item.add',
                    'product_item.edit', 'purchase_order.create', 'purchase_receipt.all','product_descrition',
                    'purchase_receipt.details', 'purchase_receipt.qr_code', 'supplier_payment.all',
                    'purchase_receipt.payment_details', 'purchase_inventory.all',
                    'purchase_inventory.details', 'purchase_inventory.qr_code',
                    'purchase_receipt.edit', 'product', 'product.add', 'product.edit',
                    'product_color', 'product_color.add', 'product_color.edit',
                    'product_size', 'product_size.add', 'product_size.edit',
                    'product_category', 'product_category.add', 'product_category.edit',
                    'product_stock', 'product_stock.add', 'product_stock.edit','purchase_stock_transfer',
                    'purchase_stock_transfer_details','stock_product_invoice.all','stock_product.invoice',
                    'stock_transfer.invoice','stock_product.barcode_print','stock_transfer_challan',
                    'transfer_challan.print','stock_product.barcode','stock_transfer_details'];
                ?>

                <!-- Purchase menu -->
                <!-- @can('purchase')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-blue"></i> <span>Purchase</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('supplier')
                                <li class="{{ Route::currentRouteName() == 'supplier' ? 'active' : '' }}">
                                    <a href="{{ route('supplier') }}"><i class="fa fa-circle-o"></i> Supplier</a>
                                </li>
                            @endcan
                            @can('product_item')
                                <li class="{{ Route::currentRouteName() == 'product_color' ? 'active' : '' }}">
                                    <a href="{{ route('product_color') }}"><i class="fa fa-circle-o"></i> Product Color </a>
                                </li>
                            @endcan
                            @can('product_item')
                                <li class="{{ Route::currentRouteName() == 'product_size' ? 'active' : '' }}">
                                    <a href="{{ route('product_size') }}"><i class="fa fa-circle-o"></i> Product Size </a>
                                </li>
                            @endcan
                            @can('product_item')
                                <li class="{{ Route::currentRouteName() == 'product_item' ? 'active' : '' }}">
                                    <a href="{{ route('product_item') }}"><i class="fa fa-circle-o"></i> Product Model </a>
                                </li>
                            @endcan
                            @can('product_item')
                                <li class="{{ Route::currentRouteName() == 'product_category' ? 'active' : '' }}">
                                    <a href="{{ route('product_category') }}"><i class="fa fa-circle-o"></i> Product Category </a>
                                </li>
                            @endcan
                            {{-- <li class="{{ Route::currentRouteName() == 'product_descrition' ? 'active' : '' }}">
                                <a href="{{ route('product_descrition') }}"><i class="fa fa-circle-o"></i> Description</a>
                            </li> --}}
                            @can('purchase_order')
                                <li class="{{ Route::currentRouteName() == 'purchase_order.create' ? 'active' : '' }}">
                                    <a href="{{ route('purchase_order.create') }}"><i class="fa fa-circle-o"></i> Purchase Order</a>
                                </li>
                            @endcan
                            @can('purchase_receipt')
                                <li class="{{ Route::currentRouteName() == 'purchase_receipt.all' ? 'active' : '' }}">
                                    <a href="{{ route('purchase_receipt.all') }}"><i class="fa fa-circle-o"></i> Receipt</a>
                                </li>
                            @endcan
                            @can('supplier_payment')
                                <li class="{{ Route::currentRouteName() == 'supplier_payment.all' ? 'active' : '' }}">
                                    <a href="{{ route('supplier_payment.all') }}"><i class="fa fa-circle-o"></i> Supplier Payment</a>
                                </li>
                            @endcan
                            @can('purchase_inventory')
                                <li class="{{ Route::currentRouteName() == 'product_stock' ? 'active' : '' }}">
                                    <a href="{{ route('product_stock') }}"><i class="fa fa-circle-o"></i> Manually Stock </a>
                                </li>
                            @endcan
                            @if(Auth::user()->id != 36)
                            @can('purchase_order')
                                <li class="{{ Route::currentRouteName() == 'stock_product_invoice.all' ? 'active' : '' }}">
                                    <a href="{{ route('stock_product_invoice.all') }}"><i class="fa fa-circle-o"></i>Stock Product Invoice</a>
                                </li>
                            @endcan
                            @endif
                            @can('purchase_inventory')
                                <li class="{{ Route::currentRouteName() == 'purchase_inventory.all' ? 'active' : '' }}">
                                    <a href="{{ route('purchase_inventory.all') }}"><i class="fa fa-circle-o"></i> Inventory</a>
                                </li>
                            @endcan
{{--                            @can('purchase_inventory')--}}
{{--                                <li class="{{ Route::currentRouteName() == 'purchase_stock_transfer' ? 'active' : '' }}">--}}
{{--                                    <a href="{{ route('purchase_stock_transfer') }}"><i class="fa fa-circle-o"></i>Stock Transfer</a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}
                            @can('purchase_inventory')
                                <li class="{{ Route::currentRouteName() == 'stock_transfer.invoice' ? 'active' : '' }}">
                                    <a href="{{ route('stock_transfer.invoice') }}"><i class="fa fa-circle-o"></i>Stock Transfer</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan -->

                
                <?php
                $subMenu = ['sales_order.create', 'sale_receipt.all', 'sale_receipt.details',
                    'customer', 'customer.add', 'customer.edit','sale_receipt.payment_details',
                    'sub_customer', 'sub_customer.add', 'sub_customer.edit',
                    'sale_information.index', 'customer_payment.all',
                    'sale_receipt.edit', 'sale_receipt.customer.all', 'sale_receipt.supplier.all',
                    'client_payment.customer.all', 'client_payment.supplier.all',
                    'sales_wastage.create', 'sale_wastage_receipt.customer.all',
                    'client_payment.all_pending_check','manually_chequeIn','cheque_status','
                    customer_payments','sale_receipt.customer.warehouse_pending.all',
                    'sale_receipt_warehouse_pending.edit','client_payment.admin_pending_check',
                    'client_payment.your_choice_pending_check','client_payment.your_choice_plus_pending_check',];
                ?>

                @can('sale')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-info"></i> <span> Customer Due Management </span>
                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @if(Auth::user()->id != 36)
                            @can('customer')
                                <li class="{{ Route::currentRouteName() == 'customer' ? 'active' : '' }}">
                                    <a href="{{ route('customer') }}"><i class="fa fa-circle-o"></i> Customer </a>
                                </li>
                            @endcan
                            @endif
                            @if(Auth::user()->id != 36)
                                @if (Auth::user()->company_branch_id == 1 || Auth::user()->company_branch_id == 2 || Auth::user()->id == 1)
                                    @can('sales_order')
                                        <li class="{{ Route::currentRouteName() == 'manually_chequeIn' ? 'active' : '' }}">
                                            <a href="{{ route('manually_chequeIn') }}"><i class="fa fa-circle-o"></i> Manually Due Entry </a>
                                        </li>
                                    @endcan
                                @endif
                            @endif
                            <!-- @can('sales_order')
                                <li class="{{ Route::currentRouteName() == 'sales_order.create' ? 'active' : '' }}">
                                    <a href="{{ route('sales_order.create') }}"><i class="fa fa-circle-o"></i> Sales Order</a>
                                </li>
                            @endcan -->

                            <?php
                            $subSubMenu = ['sale_receipt.customer.all', 'sale_receipt.supplier.all', 'sale_receipt.details',
                                'sale_receipt.payment_details','client_payment.all_pending_check','manually_chequeIn','cheque_status',
                                'sale_receipt.customer.warehouse_pending.all'];
                            ?>
                            @if(Auth::user()->id != 36)
                            <!-- @can('sale_receipt')
                                <li class="{{ Route::currentRouteName() == 'sale_receipt.customer.all' ? 'active' : '' }}">
                                    <a href="{{ route('sale_receipt.customer.all') }}"><i class="fa fa-circle-o"></i> Sales Receipt </a>
                                </li>
                            @endcan -->
                            @endif
                            @if(Auth::user()->company_branch_id != 1 && Auth::user()->company_branch_id != 2)
                            <!-- @can('sale_receipt')
                                <li class="{{ Route::currentRouteName() == 'sale_receipt.customer.warehouse_pending.all' ? 'active' : '' }}">
                                    <a href="{{ route('sale_receipt.customer.warehouse_pending.all') }}"><i class="fa fa-circle-o"></i> Sales Receipt Pending</a>
                                </li>
                            @endcan -->
                            @endif

{{--                            @can('sales_order')--}}
{{--                                <li class="{{ Route::currentRouteName() == 'sales_wastage.create' ? 'active' : '' }}">--}}
{{--                                    <a href="{{ route('sales_wastage.create') }}"><i class="fa fa-circle-o"></i> Sales Wastage</a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}
{{--                            @can('sale_receipt')--}}
{{--                                <li class="{{ Route::currentRouteName() == 'sale_wastage_receipt.customer.all' ? 'active' : '' }}">--}}
{{--                                    <a href="{{ route('sale_wastage_receipt.customer.all') }}"><i class="fa fa-circle-o"></i> Wastage Receipt </a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}

                            @can('sales_order')
                                <li class="{{ Route::currentRouteName() == 'client_payment.all_pending_check' ? 'active' : '' }}">
                                    <a href="{{ route('client_payment.all_pending_check') }}"><i class="fa fa-circle-o"></i> All Due List </a>
                                </li>
                            @endcan
                            @can('customer_payment')
                                <li class="{{ Route::currentRouteName() == 'client_payment.customer.all' ? 'active' : '' }}">
                                    <a href="{{ route('client_payment.customer.all') }}"><i class="fa fa-circle-o"></i> Customer Payment </a>
                                </li>
                            @endcan
                            @if (Auth::user()->company_branch_id == 0)
                                <!-- @can('sales_order')
                                    <li class="{{ Route::currentRouteName() == 'client_payment.admin_pending_check' ? 'active' : '' }}">
                                        <a href="{{ route('client_payment.admin_pending_check') }}"><i class="fa fa-circle-o"></i> Admin Pending Cheque </a>
                                    </li>
                                @endcan -->
                                <!-- @can('sales_order')
                                    <li class="{{ Route::currentRouteName() == 'client_payment.your_choice_pending_check' ? 'active' : '' }}">
                                        <a href="{{ route('client_payment.your_choice_pending_check') }}"><i class="fa fa-circle-o"></i> Your Choice P.Cheque </a>
                                    </li>
                                @endcan -->
                                <!-- @can('sales_order')
                                    <li class="{{ Route::currentRouteName() == 'client_payment.your_choice_plus_pending_check' ? 'active' : '' }}">
                                        <a href="{{ route('client_payment.your_choice_plus_pending_check') }}"><i class="fa fa-circle-o"></i> Your Choice Plus P.Cheque </a>
                                    </li>
                                @endcan -->
                            @endif

                        </ul>
                    </li>
                @endcan

                <?php
                $subMenu = [
                    'sales_return','sales_return.add','sales_return.create', 'sales_return.edit',
                    'product_return_invoice.all','return_invoice.details'];
                ?>

                <!-- @can('administrator')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-info"></i> <span> Sales Return  </span>
                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('customer')
                                <li class="{{ Route::currentRouteName() == 'sales_return' ? 'active' : '' }}">
                                    <a href="{{ route('sales_return') }}"><i class="fa fa-circle-o"></i> Sales Return </a>
                                </li>
                            @endcan
                            @can('customer')
                                <li class="{{ Route::currentRouteName() == 'product_return_invoice.all' ? 'active' : '' }}">
                                    <a href="{{ route('product_return_invoice.all') }}"><i class="fa fa-circle-o"></i> Return Invoice </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan -->

                <?php
                $subMenu = ['account_head.type', 'account_head.type.add', 'account_head.type.edit',
                    'account_head.sub_type', 'account_head.sub_type.add', 'account_head.sub_type.edit',
                    'transaction.all', 'transaction.add', 'transaction.details', 'balance_transfer.add'];
                ?>

                <!-- Accounts menu -->
                <!-- @can('accounts')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-info"></i> <span>Accounts</span>
                            <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                        </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('account_head_type')
                                <li class="{{ Route::currentRouteName() == 'account_head.type' ? 'active' : '' }}">
                                    <a href="{{ route('account_head.type') }}"><i class="fa fa-circle-o"></i> Account Head Type</a>
                                </li>
                            @endcan
{{--                            @can('account_head_sub_type')--}}
{{--                                <li class="{{ Route::currentRouteName() == 'account_head.sub_type' ? 'active' : '' }}">--}}
{{--                                    <a href="{{ route('account_head.sub_type') }}"><i class="fa fa-circle-o"></i> Account Head Sub Type</a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}
                            @can('project_wise_transaction')
                                <li class="{{ Route::currentRouteName() == 'transaction.all' ? 'active' : '' }}">
                                    <a href="{{ route('transaction.all') }}"><i class="fa fa-circle-o"></i> Transaction</a>
                                </li>
                            @endcan
                            @can('balance_transfer')
                                <li class="{{ Route::currentRouteName() == 'balance_transfer.add' ? 'active' : '' }}">
                                    <a href="{{ route('balance_transfer.add') }}"><i class="fa fa-circle-o"></i> Balance Transfer</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan -->

                <?php
                $subMenu = ['report.salary.sheet','report.purchase', 'report.sale', 'report.balance_summary', 'report.sub_client_statement',
                    'report.profit_and_loss', 'report.ledger','report.purchase_stock', 'report.cashbook', 'report.monthly_expenditure',
                    'report.bank_statement','report.income_statement','report.client_statement','report.supplier_statement','report.monthly_crm',
                    'report.employee_attendance','report.sale_stock','report.price.with.stock','report.price.without.stock','report.receive_payment','report.trail_balance',
                    'report.product_in_out','report.cash_statement','report.party_ledger','report.bill_wise_profit_loss','report.transaction',
                    'report.branch_wise_client','report.branch_wise_sale_return','report.employee_target_customer_wise'];
                ?>

                @can('report')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-info"></i> <span>Report</span>
                            <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">

                            @can('client_summary')
                                <li class="{{ Route::currentRouteName() == 'report.client_statement' ? 'active' : '' }}">
                                    <a href="{{ route('report.client_statement') }}"><i class="fa fa-circle-o"></i> Client Report</a>
                                </li>
                                <li class="{{ Route::currentRouteName() == 'report.employee_target_customer_wise' ? 'active' : '' }}">
                                    <a href="{{ route('report.employee_target_customer_wise') }}"><i class="fa fa-circle-o"></i> Sales Target Customer Wise</a>
                                </li>
                            @endcan
                            <!-- @can('client_summary')
                                <li class="{{ Route::currentRouteName() == 'report.party_ledger' ? 'active' : '' }}">
                                    <a href="{{ route('report.party_ledger') }}"><i class="fa fa-circle-o"></i> Party Ledger</a>
                                </li>
                            @endcan -->
                            <!-- @can('supplier_report')
                                <li class="{{ Route::currentRouteName() == 'report.supplier_statement' ? 'active' : '' }}">
                                    <a href="{{ route('report.supplier_statement') }}"><i class="fa fa-circle-o"></i> Supplier Report</a>
                                </li>
                            @endcan
                             @can('purchase_report')
                                <li class="{{ Route::currentRouteName() == 'report.purchase' ? 'active' : '' }}">
                                    <a href="{{ route('report.purchase') }}"><i class="fa fa-circle-o"></i> Purchase Report</a>
                                </li>
                            @endcan
                            @can('purchase_report')
                                <li class="{{ Route::currentRouteName() == 'report.branch_wise_sale_return' ? 'active' : '' }}">
                                    <a href="{{ route('report.branch_wise_sale_return') }}"><i class="fa fa-circle-o"></i> Sale Return Report</a>
                                </li>
                            @endcan
                            @can('sale_report')
                                <li class="{{ Route::currentRouteName() == 'report.sale' ? 'active' : '' }}">
                                    <a href="{{ route('report.sale') }}"><i class="fa fa-circle-o"></i> Sale Report</a>
                                </li>
                            @endcan
                            @can('balance_summary')
                                <li class="{{ Route::currentRouteName() == 'report.balance_summary' ? 'active' : '' }}">
                                    <a href="{{ route('report.balance_summary') }}"><i class="fa fa-circle-o"></i> Balance Summary</a>
                                </li>
                            @endcan
                             @can('profit_and_loss')
                                <li class="{{ Route::currentRouteName() == 'report.profit_and_loss' ? 'active' : '' }}">
                                    <a href="{{ route('report.profit_and_loss') }}"><i class="fa fa-circle-o"></i> Profit & Loss</a>
                                </li>
                            @endcan
                            @can('profit_and_loss')
                                <li class="{{ Route::currentRouteName() == 'report.bill_wise_profit_loss' ? 'active' : '' }}">
                                    <a href="{{ route('report.bill_wise_profit_loss') }}"><i class="fa fa-circle-o"></i>Bill Wise Profit Loss</a>
                                </li>
                            @endcan -->
                            {{-- @can('ledger')
                                <li class="{{ Route::currentRouteName() == 'report.ledger' ? 'active' : '' }}">
                                    <a href="{{ route('report.ledger') }}"><i class="fa fa-circle-o"></i> Ledger</a>
                                </li>
                            @endcan --}}
                             <!-- @can('price_with_stock')
                                <li class="{{ Route::currentRouteName() == 'report.product_in_out' ? 'active' : '' }}">
                                    <a href="{{ route('report.product_in_out') }}"><i class="fa fa-circle-o"></i> Product in Out Report</a>
                                </li>
                             @endcan -->
                                    <!-- @can('price_with_stock')
                                        <li class="{{ Route::currentRouteName() == 'report.price.with.stock' ? 'active' : '' }}">
                                            <a href="{{ route('report.price.with.stock') }}"><i class="fa fa-circle-o"></i> Price With Stock</a>
                                        </li>
                                    @endcan -->
                            {{-- @can('price_without_stock')
                                <li class="{{ Route::currentRouteName() == 'report.price.without.stock' ? 'active' : '' }}">
                                    <a href="{{ route('report.price.without.stock') }}"><i class="fa fa-circle-o"></i> Price Without Stock</a>
                                </li>
                            @endcan --}}
                            <!-- @can('cashbook')
                                <li class="{{ Route::currentRouteName() == 'report.cashbook' ? 'active' : '' }}">
                                    <a href="{{ route('report.cashbook') }}"><i class="fa fa-circle-o"></i> Cashbook</a>
                                </li>
                            @endcan

                            @can('bank_statement')
                                <li class="{{ Route::currentRouteName() == 'report.bank_statement' ? 'active' : '' }}">
                                    <a href="{{ route('report.bank_statement') }}"><i class="fa fa-circle-o"></i> Bank Statement</a>
                                </li>
                            @endcan
                            @can('cashbook')
                            <li class="{{ Route::currentRouteName() == 'report.cash_statement' ? 'active' : '' }}">
                                <a href="{{ route('report.cash_statement') }}"><i class="fa fa-circle-o"></i> Cash Statement</a>
                            </li>
                            @endcan
                             @can('receive_and_payment')
                                <li class="{{ Route::currentRouteName() == 'report.receive_payment' ? 'active' : '' }}">
                                    <a href="{{ route('report.receive_payment') }}"><i class="fa fa-circle-o"></i> Receive & Payment</a>
                                </li>
                            @endcan
                            @can('bank_statement')
                            <li class="{{ Route::currentRouteName() == 'report.branch_wise_client' ? 'active' : '' }}">
                                <a href="{{ route('report.branch_wise_client') }}"><i class="fa fa-circle-o"></i> Branch Wise Client</a>
                            </li>
                            @endcan
                            @can('bank_statement')
                                <li class="{{ Route::currentRouteName() == 'report.transaction' ? 'active' : '' }}">
                                    <a href="{{ route('report.transaction') }}"><i class="fa fa-circle-o"></i> Transction Report</a>
                                </li>
                            @endcan -->
                        </ul>
                    </li>
                @endcan

                <?php
                $subMenu = ['user.all', 'user.edit', 'user.add','user.activity'];
                ?>

                @can('user_management')
                    <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-circle-o text-info"></i> <span>User Management</span>
                            <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                        </a>
                        <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                            @can('users')
                                <li class="{{ Route::currentRouteName() == 'user.all' ? 'active' : '' }}">
                                    <a href="{{ route('user.all') }}"><i class="fa fa-circle-o"></i> Users</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            {{-- <h1>
                @yield('title')
                <small><a style="font-size:18px;font-weight:bold;color:red" href="{{ asset('yourchoice_china.apk') }}"><i class="fa fa-android" aria-hidden="true"></i> Android Apps</a></small>
            </h1> --}}
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Design & Developed By <a target="_blank" href="http://datascapeit.com">Datascape IT Limited</a></b>
        </div>
        <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('dashboard') }}">{{ config('app.name') }}</a>.</strong> All rights
        reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset('themes/backend/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('themes/backend/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>

@yield('script')
<!-- AdminLTE App -->
<script src="{{ asset('themes/backend/js/adminlte.min.js') }}"></script>
</body>
</html>
