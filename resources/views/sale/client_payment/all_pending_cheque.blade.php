@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .table-responsive table {
            min-width: 100%;
            white-space: nowrap;
        }
        
        .table-responsive th,
        .table-responsive td {
            min-width: 120px;
            padding: 8px 12px;
            vertical-align: middle;
        }
        
        .table-responsive th:first-child,
        .table-responsive td:first-child {
            min-width: 100px;
        }
        
        .table-responsive th:last-child,
        .table-responsive td:last-child {
            min-width: 150px;
        }
        
        /* Sticky first column */
        .table-responsive th:first-child,
        .table-responsive td:first-child {
            position: sticky;
            left: 0;
            background-color: #f9f9f9;
            z-index: 10;
            border-right: 2px solid #ddd;
        }
        
        .table-responsive thead th:first-child {
            background-color: #f5f5f5;
        }
        
        /* Scroll indicators */
        .table-responsive {
            position: relative;
        }
        
        .table-responsive::before,
        .table-responsive::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 20px;
            pointer-events: none;
            z-index: 15;
            transition: opacity 0.3s ease;
        }
        
        .table-responsive::before {
            left: 0;
            background: linear-gradient(to right, rgba(255,255,255,0.9), transparent);
            opacity: 0;
        }
        
        .table-responsive::after {
            right: 0;
            background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
            opacity: 0;
        }
        
        .table-responsive.scrolled-left::before {
            opacity: 1;
        }
        
        .table-responsive.scrolled-right::after {
            opacity: 1;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive th,
            .table-responsive td {
                min-width: 100px;
                padding: 6px 8px;
                font-size: 12px;
            }
            
            .table-responsive th:first-child,
            .table-responsive td:first-child {
                min-width: 80px;
            }
        }
        
        /* Search form styling */
        .search-form .form-control {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .search-form .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .search-form .btn {
            transition: all 0.15s ease-in-out;
        }
        
        .search-form .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .search-form .input-group-addon {
            transition: background-color 0.15s ease-in-out;
        }
        
        .search-form .input-group-addon:hover {
            background-color: #dee2e6;
        }
        
        /* Success message styling */
        #success-message {
            border-radius: 4px;
            border: 1px solid #d4edda;
            background-color: #d1edff;
            color: #0c5460;
            padding: 12px 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        #success-message .close {
            color: #0c5460;
            opacity: 0.7;
            font-size: 18px;
        }
        
        #success-message .close:hover {
            opacity: 1;
        }
        
        /* Payment mode indicator styling */
        .payment-mode-indicator {
            border-radius: 4px;
            border: 1px solid #bee5eb;
            background-color: #d1ecf1;
            color: #0c5460;
            margin-bottom: 15px;
            padding: 8px 12px;
            font-size: 12px;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Form field styling for different modes */
        .form-group label {
            transition: color 0.3s ease;
        }
        
        .date-update-mode .form-group label[for="modal-receive-amount"] {
            color: #6c757d;
        }
        
        .date-update-mode #modal-receive-amount {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .partial-payment-mode {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
        }
        
        .partial-payment-mode .payment-mode-indicator {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
    </style>
@endsection

@section('title')
{{--    Payments of {{ $payment->customer->name }}--}}
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" id="success-message" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">All Pending  Due Entry</h3>
                    <div class="box-tools pull-right">
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> Scroll horizontally to view all columns
                        </small>
                    </div>
                </div>
                
                <!-- Search Filter Form -->
                <div class="box-body" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 25px; margin-left: 10px; margin-right: 10px;">
                    <form method="GET" action="{{ route('client_payment.all_pending_check') }}" class="form-horizontal search-form">
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-3" style="padding-right: 25px;">
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label class="control-label" style="font-weight: 600; color: #495057; margin-bottom: 10px;">Customer Name</label>
                                    <select class="form-control select2" name="customer_id" id="customer_id" style="border-radius: 4px; border: 1px solid #ced4da;">
                                        <option value="">All Customers</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-right: 25px;">
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label class="control-label" style="font-weight: 600; color: #495057; margin-bottom: 10px;">Invoice No</label>
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no" 
                                           value="{{ request('invoice_no') }}" placeholder="Enter Invoice No"
                                           style="border-radius: 4px; border: 1px solid #ced4da;">
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-right: 25px;">
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label class="control-label" style="font-weight: 600; color: #495057; margin-bottom: 10px;">Date From</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control" name="date_from" id="date_from" 
                                               value="{{ request('date_from') }}" autocomplete="off"
                                               style="border-radius: 4px 0 0 4px; border: 1px solid #ced4da; border-right: none;">
                                        <div class="input-group-addon" style="background-color: #e9ecef; border: 1px solid #ced4da; border-left: none; border-radius: 0 4px 4px 0;">
                                            <i class="fa fa-calendar" style="color: #6c757d;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-right: 25px;">
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label class="control-label" style="font-weight: 600; color: #495057; margin-bottom: 10px;">Date To</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control" name="date_to" id="date_to" 
                                               value="{{ request('date_to') }}" autocomplete="off"
                                               style="border-radius: 4px 0 0 4px; border: 1px solid #ced4da; border-right: none;">
                                        <div class="input-group-addon" style="background-color: #e9ecef; border: 1px solid #ced4da; border-left: none; border-radius: 0 4px 4px 0;">
                                            <i class="fa fa-calendar" style="color: #6c757d;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label class="control-label" style="font-weight: 600; color: #495057; margin-bottom: 10px;">&nbsp;</label>
                                    <div style="display: flex; gap: 15px; align-items: flex-end; height: 34px;">
                                        <button type="submit" class="btn btn-primary" style="border-radius: 4px; padding: 8px 20px; font-weight: 500;">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                        
                                        <a href="{{ route('client_payment.all_pending_check') }}" class="btn btn-default" style="border-radius: 4px; padding: 8px 20px; font-weight: 500; border: 1px solid #ced4da; background-color: #fff; color: #495057;">
                                            <i class="fa fa-refresh"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    @if(request()->hasAny(['customer_id', 'invoice_no', 'date_from', 'date_to']))
                        <div class="alert alert-info" style="border-radius: 4px; border: 1px solid #b8daff; background-color: #d1ecf1; color: #0c5460; padding: 12px 16px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span>
                                    <i class="fa fa-info-circle"></i> 
                                    Showing {{ $payments->total() }} result(s) for your search criteria.
                                </span>
                                <a href="{{ route('client_payment.all_pending_check') }}" class="btn btn-xs btn-default" style="border-radius: 3px; padding: 4px 8px; font-size: 11px; border: 1px solid #6c757d; background-color: #fff; color: #495057;">
                                    <i class="fa fa-times"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Payment Method</th>
                                <th>Opening Due</th>
                                <th>Total Amount</th>
                                <th>Receive Amount</th>
                                <th>Due Amount</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Action</th>
                                {{-- <th>Branch</th> --}}
                                {{-- <th>Amount</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('Y-m-d') }}</td>
                                    <td>{{ $payment->invoice_no ?? 'N/A' }}</td>
                                    <td>{{ $payment->customer->name??'' }}</td>
                                    <td>{{ $payment->transaction_method==1?'Cash':"Bank" }}</td>
                                    <td>{{ number_format($payment->opening_due_amount ?? 0, 2) }}</td>
                                    <td>{{ number_format($payment->total_sales_amount ?? 0, 2) }}</td>
                                    <td>{{ number_format($payment->total_received_amount ?? 0, 2) }}</td>
                                    <td>{{ number_format($payment->due_amount ?? 0, 2) }}</td>
                                    <td>{{ $payment->note }}</td>
                                    {{-- <td>
                                        @if ($payment->company_branch_id == 1)
                                            Your Choice
                                        @elseif($payment->company_branch_id == 2)
                                            Your Choice Plus
                                        @else
                                            Admin
                                        @endif
                                    </td> --}}
                                    {{-- <td>{{ number_format($payment->amount * nbrCalculation(),2) }}</td> --}}
                                    <td>
                                        {{-- Display Next Payment Date from database --}}
                                        @php
                                            // Prioritize next_approximate_payment_date if available, otherwise use next_payment_date
                                            $nextPaymentDate = $payment->next_approximate_payment_date ?? $payment->next_payment_date;
                                        @endphp
                                        
                                        @if($nextPaymentDate)
                                            @php
                                                // Format the date to show only date part (remove time if present)
                                                $formattedNextPaymentDate = $nextPaymentDate;
                                                if (is_string($nextPaymentDate) && strpos($nextPaymentDate, ' ') !== false) {
                                                    $formattedNextPaymentDate = explode(' ', $nextPaymentDate)[0];
                                                } elseif ($nextPaymentDate instanceof \DateTime) {
                                                    $formattedNextPaymentDate = $nextPaymentDate->format('Y-m-d');
                                                }
                                                
                                                $dayBeforeNextPayment = date('Y-m-d', strtotime('-1 day', strtotime($formattedNextPaymentDate)));
                                            @endphp
                                            
                                            @if($currentDate == $dayBeforeNextPayment)
                                                {{-- Red color for day before due date --}}
                                                <span class="label label-danger" style="font-size: 14px">
                                                    Next Payment: {{ $formattedNextPaymentDate }}
                                                </span>
                                            @else
                                                {{-- Yellow color for normal status --}}
                                                <span class="label label-warning" style="font-size: 14px">
                                                    Next Payment: {{ $formattedNextPaymentDate }}
                                                </span>
                                            @endif
                                        @else
                                            {{-- Fallback when no next payment date is set --}}
                                            <span class="label label-warning" style="font-size: 14px">
                                                Next Payment: Not Set
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->status == 1)
                                            <a class="btn btn-info btn-sm" href="{{ route('sale_receipt.payment_details', $payment->id) }}"> Vouchar </a>
                                            
                                            @php
                                                $totalAmount = $payment->total_sales_amount ?? 0;
                                                $receivedAmount = $payment->receive_amount ?? 0;
                                                $isFullyPaid = ($receivedAmount >= $totalAmount);
                                            @endphp
                                            
                                            @if($isFullyPaid)
                                                {{-- Show Paid status and Pay button when fully paid --}}
                                                <span class="label label-success" style="font-size: 12px; margin-right: 5px;">Paid</span>
                                                <a class="btn btn-success btn-sm btn-pay" role="button" data-id="{{$payment->id}}"
                                                   data-name="{{$payment->customer->name}}"
                                                   data-sales-person="{{$payment->salesPerson->name ?? 'N/A'}}"
                                                   data-no="{{$payment->client_cheque_no}}"
                                                   data-date="{{$payment->cheque_date}}"
                                                   data-amount="{{$payment->client_amount * nbrCalculation()}}"
                                                   data-due-amount="{{$payment->due_amount ?? 0}}"
                                                   data-next-payment-date="{{$payment->next_payment_date ?? ''}}"
                                                   data-next-approximate-payment-date="{{$payment->next_approximate_payment_date ?? ''}}">Pay</a>
                                            @else
                                                {{-- Show Pending button when not fully paid --}}
                                                <a class="btn btn-warning btn-sm btn-pending" role="button" data-id="{{$payment->id}}"
                                                   data-name="{{$payment->customer->name}}"
                                                   data-sales-person="{{$payment->salesPerson->name ?? 'N/A'}}"
                                                   data-no="{{$payment->client_cheque_no}}"
                                                   data-date="{{$payment->cheque_date}}"
                                                   data-amount="{{$payment->client_amount * nbrCalculation()}}"
                                                   data-due-amount="{{$payment->due_amount ?? 0}}"
                                                   data-next-payment-date="{{$payment->next_payment_date ?? ''}}"
                                                   data-next-approximate-payment-date="{{$payment->next_approximate_payment_date ?? ''}}">Pending</a>
                                            @endif
                                            
