@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Customer
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
                    <a class="btn btn-primary" href="{{ route('customer.add') }}">Add Customer</a>

                    <hr>
                    <div class="table-responsive">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th> ID </th>
                            <th> Name </th>
                            <th> Address </th>
                            <th> Mobile </th>
                            <th> Assigned Sales Person </th>
                            {{-- <th> Branch </th> --}}
                            <th> Opening Due </th>
                            <th> Status </th>
                            <th> Action </th>
                        </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="modal-pay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Customer Payment</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form" name="modal-form">
                        <div class="form-group">
                            <label>Customer</label>
                            <input class="form-control" id="modal-customer-name" disabled>
                            <input type="hidden" id="modal-customer-id" name="customer_id">
                        </div>

                        <div class="form-group">
                            <label>Due Amount</label>
                            <input class="form-control" id="modal-due-amount" disabled>
                        </div>

                        <div class="form-group">
                            <label>Payment Type *</label>
                            <select class="form-control" id="modal-pay-type" name="payment_type" required>
                                <option value="">Select Payment Type</option>
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Mobile Banking</option>
                                <option value="4">Sale Adjustment Discount</option>
                                <option value="5">Return Adjustment</option>
                            </select>
                        </div>

                        <div id="modal-bank-info" style="display: none;">
                            <div class="form-group">
                                <label>Bank</label>
                                <select class="form-control" id="modal-bank" name="bank">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Branch</label>
                                <select class="form-control" id="modal-branch" name="branch">
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Account</label>
                                <select class="form-control" id="modal-account" name="account">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Cheque No</label>
                                <input type="text" class="form-control" id="modal-cheque-no" name="cheque_no">
                            </div>

                            <div class="form-group">
                                <label>Cheque Image</label>
                                <input type="file" class="form-control" id="modal-cheque-image" name="cheque_image">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount *</label>
                            <input type="number" step="0.01" class="form-control" id="modal-amount" name="amount" required>
                        </div>

                        <div class="form-group">
                            <label>Date *</label>
                            <input type="date" class="form-control" id="modal-date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <textarea class="form-control" id="modal-note" name="note" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-pay">Pay</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- Sweet Alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(function () {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('customer.datatable') }}',
                    data: function (d) {
                        d.employee_id = $('#employee_filter').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'employee', name: 'employee.name'},
                    {{-- {data: 'branch_status', name: 'branch_status'}, --}}
                    {data: 'opening_due', name: 'opening_due'},
                    {data: 'status', name: 'status', searchable:false},
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

            // Payment button click
            $('body').on('click', '.btn-pay', function () {
                var customerId = $(this).data('id');
                var customerName = $(this).data('name');
                var dueAmount = $(this).data('due');

                $('#modal-customer-id').val(customerId);
                $('#modal-customer-name').val(customerName);
                $('#modal-due-amount').val(dueAmount);
                $('#modal-amount').val('');
                $('#modal-note').val('');
                $('#modal-pay-type').val('');
                $('#modal-bank-info').hide();

                $('#modal-pay').modal('show');
            });

            // Payment type change
            $('#modal-pay-type').change(function () {
                if ($(this).val() == '2') {
                    $('#modal-bank-info').show();
                } else {
                    $('#modal-bank-info').hide();
                }
            });

            // Bank change - load branches
            $('#modal-bank').change(function () {
                var bankId = $(this).val();
                $('#modal-branch').html('<option value="">Select Branch</option>');
                $('#modal-account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('#modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            // Branch change - load accounts
            $('#modal-branch').change(function () {
                var branchId = $(this).val();
                $('#modal-account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('#modal-account').append('<option value="'+item.id+'">'+item.account_name+' - '+item.account_no+'</option>');
                        });
                    });
                }
            });

            // Payment submit
            $('#modal-btn-pay').click(function () {
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
        })
    </script>
@endsection
