@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Manually ChequeIn Add
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
                <div class="box-header with-border">
                    <h3 class="box-title">Manually Due Entry Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('manually_chequeIn') }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Customer Name</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="customer" id="customer">
                                    <option value=""> Select Customer </option>

                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} - {{$customer->address}} - {{$customer->mobile_no??''}} - {{$customer->branch->name??''}}</option>
                                    @endforeach
                                </select>

                                @error('customer')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('invoice_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Invoice No</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Invoice Number"
                                       name="invoice_no" value="{{ old('invoice_no') }}">

                                @error('invoice_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group {{ $errors->has('sale_order_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Sale Order</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="sale_order_no" id="sale_order_no">
                                    <option value="">Select Sale Order</option>
                                </select>

                                @error('sale_order_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group {{ $errors->has('payment_method') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Payment Method </label>
                            <div class="col-sm-10">
                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option value="">Select Payment Method</option>
                                    <option value="1" {{ old('payment_method') == '1' ? 'selected' : '' }}>Cash</option>
                                    <option value="2" {{ old('payment_method') == '2' ? 'selected' : '' }}>Bank</option>
                                </select>

                                @error('payment_method')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('sales_person_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Sales Person Name</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="sales_person_id" id="sales_person_id">
                                    <option value="">Select Sales Person</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('sales_person_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} - {{ $employee->employee_id }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('sales_person_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group {{ $errors->has('client_cheque_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Client Cheque No.*</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Client Cheque No."
                                       name="client_cheque_no" value="{{ old('client_cheque_no') }}">

                                @error('client_cheque_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group {{ $errors->has('total_sales_amount') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Total Sales Amount</label>

                            <div class="col-sm-10">
                                <input type="number" step="0.01" class="form-control" placeholder="Enter Total Sales Amount"
                                       name="total_sales_amount" id="total_sales_amount" value="{{ old('total_sales_amount',0) }}">

                                @error('total_sales_amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('receive_amount') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Receive Amount</label>

                            <div class="col-sm-10">
                                <input type="number" step="0.01" class="form-control" placeholder="Enter Receive Amount"
                                       name="receive_amount" id="receive_amount" value="{{ old('receive_amount',0) }}">

                                @error('receive_amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Due Amount</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="due_amount" readonly value="0.00">
                                <input type="hidden" name="due_amount" id="due_amount_hidden" value="0.00">
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('next_payment_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Due Amount Next Payment Date</label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="next_payment_date" name="next_payment_date" value="{{ old('next_payment_date') }}" autocomplete="off">
                                </div>
                                @error('next_payment_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group {{ $errors->has('next_approximate_payment') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Next Approximate Payment</label>
                            <div class="col-sm-10">
                                <input type="number" step="0.01" class="form-control" placeholder="Enter Next Approximate Payment Amount"
                                       name="next_approximate_payment" id="next_approximate_payment" value="{{ old('next_approximate_payment',0) }}">

                                @error('next_approximate_payment')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group {{ $errors->has('client_amount') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Cheque Amount</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Client Amount"
                                       name="client_amount" value="{{ old('client_amount',0) }}">

                                @error('client_amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group {{ $errors->has('note') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Client Note</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Note"
                                       name="note" value="{{ old('note') }}">

                                @error('note')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group {{ $errors->has('cheque_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Cheque Date </label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="cheque_date" name="cheque_date" value="{{ old('cheque_date') }}" autocomplete="off">
                                </div>
                                @error('cheque_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- /.input group -->
                        </div> --}}
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
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $('.select2').select2()

        var saleOrderSelected = '{{ old('sale_order_no') }}';

        $('#customer').change(function () {
            var customerId = $(this).val();

            $('#sale_order_no').html('<option value="">Select Sale Order</option>');

            if (customerId != '') {
                // Get sale orders for the customer
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_sale_order') }}",
                    data: { customerId: customerId }
                }).done(function( data ) {
                    $.each(data, function( index, item ) {
                        if (saleOrderSelected == item.id)
                            $('#sale_order_no').append('<option value="'+item.id+'" selected>'+item.order_no+'</option>');
                        else
                            $('#sale_order_no').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });
                });

                // Auto-select the assigned sales person for the customer
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_customer_sales_person') }}",
                    data: { customerId: customerId }
                }).done(function( response ) {
                    if (response.success && response.sales_person_id) {
                        $('#sales_person_id').val(response.sales_person_id).trigger('change');
                    }
                }).fail(function() {
                    // If no sales person is assigned, keep the current selection or reset to empty
                    console.log('No sales person assigned to this customer');
                });
            } else {
                // Reset sales person selection when no customer is selected
                $('#sales_person_id').val('').trigger('change');
            }
        });

        $('#customer').trigger('change');

        //Date picker
        $('#date, #cheque_date, #next_payment_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        });

        // Calculate due amount when total sales amount or receive amount changes
        $('#total_sales_amount, #receive_amount').on('input', function() {
            calculateDueAmount();
        });

        function calculateDueAmount() {
            var totalSalesAmount = parseFloat($('#total_sales_amount').val()) || 0;
            var receiveAmount = parseFloat($('#receive_amount').val()) || 0;
            var dueAmount = totalSalesAmount - receiveAmount;
            
            $('#due_amount').val(dueAmount.toFixed(2));
            $('#due_amount_hidden').val(dueAmount.toFixed(2));
        }

        // Initialize due amount calculation on page load
        calculateDueAmount();
    </script>
@endsection