{{--                                            <a class="btn btn-danger btn-sm btn-delete" role="button" data-id="{{$payment->id}}">Edit</a>--}}
                                            @if(Auth::user()->company_branch_id==0)
                                            <a class="btn btn-danger btn-sm btn-delete" role="button" data-id="{{$payment->id}}">Delete</a>
                                            @endif
                                        @else
                                            <a class="btn btn-info btn-sm" href="{{ route('sale_receipt.payment_details', $payment->id) }}"> Vouchar </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <p>{!! $payments->render() !!}</p>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline" id="modalBtnDelete">Delete</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-pay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Payment Information</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <input type="hidden" name="payment_id" id="payment_id">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>
                        <div class="form-group">
                            <label> Sales Person Name</label>
                            <input class="form-control" id="modal-bank-name" disabled>
                        </div>
                        <!-- <div class="form-group">
                            <label>Bank Cheque No</label>
                            <input class="form-control" id="modal-cheque-no" disabled>
                        </div> -->
                        <!-- <div class="form-group">
                            <label>Bank Cheque Date</label>
                            <input class="form-control" id="modal-cheque-date" disabled>
                        </div> -->
                        <!-- <div class="form-group">
                            <label>Cheque Amount</label>
                            <input class="form-control" id="modal-cheque-amount" disabled>
                        </div> -->
                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                            </select>
                        </div>

                        <div id="modal-bank-info">
                            <div class="form-group">
                                <label>Storage Bank</label>
                                <select class="form-control select2 modal-bank" name="bank">
                                    <option value="">Select Bank</option>

                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Storage Branch</label>
                                <select class="form-control select2 modal-branch" name="branch">
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Storage Account</label>
                                <select class="form-control select2 modal-account" name="account">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Storage Cheque No.</label>
                                <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No.">
                            </div>

                            <div class="form-group">
                                <label>Cheque Image</label>
                                <input class="form-control" name="cheque_image" type="file">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date" name="date"
                                       value="{{ date('Y-m-d') }}" autocomplete="off" readonly>
                            </div>
                            <!-- /.input group -->
                        </div>


                        
                        <div class="form-group">
                            <label>Next Payment Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="next_payment_date" name="next_payment_date"
                                       value="" autocomplete="off" readonly>
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Next Approximate Payment Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="next_approximate_payment_date" name="next_approximate_payment_date"
                                       value="" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Due Amount</label>
                            <input class="form-control" id="modal-due-amount" disabled>
                        </div>

                        <div class="form-group">
                            <label>Receive Amount</label>
                            <input class="form-control" id="modal-receive-amount" name="receive_amount" placeholder="Enter amount to receive" type="number" step="0.01" min="0">
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-approved">Submit</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        var due;
        $(function () {
            // Auto-dismiss success message
            if ($('#success-message').length) {
                $('#success-message').fadeIn(500);
                setTimeout(function() {
                    $('#success-message').fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 4000); // Auto-dismiss after 4 seconds
            }
            
            // Initialize Select2 for search form
            $('.select2').select2();
            
            //Date picker for search form
            $('#date_from, #date_to').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            });
            
            //Date picker for modal (only for next_approximate_payment_date)
            $('#next_approximate_payment_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                clearBtn: true
            });

            // Add scroll indicator
            $('.table-responsive').on('scroll', function() {
                var scrollLeft = $(this).scrollLeft();
                var scrollWidth = $(this)[0].scrollWidth;
                var clientWidth = $(this)[0].clientWidth;
                
                if (scrollLeft > 0) {
                    $(this).addClass('scrolled-left');
                } else {
                    $(this).removeClass('scrolled-left');
                }
                
                if (scrollLeft + clientWidth >= scrollWidth - 5) {
                    $(this).addClass('scrolled-right');
                } else {
                    $(this).removeClass('scrolled-right');
                }
            });

            var salePaymentId;

            $('body').on('click', '.btn-delete', function () {
                $('#modal-delete').modal('show');
                salePaymentId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('pending_cheque.delete') }}",
                    data: { id: salePaymentId }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            $('body').on('click', '.btn-pending, .btn-pay', function () {
                var paymentId = $(this).data('id');
                var clientName = $(this).data('name');
                var salesPersonName = $(this).data('sales-person');
                var clientChequeNo = $(this).data('no');
                var ChequeDate = $(this).data('date');
                var ChequeAmount = $(this).data('amount');
                var dueAmount = $(this).data('due-amount');
                var nextPaymentDate = $(this).data('next-payment-date');
                var nextApproximatePaymentDate = $(this).data('next-approximate-payment-date');
                
                $('#modal-name').val(clientName);
                $('#modal-bank-name').val(salesPersonName);
                $('#modal-cheque-no').val(clientChequeNo);
                $('#modal-cheque-amount').val(ChequeAmount);
                $('#modal-cheque-date').val(ChequeDate);
                $('#modal-due-amount').val(dueAmount);
                $('#modal-receive-amount').val(''); // Clear receive amount field
                $('#date').val('{{ date('Y-m-d') }}'); // Always set current date
                $('#next_payment_date').val(nextPaymentDate);
                // Format the date properly (remove time part if present)
                var formattedDate = nextApproximatePaymentDate;
                if (formattedDate && formattedDate.includes(' ')) {
                    formattedDate = formattedDate.split(' ')[0]; // Take only the date part
                }
                $('#next_approximate_payment_date').val(formattedDate);
                $('#payment_id').val(paymentId);
                
                // Initialize form mode
                updateFormMode();
                
                $('#modal-pay').modal('show');
            });

            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');

            $('#modal-order').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        due = parseFloat(response.due).toFixed(2);
                        $('#modal-order-info').html('<strong>Total: </strong>৳'+parseFloat(response.total).toFixed(2)+' <strong>Paid: </strong>৳'+parseFloat(response.paid).toFixed(2)+' <strong>Due: </strong>৳'+parseFloat(response.due).toFixed(2));
                        $('#modal-order-info').show();
                    });
                }
            });

            $('.modal-bank').change(function () {
                var bankId = $(this).val();
                $('.modal-branch').html('<option value="">Select Branch</option>');
                $('.modal-account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('.modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('.modal-branch').trigger('change');
                    });
                }

                $('.modal-branch').trigger('change');
            });

            $('.modal-branch').change(function () {
                var branchId = $(this).val();
                $('.modal-account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('.modal-account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });

            // Validate receive amount
            $('#modal-receive-amount').on('input', function() {
                var receiveAmount = parseFloat($(this).val()) || 0;
                var dueAmount = parseFloat($('#modal-due-amount').val()) || 0;
                
                if (receiveAmount > dueAmount) {
                    $(this).addClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                    $(this).after('<div class="invalid-feedback">Receive amount cannot exceed due amount</div>');
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            // Add visual indicator for form mode
            function updateFormMode() {
                var receiveAmount = parseFloat($('#modal-receive-amount').val()) || 0;
                var nextApproximateDate = $('#next_approximate_payment_date').val();
                
                // Remove existing mode classes
                $('#modal-pay .modal-body').removeClass('date-update-mode payment-mode partial-payment-mode');
                $('.payment-mode-indicator').remove();
                
                if (receiveAmount > 0 && nextApproximateDate) {
                    // Partial payment mode - both amount and date provided
                    $('#modal-receive-amount').attr('required', true);
                    $('#modal-btn-approved').before('<div class="payment-mode-indicator"><i class="fa fa-info-circle"></i> Partial payment mode: Payment will be processed but record will stay in due list until fully paid</div>');
                    $('#modal-btn-approved').text('Process Partial Payment').removeClass('btn-info').addClass('btn-warning');
                    $('#modal-pay .modal-body').addClass('partial-payment-mode');
                } else if (receiveAmount > 0) {
                    // Full payment mode - only amount provided
                    $('#modal-receive-amount').attr('required', true);
                    $('#modal-btn-approved').text('Submit Payment').removeClass('btn-info btn-warning').addClass('btn-primary');
                    $('#modal-pay .modal-body').addClass('payment-mode');
                } else if (nextApproximateDate) {
                    // Date update mode - only date provided
                    $('#modal-receive-amount').attr('required', false);
                    $('#modal-btn-approved').before('<div class="payment-mode-indicator"><i class="fa fa-info-circle"></i> Date update mode: Only updating the next payment date</div>');
                    $('#modal-btn-approved').text('Update Date').removeClass('btn-primary btn-warning').addClass('btn-info');
                    $('#modal-pay .modal-body').addClass('date-update-mode');
                } else {
                    // Default mode
                    $('#modal-receive-amount').attr('required', true);
                    $('#modal-btn-approved').text('Submit Payment').removeClass('btn-info btn-warning').addClass('btn-primary');
                }
            }

            // Monitor form changes to update mode
            $('#modal-receive-amount, #next_approximate_payment_date').on('input change', updateFormMode);

            $('#modal-btn-approved').click(function () {
                var receiveAmount = parseFloat($('#modal-receive-amount').val()) || 0;
                var dueAmount = parseFloat($('#modal-due-amount').val()) || 0;
                var nextApproximateDate = $('#next_approximate_payment_date').val();
                
                // Check if this is date update mode (no receive amount but has next approximate date)
                if (receiveAmount <= 0 && nextApproximateDate) {
                    // Date update mode - only update the next payment date
                    var formData = new FormData();
                    formData.append('payment_id', $('#payment_id').val());
                    formData.append('next_approximate_payment_date', nextApproximateDate);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    
                    $.ajax({
                        type: "POST",
                        url: "{{ route('client_payment.update_date_only') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#modal-pay').modal('hide');
                                Swal.fire(
                                    'Updated!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = 'An error occurred while updating the date.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 422) {
                                errorMessage = 'Validation error. Please check your input.';
                            } else if (xhr.status === 404) {
                                errorMessage = 'Payment record not found.';
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                            });
                        }
                    });
                    return;
                }
                
                // Payment mode - validate receive amount
                if (receiveAmount > dueAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Receive amount cannot exceed due amount',
                    });
                    return;
                }
                
                if (receiveAmount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a valid receive amount',
                    });
                    return;
                }
                
                // Check if this is partial payment (both amount and date provided)
                if (receiveAmount > 0 && nextApproximateDate) {
                    // Partial payment mode - create payment but keep in due list
                    var formData = new FormData($('#modal-form')[0]);
                    formData.append('is_partial_payment', '1');
                    formData.append('next_approximate_payment_date', nextApproximateDate);
                    
                    $.ajax({
                        type: "POST",
                        url: "{{ route('client_cheque.approved') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#modal-pay').modal('hide');
                                Swal.fire(
                                    'Partial Payment Processed!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message,
                                });
                            }
                        }
                    });
                    return;
                }
                
                // Full payment mode - proceed with normal payment flow
                var formData = new FormData($('#modal-form')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{ route('client_cheque.approved') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-pay').modal('hide');
                            Swal.fire(
                                'Approved!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
