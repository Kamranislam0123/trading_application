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
                <div class="box-body table-responsive">
                    <table id="table" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            {{-- <th>Branch</th> --}}
                            <th>Opening Due</th>
                            <th>Total</th>
                            <th>Return</th>
                            <th>Paid</th>
                            <th>Due</th>
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
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("client_payment.customer.datatable") }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {{-- {data: 'branch', name: 'branch'}, --}}
                    {data: 'opening_due', name: 'opening_due', orderable: false},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'return', name: 'return', orderable: false},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
            });

            $('.select2').select2();

            //Date picker
            $('#date, #next-payment-date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
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
