@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Payments of {{ $customer->name }}
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
                <div class="box-body table-responsive">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                {{-- <th>Branch</th> --}}
                                <th>Payment Method</th>
                                <th>Received Amount</th>
                                <th>Note</th>
                                <th>Received by</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('Y-m-d') }}</td>
                                    <td>{{ $payment->customer->name??'' }}</td>
                                    {{-- <td>
                                        @if($payment->company_branch_id == 1)
                                            Your Choice
                                        @elseif($payment->company_branch_id == 2)
                                            Your Choice Plus
                                        @else
                                            Admin Payment
                                        @endif
                                    </td> --}}
                                    <td>
                                        @if($payment->transaction_method == 5)
                                            Return Adjustment
                                        @else
                                            {{ ($payment->transaction_method==1?'Cash':($payment->transaction_method == 4?'Sale Adjustment':'Bank')) }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($payment->receive_amount ?? $payment->amount,2) }}</td>
                                    <td>{{ $payment->note }}</td>
                                    <td>
                                        @if ($payment->company_branch_id == 0)
                                            MD.AJMAIN HOSAN BHUYAN
                                        @elseif($payment->company_branch_id == 1)
                                            Your Choice
                                        @else
                                            Your Choice Plus
                                        @endif
</td>
                                    <td>
                                        @if ($payment->status == 1)
                                            <a class="btn btn-info btn-sm" href="{{ route('sale_receipt.payment_details', $payment->id) }}"> Voucher </a>
                                            <a class="btn btn-warning btn-sm btn-pending" role="button" data-id="{{$payment->id}}" data-name="{{$payment->customer->name}}">Pending</a>
                                            <a class="btn btn-danger btn-sm btn-delete" role="button" data-id="{{$payment->id}}" data-customer-id="{{$payment->customer_id}}">Delete</a>
                                        @else
                                            <a class="btn btn-info btn-sm" href="{{ route('sale_receipt.payment_details', $payment->id) }}"> Voucher </a>
                                            @if(Auth::user()->company_branch_id==0)
                                                <a class="btn btn-danger btn-sm btn-delete" role="button" data-id="{{$payment->id}}" data-customer-id="{{$payment->customer_id}}">Delete</a>
                                            @endif
                                        @endif
                                        @if($payment->status == 2)
                                                <a class="btn btn-primary btn-sm btn-edit" role="button" data-id="{{$payment->id}}" data-customer-id="{{$payment->customer_id}}" data-amount="{{$payment->amount}}">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p>{!! $payments->render() !!}</p>
            </div>
        </div>
    </div>

    <!---edit model--->
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
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>
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
                                       value="{{ date('Y-m-d') }}" autocomplete="off">
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
                    <button type="button" class="btn btn-primary" id="modal-btn-approved">Approved</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
    <!--- edit modal--->
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Payment Information Edit</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form-edit" enctype="multipart/form-data" name="modal-form-edit">
                            <input type="hidden" name="payment_id" id="payment_id_edit">
                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control"   name="amount" id="amount-edit" placeholder="Enter Amount">
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
    <!---end edit model--->
    <!-- /.modal -->

@endsection

@section('script')
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
            //Date picker
            $('#date, #next-payment-date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('body').on('click', '.btn-delete', function () {
                $('#modal-delete').modal('show');
                paymentId = $(this).data('id');
                customer_id = $(this).data('customer-id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('payment_voucher.delete') }}",
                    data: { id: paymentId,customer_id:customer_id }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            $('body').on('click', '.btn-pending', function () {
                var paymentId = $(this).data('id');
                var clientName = $(this).data('name');
                $('#modal-name').val(clientName);
                $('#payment_id').val(paymentId);
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

            $('#modal-btn-approved').click(function () {
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
            // edit
            $('body').on('click', '.btn-edit', function () {
                var paymentId = $(this).data('id');
                var amountEdit = $(this).data('amount');
                $('#payment_id_edit').val(paymentId);
                $('#amount-edit').val(amountEdit);
                $('#modal-edit').modal('show');

            });

            $('#modal-btn-pay').click(function () {
                var formData = new FormData($('#modal-form-edit')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('client_payment.voucher_update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-pay').modal('hide');
                            Swal.fire(
                                'Paid!',
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
