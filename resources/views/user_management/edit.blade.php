@extends('layouts.app')

@section('style')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/iCheck/square/blue.css') }}">
@endsection

@section('title')
    User Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">User Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('user.edit',['user' => $user->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $user->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Role *</label>
                            <div class="col-sm-10">

                                <select class="form-control" name="role">
                                    <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>All</option>
                                    <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>Not All</option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label class="col-sm-2 control-label"> Select Company Branch *</label>
                            <div class="col-sm-10">

                                <select class="form-control" name="company_branch_id">
                                    @foreach($companyBranchs as $companyBranch)
                                    <option value="{{ $companyBranch->id }}" {{ old('company_branch_id', $user->company_branch_id) == $companyBranch->id ? 'selected' : '' }}>{{$companyBranch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->

                        <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Email *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Email"
                                       name="email" value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $user->email) : old('email') }}">

                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Password *</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" placeholder="Enter Password"
                                       name="password">

                                @error('password')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Confirm Password *</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" placeholder="Enter Confirm Password"
                                       name="password_confirmation">
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <tr>
                                <td colspan="2">
                                    <input type="checkbox" id="checkAll"> Check All
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="5" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="administrator" id="administrator" {{ ($user->can('administrator') ? 'checked' : '') }}> Administrator
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="warehouse" id="warehouse" {{ ($user->can('warehouse') ? 'checked' : '') }}> Warehouse
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="unit" id="unit" {{ ($user->can('unit') ? 'checked' : '') }}> Unit
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="terms_condition" id="terms_condition" {{ ($user->can('terms_condition') ? 'checked' : '') }}> Terms & condition
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="department" id="department" {{ ($user->can('department') ? 'checked' : '') }}> Department
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="designation" id="designation" {{ ($user->can('designation') ? 'checked' : '') }}> Designation
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="4" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="bank_and_account" id="bank_and_account" {{ ($user->can('bank_and_account') ? 'checked' : '') }}> Bank & Account
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="bank" id="bank" {{ ($user->can('bank') ? 'checked' : '') }}> Bank
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="branch" id="branch" {{ ($user->can('branch') ? 'checked' : '') }}> Branch
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="account" id="account" {{ ($user->can('account') ? 'checked' : '') }}> Account
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="cash" id="cash" {{ ($user->can('cash') ? 'checked' : '') }}> Cash
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="3" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="hr" id="hr" {{ ($user->can('hr') ? 'checked' : '') }}> HR
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="employee" id="employee" {{ ($user->can('employee') ? 'checked' : '') }}> Employee
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="employee_list" id="employee_list" {{ ($user->can('employee_list') ? 'checked' : '') }}> Employee List
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="employee_attendance" id="employee_attendance" {{ ($user->can('employee_attendance') ? 'checked' : '') }}> Employee Attendance
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="4" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="payroll" id="payroll" {{ ($user->can('payroll') ? 'checked' : '') }}> Payroll
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="salary_update" id="salary_update" {{ ($user->can('salary_update') ? 'checked' : '') }}> Salary Update
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="salary_process" id="salary_process" {{ ($user->can('salary_process') ? 'checked' : '') }}> Salary Process
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="leave" id="leave" {{ ($user->can('leave') ? 'checked' : '') }}> Leave
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="holiday" id="holiday" {{ ($user->can('holiday') ? 'checked' : '') }}> Holiday
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="3" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="bank_and_account" id="bank_and_account" {{ ($user->can('bank_and_account') ? 'checked' : '') }}> Bank & Account
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="bank" id="bank" {{ ($user->can('bank') ? 'checked' : '') }}> Bank
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="branch" id="branch" {{ ($user->can('branch') ? 'checked' : '') }}> Branch
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="account" id="account" {{ ($user->can('account') ? 'checked' : '') }}> Account
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="4" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="crm" id="crm" {{ ($user->can('crm') ? 'checked' : '') }}> CRM
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="marketing" id="marketing" {{ ($user->can('marketing') ? 'checked' : '') }}> Client Work Order
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="employee_marketing" id="employee_marketing" {{ ($user->can('employee_marketing') ? 'checked' : '') }}> Employee Work Order Summary
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="employee_work_orders" id="employee_work_orders" {{ ($user->can('employee_work_orders') ? 'checked' : '') }}> Employee Based Client Orders
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="report_monthly_crm" id="report_monthly_crm" {{ ($user->can('report_monthly_crm') ? 'checked' : '') }}> Monthly CRM Report
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="1" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="supplier_charge" id="supplier_charge" {{ ($user->can('supplier_charge') ? 'checked' : '') }}> Supplier Charge
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="supplier_charge_edit" id="supplier_charge_edit" {{ ($user->can('supplier_charge_edit') ? 'checked' : '') }}> Supplier Charge Edit
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="4" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="proposal" id="proposal" {{ ($user->can('proposal') ? 'checked' : '') }}> Proposal
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="proposal_create" id="proposal_create" {{ ($user->can('proposal_create') ? 'checked' : '') }}> Proposal Create
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="proposal_edit" id="proposal_edit" {{ ($user->can('proposal_edit') ? 'checked' : '') }}> Proposal Edit
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="proposals" id="proposals" {{ ($user->can('proposals') ? 'checked' : '') }}> My Proposals
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="all_proposals" id="all_proposals" {{ ($user->can('all_proposals') ? 'checked' : '') }}> All Proposals
                                </td>
                            </tr>


                            <tr>
                                <td rowspan="7" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="purchase" id="purchase" {{ ($user->can('purchase') ? 'checked' : '') }}> Purchase
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="supplier" id="supplier" {{ ($user->can('supplier') ? 'checked' : '') }}> Supplier
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="product_item" id="product_item" {{ ($user->can('product_item') ? 'checked' : '') }}> Product Item
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="product" id="product" {{ ($user->can('product') ? 'checked' : '') }}> Product
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="purchase_order" id="purchase_order" {{ ($user->can('purchase_order') ? 'checked' : '') }}> Purchase Order
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="purchase_receipt" id="purchase_receipt" {{ ($user->can('purchase_receipt') ? 'checked' : '') }}> Purchase Receipt
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="supplier_payment" id="supplier_payment" {{ ($user->can('supplier_payment') ? 'checked' : '') }}> Supplier Payment
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="purchase_inventory" id="purchase_inventory" {{ ($user->can('purchase_inventory') ? 'checked' : '') }}> Inventory
                                </td>
                            </tr>


                            <tr>
                                <td rowspan="5" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="sale" id="sale" {{ ($user->can('sale') ? 'checked' : '') }}> Sale
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="customer" id="customer" {{ ($user->can('customer') ? 'checked' : '') }}> Customer
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="sub_customer" id="sub_customer" {{ ($user->can('sub_customer') ? 'checked' : '') }}> Sub customer
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="sales_order" id="sales_order" {{ ($user->can('sales_order') ? 'checked' : '') }}> Sales Order
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="sale_receipt" id="sale_receipt" {{ ($user->can('sale_receipt') ? 'checked' : '') }}> Receipt
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="customer_payment" id="customer_payment" {{ ($user->can('customer_payment') ? 'checked' : '') }}> Customer Payment
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="4" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="accounts" id="accounts" {{ ($user->can('accounts') ? 'checked' : '') }}> Accounts
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="account_head_type" id="account_head_type" {{ ($user->can('account_head_type') ? 'checked' : '') }}> Account Head Type
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="account_head_sub_type" id="account_head_sub_type" {{ ($user->can('account_head_sub_type') ? 'checked' : '') }}> Account Head Sub Type
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="project_wise_transaction" id="project_wise_transaction" {{ ($user->can('project_wise_transaction') ? 'checked' : '') }}> Transaction
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="balance_transfer" id="balance_transfer" {{ ($user->can('balance_transfer') ? 'checked' : '') }}> Balance Transfer
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="15" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="report" id="report" {{ ($user->can('report') ? 'checked' : '') }}> Report
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="client_summary" id="client_summary" {{ ($user->can('client_summary') ? 'checked' : '') }}> Client Report
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="sub_client_summary" id="sub_client_summary" {{ ($user->can('sub_client_summary') ? 'checked' : '') }}> Sub Client Report
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="supplier_report" id="supplier_report" {{ ($user->can('supplier_report') ? 'checked' : '') }}> Supplier Report
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="purchase_report" id="purchase_report" {{ ($user->can('purchase_report') ? 'checked' : '') }}> Purchase Report
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="sale_report" id="sale_report" {{ ($user->can('sale_report') ? 'checked' : '') }}> Sale Report
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="balance_summary" id="balance_summary" {{ ($user->can('balance_summary') ? 'checked' : '') }}> Balance Summary
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="profit_and_loss" id="profit_and_loss" {{ ($user->can('profit_and_loss') ? 'checked' : '') }}> Profit & Loss
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="ledger" id="ledger" {{ ($user->can('ledger') ? 'checked' : '') }}> Ledger
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="price_with_stock" id="price_with_stock" {{ ($user->can('price_with_stock') ? 'checked' : '') }}> Price With Stock
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="price_without_stock" id="price_without_stock" {{ ($user->can('price_without_stock') ? 'checked' : '') }}> Price Without Stock
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="cashbook" id="cashbook" {{ ($user->can('cashbook') ? 'checked' : '') }}> Cashbook
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="balance_sheet" id="balance_sheet" {{ ($user->can('balance_sheet') ? 'checked' : '') }}> Balance Sheet
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="monthly_expenditure" id="monthly_expenditure" {{ ($user->can('monthly_expenditure') ? 'checked' : '') }}> Monthly Expenditure
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="bank_statement" id="bank_statement" {{ ($user->can('bank_statement') ? 'checked' : '') }}> Bank Statement
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="permission[]" value="receive_and_payment" id="receive_and_payment" {{ ($user->can('receive_and_payment') ? 'checked' : '') }}> Receive & Payment
                                </td>
                            </tr>

                            <tr>
                                <td rowspan="1" style="vertical-align: middle;">
                                    <input type="checkbox" name="permission[]" value="user_management" id="user_management" {{ ($user->can('user_management') ? 'checked' : '') }}> User Management
                                </td>

                                <td>
                                    <input type="checkbox" name="permission[]" value="users" id="users" {{ ($user->can('users') ? 'checked' : '') }}> Users
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- iCheck -->
    <script src="{{ asset('themes/backend/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function () {
            $("#checkAll").click(function () {
                $('input:checkbox').not(this).prop('disabled', this.disabled);
                $('input:checkbox').not(this).prop('checked', this.checked);

                init();
            });

            // Administrator
            $('#administrator').click(function () {
                if ($(this).prop('checked')) {
                    $('#unit').attr("disabled", false);
                    $('#terms_condition').attr("disabled", false);
                    $('#project').attr("disabled", false);
                    $('#partner').attr("disabled", false);
                    $('#warehouse').attr("disabled", false);
                    $('#department').attr("disabled", false);
                    $('#designation').attr("disabled", false);
                } else {
                    $('#unit').attr("disabled", true);
                    $('#terms_condition').attr("disabled", true);
                    $('#project').attr("disabled", true);
                    $('#partner').attr("disabled", true);
                    $('#warehouse').attr("disabled", true);
                    $('#department').attr("disabled", true);
                    $('#designation').attr("disabled", true);
                }
            });

            // Bank & Account
            $('#bank_and_account').click(function () {
                if ($(this).prop('checked')) {
                    $('#bank').attr("disabled", false);
                    $('#branch').attr("disabled", false);
                    $('#account').attr("disabled", false);
                    $('#cash').attr("disabled", false);
                } else {
                    $('#bank').attr("disabled", true);
                    $('#branch').attr("disabled", true);
                    $('#cash').attr("disabled", true);
                }
            });

            // HR Management
            $('#hr').click(function () {
                if ($(this).prop('checked')) {
                    $('#employee').attr("disabled", false);
                    $('#employee_list').attr("disabled", false);
                    $('#employee_attendance').attr("disabled", false);
                } else {
                    $('#employee').attr("disabled", true);
                    $('#employee_list').attr("disabled", true);
                    $('#employee_attendance').attr("disabled", true);
                }
            });

            // payroll
            $('#payroll').click(function () {
                if ($(this).prop('checked')) {
                    $('#salary_update').attr("disabled", false);
                    $('#salary_process').attr("disabled", false);
                    $('#leave').attr("disabled", false);
                    $('#holiday').attr("disabled", false);
                } else {
                    $('#salary_update').attr("disabled", true);
                    $('#salary_process').attr("disabled", true);
                    $('#leave').attr("disabled", true);
                    $('#holiday').attr("disabled", true);
                }
            });

            // CRM Management
            $('#crm').click(function () {
                if ($(this).prop('checked')) {
                    $('#marketing').attr("disabled", false);
                    $('#employee_marketing').attr("disabled", false);
                    $('#employee_work_orders').attr("disabled", false);
                    $('#report_monthly_crm').attr("disabled", false);
                } else {
                    $('#marketing').attr("disabled", true);
                    $('#employee_marketing').attr("disabled", true);
                    $('#employee_work_orders').attr("disabled", true);
                    $('#report_monthly_crm').attr("disabled", true);
                }
            });

            // Proposal
            $('#proposal').click(function () {
                if ($(this).prop('checked')) {
                    $('#proposal_create').attr("disabled", false);
                    $('#proposal_edit').attr("disabled", false);
                    $('#proposals').attr("disabled", false);
                    $('#all_proposals').attr("disabled", false);
                } else {
                    $('#proposal_create').attr("disabled", true);
                    $('#proposal_edit').attr("disabled", true);
                    $('#proposals').attr("disabled", true);
                    $('#all_proposals').attr("disabled", true);
                }
            });

            // Supplier Charge
            $('#supplier_charge').click(function () {
                if ($(this).prop('checked')) {
                    $('#supplier_charge_edit').attr("disabled", false);
                } else {
                    $('#supplier_charge_edit').attr("disabled", true);
                }
            });

            // Purchase
            $('#purchase').click(function () {
                if ($(this).prop('checked')) {
                    $('#supplier').attr("disabled", false);
                    $('#purchase_product').attr("disabled", false);
                    $('#purchase_order').attr("disabled", false);
                    $('#purchase_receipt').attr("disabled", false);
                    $('#purchase_inventory').attr("disabled", false);
                    $('#supplier_payment').attr("disabled", false);
                    $('#product_item').attr("disabled", false);
                    $('#product').attr("disabled", false);
                } else {
                    $('#supplier').attr("disabled", true);
                    $('#purchase_product').attr("disabled", true);
                    $('#purchase_order').attr("disabled", true);
                    $('#purchase_receipt').attr("disabled", true);
                    $('#purchase_inventory').attr("disabled", true);
                    $('#supplier_payment').attr("disabled", true);
                    $('#product_item').attr("disabled", true);
                    $('#product').attr("disabled", true);
                }
            });

            // Sale
            $('#sale').click(function () {
                if ($(this).prop('checked')) {
                    $('#flat').attr("disabled", false);
                    $('#client').attr("disabled", false);
                    $('#sales_order').attr("disabled", false);
                    $('#sale_receipt').attr("disabled", false);
                    $('#customer_payment').attr("disabled", false);
                    $('#inventory').attr("disabled", false);
                    $('#customer').attr("disabled", false);
                    $('#sub_customer').attr("disabled", false);
                } else {
                    $('#flat').attr("disabled", true);
                    $('#client').attr("disabled", true);
                    $('#sales_order').attr("disabled", true);
                    $('#sale_receipt').attr("disabled", true);
                    $('#customer_payment').attr("disabled", true);
                    $('#inventory').attr("disabled", true);
                    $('#customer').attr("disabled", true);
                    $('#sub_customer').attr("disabled", true);
                }
            });
            // Loan
            $('#loan').click(function () {
                if ($(this).prop('checked')) {
                    $('#loan_holder').attr("disabled", false);
                    $('#Loan_transaction').attr("disabled", false);
                } else {
                    $('#loan_holder').attr("disabled", true);
                    $('#Loan_transaction').attr("disabled", true);
                }
            });

            // Accounts
            $('#accounts').click(function () {
                if ($(this).prop('checked')) {
                    $('#account_head_type').attr("disabled", false);
                    $('#account_head_sub_type').attr("disabled", false);
                    $('#project_wise_transaction').attr("disabled", false);
                    $('#balance_transfer').attr("disabled", false);
                } else {
                    $('#account_head_type').attr("disabled", true);
                    $('#account_head_sub_type').attr("disabled", true);
                    $('#project_wise_transaction').attr("disabled", true);
                    $('#balance_transfer').attr("disabled", true);
                }
            });

            // Report
            $('#report').click(function () {
                if ($(this).prop('checked')) {
                    $('#client_report').attr("disabled", false);
                    $('#client_summary').attr("disabled", false);
                    $('#sub_client_summary').attr("disabled", false);
                    $('#project_report').attr("disabled", false);
                    $('#project_head_report').attr("disabled", false);
                    $('#balance').attr("disabled", false);
                    $('#supplier_report').attr("disabled", false);
                    $('#purchase_report').attr("disabled", false);
                    $('#sale_report').attr("disabled", false);
                    $('#balance_summary').attr("disabled", false);
                    $('#profit_and_loss').attr("disabled", false);
                    $('#ledger').attr("disabled", false);
                    $('#price_with_stock').attr("disabled", false);
                    $('#price_without_stock').attr("disabled", false);
                    $('#cashbook').attr("disabled", false);
                    $('#balance_sheet').attr("disabled", false);
                    $('#monthly_expenditure').attr("disabled", false);
                    $('#bank_statement').attr("disabled", false);
                    $('#receive_and_payment').attr("disabled", false);
                } else {
                    $('#client_report').attr("disabled", true);
                    $('#client_summary').attr("disabled", true);
                    $('#sub_client_summary').attr("disabled", true);
                    $('#project_report').attr("disabled", true);
                    $('#project_head_report').attr("disabled", true);
                    $('#balance').attr("disabled", true);
                    $('#supplier_report').attr("disabled", true);
                    $('#purchase_report').attr("disabled", true);
                    $('#sale_report').attr("disabled", true);
                    $('#balance_summary').attr("disabled", true);
                    $('#profit_and_loss').attr("disabled", true);
                    $('#ledger').attr("disabled", true);
                    $('#price_with_stock').attr("disabled", true);
                    $('#price_without_stock').attr("disabled", true);
                    $('#cashbook').attr("disabled", true);
                    $('#balance_sheet').attr("disabled", true);
                    $('#monthly_expenditure').attr("disabled", true);
                    $('#bank_statement').attr("disabled", true);
                    $('#receive_and_payment').attr("disabled", true);
                }
            });

            // User Management
            $('#user_management').click(function () {
                if ($(this).prop('checked')) {
                    $('#users').attr("disabled", false);
                } else {
                    $('#users').attr("disabled", true);
                }
            });

            init();
        });

        function init() {
            if (!$('#administrator').prop('checked')) {
                $('#unit').attr("disabled", true);
                $('#terms_condition').attr("disabled", true);
                $('#project').attr("disabled", true);
                $('#partner').attr("disabled", true);
                $('#warehouse').attr("disabled", true);
                $('#department').attr("disabled", true);
                $('#designation').attr("disabled", true);
            }

            if (!$('#bank_and_account').prop('checked')) {
                $('#bank').attr("disabled", true);
                $('#branch').attr("disabled", true);
                $('#account').attr("disabled", true);
                $('#cash').attr("disabled", true);
            }

            if (!$('#hr').prop('checked')) {
                $('#employee').attr("disabled", true);
                $('#employee_list').attr("disabled", true);
                $('#employee_attendance').attr("disabled", true);
            }

            if (!$('#payroll').prop('checked')) {
                $('#salary_update').attr("disabled", true);
                $('#salary_process').attr("disabled", true);
                $('#leave').attr("disabled", true);
                $('#holiday').attr("disabled", true);
            }

            if (!$('#crm').prop('checked')) {
                $('#marketing').attr("disabled", true);
                $('#employee_marketing').attr("disabled", true);
                $('#employee_work_orders').attr("disabled", true);
                $('#report_monthly_crm').attr("disabled", true);
            }

            if (!$('#proposal').prop('checked')) {
                $('#proposal_create').attr("disabled", true);
                $('#proposal_edit').attr("disabled", true);
                $('#proposals').attr("disabled", true);
                $('#all_proposals').attr("disabled", true);
            }

            if (!$('#supplier_charge').prop('checked')) {
                $('#supplier_charge_edit').attr("disabled", true);
            }

            if (!$('#purchase').prop('checked')) {
                $('#supplier').attr("disabled", true);
                $('#purchase_product').attr("disabled", true);
                $('#purchase_order').attr("disabled", true);
                $('#purchase_receipt').attr("disabled", true);
                $('#purchase_inventory').attr("disabled", true);
                $('#supplier_payment').attr("disabled", true);
                $('#product_item').attr("disabled", true);
                $('#product').attr("disabled", true);
            }

            if (!$('#sale').prop('checked')) {
                $('#flat').attr("disabled", true);
                $('#client').attr("disabled", true);
                $('#sales_order').attr("disabled", true);
                $('#sale_receipt').attr("disabled", true);
                $('#customer_payment').attr("disabled", true);
                $('#inventory').attr("disabled", true);
                $('#customer').attr("disabled", true);
                $('#sub_customer').attr("disabled", true);
            }

            if (!$('#accounts').prop('checked')) {
                $('#account_head_type').attr("disabled", true);
                $('#account_head_sub_type').attr("disabled", true);
                $('#project_wise_transaction').attr("disabled", true);
                $('#balance_transfer').attr("disabled", true);
            }
            if (!$('#loan').prop('checked')) {
                $('#loan_holder').attr("disabled", true);
                $('#Loan_transaction').attr("disabled", true);
            }

            if (!$('#report').prop('checked')) {
                $('#client_report').attr("disabled", true);
                $('#client_summary').attr("disabled", true);
                $('#sub_client_summary').attr("disabled", true);
                $('#project_report').attr("disabled", true);
                $('#project_head_report').attr("disabled", true);
                $('#balance').attr("disabled", true);
                $('#supplier_report').attr("disabled", true);
                $('#purchase_report').attr("disabled", true);
                $('#sale_report').attr("disabled", true);
                $('#balance_summary').attr("disabled", true);
                $('#profit_and_loss').attr("disabled", true);
                $('#ledger').attr("disabled", true);
                $('#price_with_stock').attr("disabled", true);
                $('#price_without_stock').attr("disabled", true);
                $('#cashbook').attr("disabled", true);
                $('#balance_sheet').attr("disabled", true);
                $('#monthly_expenditure').attr("disabled", true);
                $('#bank_statement').attr("disabled", true);
                $('#receive_and_payment').attr("disabled", true);
            }

            if (!$('#user_management').prop('checked')) {
                $('#users').attr("disabled", true);
            }
        }
    </script>
@endsection
