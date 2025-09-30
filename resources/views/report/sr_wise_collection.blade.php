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

        .summary-section {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-label {
            font-weight: bold;
            color: #495057;
        }

        .summary-value {
            font-weight: bold;
            color: #007bff;
        }

        .employee-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .employee-header {
            background-color: #e9ecef;
            padding: 15px;
            border: 1px solid #ced4da;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .achievement-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .achievement-excellent {
            background-color: #d4edda;
            color: #155724;
        }

        .achievement-good {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .achievement-average {
            background-color: #fff3cd;
            color: #856404;
        }

        .achievement-poor {
            background-color: #f8d7da;
            color: #721c24;
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
    SR Wise Collection Report
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
                    <form action="{{ route('report.sr_wise_collection') }}" method="GET">
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
                            <div class="col-md-2">
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
                            </div>
                            <div class="col-md-5">
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
                                <h2 class="document-title">SR Wise Collection Report</h2>
                                <div class="company-details">
                                    @if($fromDate || $toDate)
                                        <p><strong>Date Range:</strong> 
                                            {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d F, Y') : 'Start' }} 
                                            to 
                                            {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d F, Y') : 'End' }}
                                        </p>
                                    @endif
                                    @if($employeeId)
                                        <p><strong>Sales Person:</strong> {{ $employees->where('id', $employeeId)->first()->name ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section">
                        <h4 style="margin-top: 0; color: #495057;">Summary</h4>
                        <div class="summary-row">
                            <span class="summary-label">Total Target Amount:</span>
                            <span class="summary-value">৳{{ number_format($totalTargetAmount * nbrCalculation(), 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Total Collection Amount:</span>
                            <span class="summary-value">৳{{ number_format($totalCollectionAmount * nbrCalculation(), 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Overall Achievement:</span>
                            <span class="summary-value">
                                @php
                                    $overallAchievement = $totalTargetAmount > 0 ? ($totalCollectionAmount / $totalTargetAmount) * 100 : 0;
                                @endphp
                                {{ number_format($overallAchievement, 2) }}%
                            </span>
                        </div>
                    </div>

                    <!-- Employee Collection Details -->
                    @if($collectionData->count() > 0)
                        @foreach($collectionData as $data)
                            <div class="employee-section">
                                <div class="employee-header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 style="margin: 0; color: #495057;">
                                                <strong>{{ $data['employee']->name ?? 'N/A' }}</strong>
                                            </h5>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <span class="achievement-badge 
                                                @if($data['achievement_percentage'] >= 100) achievement-excellent
                                                @elseif($data['achievement_percentage'] >= 80) achievement-good
                                                @elseif($data['achievement_percentage'] >= 60) achievement-average
                                                @else achievement-poor
                                                @endif">
                                                {{ number_format($data['achievement_percentage'], 1) }}% Achievement
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-md-4">
                                            <strong>Target:</strong> ৳{{ number_format($data['target_amount'] * nbrCalculation(), 2) }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Collection:</strong> ৳{{ number_format($data['collection_amount'] * nbrCalculation(), 2) }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Target Period:</strong> 
                                            {{ $data['target']->from_date ? \Carbon\Carbon::parse($data['target']->from_date)->format('d M Y') : 'N/A' }} - 
                                            {{ $data['target']->to_date ? \Carbon\Carbon::parse($data['target']->to_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                @if($data['collection_details']->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">SL</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Customer Name</th>
                                                    <th class="text-center">Received Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $serialNumber = 1;
                                                @endphp
                                                @foreach($data['collection_details'] as $detail)
                                                    @foreach($detail['payments'] as $payment)
                                                        <tr>
                                                            <td class="text-center">{{ $serialNumber++ }}</td>
                                                            <td class="text-center">{{ \Carbon\Carbon::parse($payment->date)->format('d-m-Y') }}</td>
                                                            <td>{{ $detail['customer']->name ?? 'N/A' }}</td>
                                                            <td class="text-right">৳{{ number_format($payment->amount * nbrCalculation(), 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="no-data">
                                        No collection data found for this sales person in the selected period.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">
                            No collection data found for the selected criteria.
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

        function resetFilters() {
            // Clear all form fields
            $('select[name="employee_id"]').val('').trigger('change');
            $('input[name="from_date"]').val('');
            $('input[name="to_date"]').val('');
            
            // Reload the page without any filters
            window.location.href = "{{ route('report.sr_wise_collection') }}";
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
