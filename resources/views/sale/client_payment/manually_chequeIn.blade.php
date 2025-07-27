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
                    <h3 class="box-title">Manually ChequeIn Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('manually_chequeIn') }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Customer</label>

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

                        <div class="form-group {{ $errors->has('sale_order_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Sale Order</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="sale_order_no" id="sale_order_no">
                                    <option value="">Select Sale Order</option>
                                </select>

                                @error('sale_order_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('client_bank_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Client Bank Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Client Bank Name"
                                       name="client_bank_name" value="{{ old('client_bank_name') }}">

                                @error('client_bank_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('client_cheque_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Client Cheque No.*</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Client Cheque No."
                                       name="client_cheque_no" value="{{ old('client_cheque_no') }}">

                                @error('client_cheque_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('client_amount') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Cheque Amount</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Client Amount"
                                       name="client_amount" value="{{ old('client_amount',0) }}">

                                @error('client_amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

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

                        <div class="form-group {{ $errors->has('cheque_date') ? 'has-error' :'' }}">
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
                        </div>
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
            }
        });

        $('#customer').trigger('change');

        //Date picker
        $('#date, #cheque_date').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
