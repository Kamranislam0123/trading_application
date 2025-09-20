@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header-section {
            margin-bottom: 30px;
        }

        .company-logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
            margin-left: 20px;
            border-radius: 50%;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            padding: 10px 0;
        }

        .company-details {
            font-size: 11px;
            line-height: 1.3;
            margin-top: 40px;
            padding-bottom: 10px;
        }

        .company-details p {
            margin: 2px 0;
        }

        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            border: 1px solid black !important;
        }

        .table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 8px;
            text-align: left;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #ddd;
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .employee-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .employee-header {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
        }

        .customer-section {
           
            margin-bottom: 20px;
        }

        .customer-header {
            background-color: #e9ecef;
            padding: 8px;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
        }

      

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
@endsection

@section('title')
    Employee Target Customer Wise Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Filter</h3>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <form action="{{ route('report.employee_target_customer_wise') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sales Person</label>
                                    <select class="form-control select2" name="employee_id">
                                        <option value="">All Sales Persons</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select class="form-control select2" name="customer_id">
                                        <option value="">All Customers</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Year</label>
                                    <select class="form-control" name="year">
                                        @for($i=2020; $i <= date('Y') + 1; $i++)
                                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Generate Report</button>
                                    <button type="button" class="btn btn-success" onclick="window.print()">Print</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="printableArea">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="header-section">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 class="document-title">Sales Target Customer Wise Report</h2>
                                <div class="company-details">
                                    <p><strong>Year:</strong> {{ $year }}</p>
                                    @if($employeeId)
                                        <p><strong>Sales Person:</strong> {{ $employees->where('id', $employeeId)->first()->name ?? 'N/A' }}</p>
                                    @endif
                                    @if($customerId)
                                        <p><strong>Customer:</strong> {{ $customers->where('id', $customerId)->first()->name ?? 'N/A' }}</p>
                                    @endif
                                    <p><strong>Generated Date:</strong> {{ date('d F, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($targets->count() > 0)
                        @foreach($targets->groupBy('employee_id') as $employeeId => $employeeTargets)
                            @php
                                $employee = $employeeTargets->first()->employee;
                                $employeeCustomers = $employeeCustomers[$employeeId] ?? collect();
                            @endphp
                            
                            <div class="employee-section">
                                <div class="employee-header">
                                    <h4><strong>Sales Person:</strong> {{ $employee->name ?? 'N/A' }}</h4>
                                    <p><strong>Sales Person ID:</strong> {{ $employee->employee_id ?? 'N/A' }}</p>
                                </div>

                                @if($employeeCustomers->count() > 0)
                                    @foreach($employeeCustomers as $customer)
                                        <div class="customer-section">
                                            <div class="customer-header">
                                                <h5><strong>Customer:</strong> {{ $customer->name }}</h5>
                                                <p><strong>Mobile:</strong> {{ $customer->mobile_no ?? 'N/A' }} | 
                                                   <strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
                                            </div>

                                            <div class="target-details">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">From Date</th>
                                                            <th class="text-center">To Date</th>
                                                            <th class="text-center">Target Amount</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Created Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $customerTargets = $employeeTargets->where('employee_id', $employeeId);
                                                            $totalTargetAmount = 0;
                                                        @endphp
                                                        
                                                        @if($customerTargets->count() > 0)
                                                            @foreach($customerTargets as $target)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        {{ $target->from_date ? \Carbon\Carbon::parse($target->from_date)->format('d F, Y') : 'N/A' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $target->to_date ? \Carbon\Carbon::parse($target->to_date)->format('d F, Y') : 'N/A' }}
                                                                    </td>
                                                                    <td class="text-right">৳{{ number_format($target->amount, 2) }}</td>
                                                                    <td class="text-center">
                                                                        @if($target->status == 1)
                                                                            <span class="label label-success">Active</span>
                                                                        @else
                                                                            <span class="label label-danger">Inactive</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $target->created_at->format('d F, Y') }}
                                                                    </td>
                                                                </tr>
                                                                @php $totalTargetAmount += $target->amount; @endphp
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="5" class="text-center">No targets found for this customer</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="2" class="text-right">Total Target Amount:</th>
                                                            <th class="text-right">৳{{ number_format($totalTargetAmount, 2) }}</th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="customer-section">
                                        <div class="no-data">
                                            <p>No customers assigned to this employee</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">
                            <h4>No Data Found</h4>
                            <p>No employee targets found for the selected criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>
@endsection
