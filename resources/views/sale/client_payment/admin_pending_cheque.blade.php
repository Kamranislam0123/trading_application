@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    {{--    Payments of {{ $payment->customer->name }}--}}
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
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('Y-m-d') }}</td>
                                    <td>{{ $payment->customer->name??'' }}</td>
                                    <td>
                                        @if ($payment->company_branch_id == 1)
                                            Your Choice
                                        @elseif($payment->company_branch_id == 2)
                                            Your Choice Plus
                                        @else
                                            Admin
                                        @endif
                                    </td>
                                    <td>{{ $payment->transaction_method==1?'Cash':"Bank" }}</td>
                                    <td>{{ number_format($payment->amount * nbrCalculation(),2) }}</td>
                                    <td>{{ $payment->note }}</td>
                                    <td>
                                        @if ($currentDate == $payment->cheque_date)
                                            <span class="label label-success" style="font-size: 14px">Due Today</span>
                                        @elseif($payment->cheque_date == date('Y-m-d', strtotime('-1 day', strtotime($currentDate))))
                                            <span class="label label-primary" style="font-size: 14px">Due From Yesterday</span>
                                        @elseif($payment->cheque_date == date('Y-m-d', strtotime('+1 day', strtotime($currentDate))))
                                            <span class="label label-warning" style="font-size: 14px">Due Tomorrow</span>
                                        @elseif($currentDate < $payment->cheque_date)
                                            <span class="label label-info" style="font-size: 14px">Next Date - {{$payment->cheque_date}}</span>
                                        @else
                                            <span class="label label-danger" style="font-size: 14px">Due From - {{$payment->cheque_date}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->status == 1)
                                            <a class="btn btn-info btn-sm" href="{{ route('sale_receipt.payment_details', $payment->id) }}"> Vouchar </a>
                                            <a class="btn btn-warning btn-sm btn-pending" role="button" data-id="{{$payment->id}}"
                                               data-name="{{$payment->customer->name}}"
                                               data-bank="{{$payment->client_bank_name}}"
                                               data-no="{{$payment->client_cheque_no}}"
                                               data-date="{{$payment->cheque_date}}"
                                               data-amount="{{$payment->client_amount * nbrCalculation()}}">Pending</a>
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
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input class="form-control" id="modal-bank-name" disabled>
                        </div>
                        <div class="form-group">
                            <label>Bank Cheque No</label>
                            <input class="form-control" id="modal-cheque-no" disabled>
                        </div>
                        <div class="form-group">
                            <label>Bank Cheque Date</label>
                            <input class="form-control" id="modal-cheque-date" disabled>
                        </div>
                        <div class="form-group">
                            <label>Cheque Amount</label>
                            <input class="form-control" id="modal-cheque-amount" disabled>
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
                format: 'yyyy-mm-dd',
                todayHighlight: true
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

            $('body').on('click', '.btn-pending', function () {
                var paymentId = $(this).data('id');
                var clientName = $(this).data('name');
                var clientBankName = $(this).data('bank');
                var clientChequeNo = $(this).data('no');
                var ChequeDate = $(this).data('date');
                var ChequeAmount = $(this).data('amount');
                $('#modal-name').val(clientName);
                $('#modal-bank-name').val(clientBankName);
                $('#modal-cheque-no').val(clientChequeNo);
                $('#modal-cheque-amount').val(ChequeAmount);
                $('#modal-cheque-date').val(ChequeDate);
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
        });
    </script>
@endsection
