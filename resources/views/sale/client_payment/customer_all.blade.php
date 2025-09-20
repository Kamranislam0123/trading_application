@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <style>
        .select2{width:100% !important;}
        .label {
            font-size: 11px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-width: 100%;
        }
        
        .table-responsive table {
            min-width: 1200px; /* Ensure minimum width for all columns */
            white-space: nowrap;
        }
        
        .table-responsive th,
        .table-responsive td {
            min-width: 100px;
            padding: 8px 12px;
            vertical-align: middle;
        }
        
        /* Action column should be wider */
        .table-responsive th:last-child,
        .table-responsive td:last-child {
            min-width: 200px;
        }
        
        /* Sales Person column */
        .table-responsive th:nth-child(4),
        .table-responsive td:nth-child(4) {
            min-width: 120px;
        }
        
        /* Customer Name column */
        .table-responsive th:first-child,
        .table-responsive td:first-child {
            min-width: 150px;
        }
        
        /* Address column - limit width and add ellipsis */
        .table-responsive th:nth-child(2),
        .table-responsive td:nth-child(2) {
            min-width: 150px;
            max-width: 200px;
            overflow: hidden;
            
            white-space: nowrap;
        }
        
        /* Mobile column */
        .table-responsive th:nth-child(3),
        .table-responsive td:nth-child(3) {
            min-width: 120px;
        }
        
        .label-success {
            background-color: #5cb85c;
            color: white;
        }
        .label-warning {
            background-color: #f0ad4e;
            color: white;
        }
    </style>
@endsection

