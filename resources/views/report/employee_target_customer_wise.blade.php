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
            padding: 0;
        }

        .content-wrapper {
            margin-left: 230px !important;
            padding: 15px !important;
        }

        .content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .box {
            margin-bottom: 0;
        }

        /* Responsive handling for smaller screens */
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0 !important;
            }
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

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .main-footer {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .header-section {
                margin-bottom: 20px;
            }
        }
    </style>
@endsection

@section('title')
    Employee Target Customer Wise Report
@endsection

@section('content')
    <div class="row no-print">
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
                            <!-- <div class="col-md-3">
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
                            </div> -->
                            <!-- <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" class="form-control" name="from_date" value="{{ $fromDate ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" class="form-control" name="to_date" value="{{ $toDate ?? '' }}">
                                </div>
                            </div> -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sort by Due Amount:</label>
                                    <select class="form-control" name="sort_by_amount">
                                        <option value="">Default Order</option>
                                        <option value="low_to_high" {{ $sortByAmount == 'low_to_high' ? 'selected' : '' }}>Low to High</option>
                                        <option value="high_to_low" {{ $sortByAmount == 'high_to_low' ? 'selected' : '' }}>High to Low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sort by Date:</label>
                                    <select class="form-control" name="sort_by_date">
                                        <option value="">Default Order</option>
                                        <option value="newest" {{ $sortByDate == 'newest' ? 'selected' : '' }}>Newest</option>
                                        <option value="oldest" {{ $sortByDate == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <button type="submit" class="btn btn-primary">Generate Report</button>
                                        <button type="button" class="btn btn-warning" onclick="resetFilters()">Reset</button>
                                        <button type="button" class="btn btn-success" onclick="printReport()">Print</button>
                                    </div>
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
                                <h2 class="document-title">Sr Wise Due list</h2>
                                <div class="company-details">
                                    <!-- @if($fromDate || $toDate)
                                        <p><strong>Date Range:</strong> 
                                            {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d F, Y') : 'Start' }} 
                                            to 
                                            {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d F, Y') : 'End' }}
                                        </p>
                                    @endif -->
                                    @if($sortByAmount)
                                        <!-- <p><strong>Sort Order:</strong>  -->
                                            <!-- {{ ucwords(str_replace('_', ' ', $sortByAmount)) }} -->
                                        </p>
                                    @endif
                                    @if($customerId)
                                        <p><strong>Customer:</strong> {{ $customers->where('id', $customerId)->first()->name ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Sales Person</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Current Due</th>
                                    <th class="text-center">Last Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($targets->count() > 0)
                                    @php
                                        $serialNumber = 1;
                                        $totalDue = 0;
                                    @endphp
                                    @foreach($targets as $target)
                                        @php
                                            $employee = $target->employee;
                                            $customer = $target->customer;
                                            
                                            // Get customer's current due amount
                                            $customerDue = $customer ? $customer->due : 0;
                                            $totalDue += $customerDue;
                                            
                                            // Get the last payment date for this customer
                                            $lastPayment = \App\Model\SalePayment::where('customer_id', $customer->id)
                                                ->whereIn('status', [1, 2]) // Both pending and approved payments
                                                ->orderBy('date', 'desc')
                                                ->first();
                                            
                                            $lastPaymentDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->date)->format('d-m-Y') : 'No Payment';
                                            
                                            // Add payment count information
                                            $paymentCount = \App\Model\SalePayment::where('customer_id', $customer->id)->whereIn('status', [1, 2])->count();
                                            if ($paymentCount > 0) {
                                                $lastPaymentDate .= " ({$paymentCount} payments)";
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $serialNumber++ }}</td>
                                            <td>{{ $employee->name ?? 'N/A' }}</td>
                                            <td>{{ $customer->name ?? 'N/A' }}</td>
                                            <td class="text-center">৳{{ number_format($customerDue * nbrCalculation(), 2) }}</td>
                                            <td class="text-center">{{ $lastPaymentDate }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="5" style="padding: 20px; color: #666; font-style: italic;">
                                            No employee targets found for the selected criteria.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            @if($targets->count() > 0)
                                <tfoot>
                                    <tr>
                                        <th class="text-center" colspan="3">Total Due Amount</th>
                                        <th class="text-center">৳{{ number_format($totalDue * nbrCalculation(), 2) }}</th>
                                        <th class="text-center">-</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
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

        function resetFilters() {
            // Clear all form fields
            $('select[name="employee_id"]').val('').trigger('change');
            $('select[name="customer_id"]').val('').trigger('change');
            // $('input[name="from_date"]').val('');
            // $('input[name="to_date"]').val('');
            $('select[name="sort_by_amount"]').val('');
            $('select[name="sort_by_date"]').val('');
            
            // Reload the page without any filters
            window.location.href = "{{ route('report.employee_target_customer_wise') }}";
        }

        function printReport() {
            // Hide filter section temporarily
            $('.no-print').hide();
            
            // Print the page
            window.print();
            
            // Show filter section again after printing
            $('.no-print').show();
        }
    </script>
@endsection