@section('title')
    Customer payment
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <!-- Sorting Filters -->
                <div class="box-header with-border">
                    <h3 class="box-title">Customer Payments</h3>
                    <div class="box-tools pull-right">
                        <div class="form-inline" style="margin-top: 10px;">
                            <label style="margin-right: 10px; font-weight: 600;">Sort by Total Amount:</label>
                            <select id="total_amount_sort" class="form-control" style="display: inline-block; width: auto; min-width: 150px; margin-right: 15px;">
                                <option value="">Default Order</option>
                                <option value="low_to_high">Low to High</option>
                                <option value="high_to_low">High to Low</option>
                            </select>
                            
                            <label style="margin-right: 10px; font-weight: 600; margin-left: 20px;">Sort by Next Payment Date:</label>
                            <select id="next_payment_date_sort" class="form-control" style="display: inline-block; width: auto; min-width: 150px; margin-right: 15px;">
                                <option value="">Default Order</option>
                                <option value="newest_to_oldest">Newest to Oldest</option>
                                <option value="oldest_to_newest">Oldest to Newest</option>
                            </select>
                            
                            <button type="button" class="btn btn-info btn-sm" id="apply-sorting" style="margin-right: 5px;">
                                <i class="fa fa-sort"></i> Apply
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="clear-sorting">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table id="table" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>Sales Person</th>
                            {{-- <th>Branch</th> --}}
                            <th>Opening Due</th>
                            <th>Total</th>
                            <th>Return</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Next Payment Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
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
                        <input type="hidden" name="customer_id" id="customer_id">
                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>
                        <div class="form-group">
                            <label>Customer due</label>
                            <input class="form-control" id="modal-due" disabled>
                        </div>

                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Mobile Banking</option>
                                <option value="4">Sale Adjustment Discount</option>
                                <option value="5">Return Adjustment Amount</option>
                            </select>
                        </div>

                        <div id="modal-bank-info">
                            <div class="form-group">
                                <label>Bank</label>
                                <select class="form-control select2 modal-bank" name="bank">
                                    <option value="">Select Bank</option>

                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Branch</label>
                                <select class="form-control select2 modal-branch" name="branch">
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Account</label>
                                <select class="form-control select2 modal-account" name="account">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Cheque No.</label>
                                <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No.">
                            </div>

                            <div class="form-group">
                                <label>Cheque Image</label>
                                <input class="form-control" name="cheque_image" type="file">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" name="amount" id="amount" placeholder="Enter Amount">
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Next Payment Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="next_payment_date" name="next_payment_date" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-pay">Pay</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Return Modal -->
    <div class="modal fade" id="modal-return-customer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Return Payment</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form-return-customer" enctype="multipart/form-data" name="modal-form-return-customer">
                        <input type="hidden" name="customer_id" id="customer_id_return_customer">
                        
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input class="form-control" id="customer_name_return_customer" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label>Customer Due Amount</label>
                            <input class="form-control" id="customer_due_return_customer" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label>Return Amount <span class="text-danger">*</span></label>
                            <input class="form-control" name="return_amount" id="return_amount_customer" placeholder="Enter Return Amount" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Return Date <span class="text-danger">*</span></label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="return_date_customer" name="return_date"
                                       value="{{ date('Y-m-d') }}" autocomplete="off" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Return Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="return_reason" id="return_reason_customer" placeholder="Enter return reason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="modal-btn-return-customer">Process Return</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        var due;
        $(function () {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("client_payment.customer.datatable") }}',
                    data: function (d) {
                        d.employee_id = $('#employee_filter').val();
                        d.total_amount_sort = $('#total_amount_sort').val();
                        d.next_payment_date_sort = $('#next_payment_date_sort').val();
                        
                        // Debug: Log the data being sent
                        console.log('DataTable AJAX data:', {
                            employee_id: d.employee_id,
                            total_amount_sort: d.total_amount_sort,
                            next_payment_date_sort: d.next_payment_date_sort
                        });
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'employee', name: 'employee.name'},
                    {{-- {data: 'branch', name: 'branch'}, --}}
                    {data: 'opening_due', name: 'opening_due', orderable: false},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'return', name: 'return', orderable: false},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                    {data: 'next_payment_date', name: 'next_payment_date', orderable: false},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
            });

            // Add sales person filter before the search input
            var filterHtml = '<label style="margin-right: 10px;">Sales Person: </label><select id="employee_filter" class="form-control" style="display: inline-block; width: auto; min-width: 150px; margin-right: 15px;">';
            filterHtml += '<option value="">All</option>';
            @foreach($employees as $employee)
                filterHtml += '<option value="{{ $employee->id }}">{{ $employee->name }}</option>';
            @endforeach
            filterHtml += '</select>';
            
            $('.dataTables_filter').prepend(filterHtml);

            // Handle sales person filter change
            $('#employee_filter').on('change', function() {
                table.ajax.reload();
            });

            // Handle sorting (both total amount and next payment date)
            $('#apply-sorting').on('click', function() {
                console.log('Total amount sort:', $('#total_amount_sort').val());
                console.log('Next payment date sort:', $('#next_payment_date_sort').val());
                table.ajax.reload();
            });

            // Handle clear sorting
            $('#clear-sorting').on('click', function() {
                $('#total_amount_sort').val('');
                $('#next_payment_date_sort').val('');
                table.ajax.reload();
            });

            // Add tooltips for truncated addresses
            $('body').tooltip({
                selector: 'td:nth-child(2)',
                placement: 'top',
                trigger: 'hover'
            });

            $('.select2').select2();

            //Date picker
            $('#date, #next_payment_date, #return_date_customer').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            });

            $('body').on('click', '.btn-pay', function () {
                var clientId = $(this).data('id');
                var clientName = $(this).data('name');
                var clientDue = $(this).data('due');
                $('#customer_id').val(clientId);
                $('#modal-name').val(clientName);
                $('#modal-due').val(clientDue);
                $('#modal-pay').modal('show');

            });

            $('#modal-btn-pay').click(function () {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'

                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData($('#modal-form')[0]);

                        $.ajax({
                            type: "POST",
                            url: "{{ route('client_payment.make_payment') }}",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    $('#modal-pay').modal('hide');
                                    Swal.fire({
                                        title: 'Paid!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, // Auto close after 2 seconds
                                        showConfirmButton: false, // Hide the OK button
                                        timerProgressBar: true // Show progress bar
                                    });
                                    
                                    // Redirect after 2 seconds
                                    setTimeout(function() {
                                        window.location.href = response.redirect_url;
                                    }, 2000);

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.message,
                                    });
                                }
                            }
                        });
                    }
                })
            });

            {{--$('#modal-btn-pay').click(function () {--}}
            {{--    var formData = new FormData($('#modal-form')[0]);--}}

            {{--    $.ajax({--}}
            {{--        type: "POST",--}}
            {{--        url: "{{ route('client_payment.make_payment') }}",--}}
            {{--        data: formData,--}}
            {{--        processData: false,--}}
            {{--        contentType: false,--}}
            {{--        success: function(response) {--}}
            {{--            if (response.success) {--}}
            {{--                $('#modal-pay').modal('hide');--}}
            {{--                Swal.fire(--}}
            {{--                    'Paid!',--}}
            {{--                    response.message,--}}
            {{--                    'success'--}}
            {{--                ).then((result) => {--}}
            {{--                    //location.reload();--}}
            {{--                    window.location.href = response.redirect_url;--}}
            {{--                });--}}
            {{--            } else {--}}
            {{--                Swal.fire({--}}
            {{--                    icon: 'error',--}}
            {{--                    title: 'Oops...',--}}
            {{--                    text: response.message,--}}
            {{--                });--}}
            {{--            }--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1'|| $(this).val() == '4' || $(this).val() == '5') {
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

            checkNextPayment();
            $('#amount').keyup(function () {
                checkNextPayment();
            });

            // Return functionality for customer table
            $('body').on('click', '.btn-return-customer', function () {
                var customerId = $(this).data('id');
                var customerName = $(this).data('name');
                var customerDue = $(this).data('due');
                
                $('#customer_id_return_customer').val(customerId);
                $('#customer_name_return_customer').val(customerName);
                $('#customer_due_return_customer').val(customerDue);
                $('#return_amount_customer').val('');
                $('#return_reason_customer').val('');
                $('#modal-return-customer').modal('show');
            });

            $('#modal-btn-return-customer').click(function () {
                var formData = new FormData($('#modal-form-return-customer')[0]);
                var returnAmount = parseFloat($('#return_amount_customer').val());

                // Validate return amount
                if (returnAmount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Return amount must be greater than 0',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to process a return of ৳" + returnAmount.toFixed(2),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Process Return'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('client_payment.return_customer') }}",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    $('#modal-return-customer').modal('hide');
                                    Swal.fire({
                                        title: 'Return Processed!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, // Auto close after 2 seconds
                                        showConfirmButton: false, // Hide the OK button
                                        timerProgressBar: true // Show progress bar
                                    });
                                    
                                    // Reload page after 2 seconds
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = 'An error occurred while processing the return';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage,
                                });
                            }
                        });
                    }
                });
            });
        });

        function checkNextPayment() {
            var paid = $('#amount').val();

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;

            if (parseFloat(paid) >= due)
                $('#fg-next-payment-date').hide();
            else
                $('#fg-next-payment-date').show();
        }
    </script>
@endsection
