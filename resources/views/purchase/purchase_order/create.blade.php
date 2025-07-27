@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Purchase Order
@endsection

@section('content')
{{--    @if(session('error'))--}}
{{--        <div class="alert alert-danger alert-dismissable">--}}
{{--            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>--}}
{{--            {{session('error')}}--}}
{{--        </div>--}}
{{--    @endif--}}
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Order Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('purchase_order.create') }}" id="purchase-form">
                    @csrf

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('supplier_id') ? 'has-error' :'' }}">
                                    <label>Supplier</label>

                                    <select class="form-control select2" style="width: 100%;" name="supplier_id">
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('supplier_id')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('warehouse_id') ? 'has-error' :'' }}">
                                    <label>Warehouse</label>

                                    <select class="form-control select2" style="width: 100%;" name="warehouse_id" data-placeholder="Select Warehouse">
                                        {{-- <option value="">Select Warehouse</option> --}}

                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('warehouse')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('product_type') ? 'has-error' :'' }}">
                                    <label>Product Type</label>

                                    <select class="form-control select2" style="width: 100%;" name="product_type">
                                         <option value="1" {{ old('product_type') == 1 ? 'selected' : '' }}>China Product</option>
                                    </select>

                                    @error('product_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('Y-m-d')) : old('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product Model </th>
                                    <th>Product Category </th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Unit Price</th>
                                    <th width="10%">Selling Price</th>
                                    <th>Total Cost</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                @if (old('product_item') != null && sizeof(old('product_item')) > 0)
                                    @foreach(old('product_item') as $item)
                                        <tr class="product-item">
                                            <td>
                                                <div class="form-group {{ $errors->has('product_item.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control product_item" name="product_item[]" style="width: 100%;" value="{{ old('product_item.'.$loop->index) }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group {{ $errors->has('product_category.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control product_category" name="product_category[]" style="width: 100%;" value="{{ old('product_category.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('selling_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" class="form-control selling_price" name="selling_price[]" value="{{ old('selling_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td class="total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @for ($i = 1; $i <=50; $i++)
                                    <tr class="product-item">
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control product_item" name="product_item[]" style="width: 100%;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control product_category" name="product_category[]" style="width: 100%;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" step="any" class="form-control quantity" value="6" name="quantity[]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control unit_price" name="unit_price[]" value="0">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control selling_price" name="selling_price[]" value="0">
                                            </div>
                                        </td>

                                        <td class="total-cost">৳0.00</td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>
                                    @endfor
                                @endif
                                <tfoot>
                                <tr>
                                    <td>
                                        <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add Product</a>
                                    </td>
                                    <th colspan="" class="text-right">Total Quantity</th>
                                    <th id="total-quantity">0</th>
                                    <th colspan="" class="text-right"></th>
                                    <th colspan="" class="text-right">Total Amount</th>
                                    <th id="total-amount">৳0.00</th>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Payment</h3>
                                </div>
                                <!-- /.box-header -->

                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Payment Type</label>
                                                <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                                    <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                                    <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                                    <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                                </select>
                                            </div>

                                            <div id="modal-bank-info">
                                                <div class="form-group {{ $errors->has('bank') ? 'has-error' :'' }}">
                                                    <label>Bank</label>
                                                    <select class="form-control select2" id="modal-bank" name="bank">
                                                        <option value="">Select Bank</option>

                                                        @foreach($banks as $bank)
                                                            <option value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group {{ $errors->has('branch') ? 'has-error' :'' }}">
                                                    <label>Branch</label>
                                                    <select class="form-control select2" id="modal-branch" name="branch">
                                                        <option value="">Select Branch</option>
                                                    </select>
                                                </div>

                                                <div class="form-group {{ $errors->has('account') ? 'has-error' :'' }}">
                                                    <label>Account</label>
                                                    <select class="form-control select2" id="modal-account" name="account">
                                                        <option value="">Select Account</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Cheque No.</label>
                                                    <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No." value="{{ old('cheque_no') }}">
                                                </div>

                                                <div class="form-group {{ $errors->has('cheque_image') ? 'has-error' :'' }}">
                                                    <label>Cheque Image</label>
                                                    <input class="form-control" name="cheque_image" type="file">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th colspan="4" class="text-right">Product Sub Total</th>
                                                    <th id="product-sub-total">৳0.00</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right"> Discount (%) </th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('discount_percentage') ? 'has-error' :'' }}">
                                                            <input type="text" class="form-control" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', 0) }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right"> Discount (Amount) </th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                                            <input type="text" class="form-control" name="discount" id="discount" value="{{ old('discount', 0) }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right"> Transport Cost </th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('transport_cost') ? 'has-error' :'' }}">
                                                            <input type="text" class="form-control" name="transport_cost" id="transport_cost" value="{{ old('transport_cost', 0) }}">
                                                        </div>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>

                                        <div class="col-md-4">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th colspan="4" class="text-right"> Total</th>
                                                    <th id="final_total">৳0.00</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right">Paid</th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('paid') ? 'has-error' :'' }}">
                                                            <input type="text" class="form-control" name="paid" id="paid" value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid') }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-right">Due</th>
                                                    <th id="due">৳0.00</th>
                                                </tr>
                                                <tr id="tr-next-payment">
                                                    <th colspan="4" class="text-right">Next Payment Date</th>
                                                    <td>
                                                        <div class="form-group {{ $errors->has('next_payment') ? 'has-error' :'' }}">
                                                            <div class="input-group date">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input type="text" class="form-control pull-right" id="next_payment" name="next_payment" value="{{ old('next_payment') }}" autocomplete="off">
                                                            </div>
                                                            <!-- /.input group -->
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <input type="hidden" name="total" id="total">
                                    <input type="hidden" name="due_total" id="due_total">
                                    <button type="submit" class="btn btn-primary submission ">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="template-product">
        @php
            $i=1;
        @endphp
        <tr class="product-item" >
            <td>
                <div class="form-group">
                    <input type="text" class="form-control product_item" name="product_item[]" style="width: 100%;" >
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control product_category" name="product_category[]" style="width: 100%;" >
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" value="6" name="quantity[]" >
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" class="form-control unit_price" name="unit_price[]" value="0" >
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" class="form-control selling_price" value="0" name="selling_price[]">
                </div>
            </td>

            <td class="total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();

            //Date picker
            $('#date,#next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('.product_item').autocomplete({
                source:function (request, response) {
                    $.getJSON('{{ route("get_productItem_suggestion") }}?term='+request.term, function (data) {
                        console.log(data);
                        var array = $.map(data, function (row) {
                            if(row.type == 1) {
                                return {
                                    value: row.name,
                                    label: row.name+" "+"China",
                                }
                            }else {
                                return {
                                    value: row.name,
                                    label: row.name+" "+"Bangla",
                                }
                            }

                        });
                        response($.ui.autocomplete.filter(array, request.term));
                    })
                },
                minLength: 2,
                //delay: 50,
            });

            $('.product_category').autocomplete({
                source:function (request, response) {
                    $.getJSON('{{ route("get_categoryItem_suggestion") }}?term='+request.term, function (data) {
                        console.log(data);
                        var array = $.map(data, function (row) {
                            if(row.type == 1) {
                                return {
                                    value: row.name,
                                    label: row.name+" "+"China",
                                }
                            }else {
                                return {
                                    value: row.name,
                                    label: row.name+" "+"Bangla",
                                }
                            }
                        });
                        response($.ui.autocomplete.filter(array, request.term));
                    })
                },
                minLength: 2,
                //delay: 50,
            });

            var message = '{{ session('message') }}';

            if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
                if (message != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message,
                    });
                }
            }

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);


                item.find('.product_item').autocomplete({
                    source:function (request, response) {
                        $.getJSON('{{ route('get_productItem_suggestion') }}?term='+request.term, function (data) {
                            console.log(data);
                            var array = $.map(data, function (row) {
                                if(row.type == 1) {
                                    return {
                                        value: row.name,
                                        label: row.name+" "+"China",
                                    }
                                }else {
                                    return {
                                        value: row.name,
                                        label: row.name+" "+"Bangla",
                                    }
                                }
                            });

                            response($.ui.autocomplete.filter(array, request.term));
                        })
                    },
                    minLength: 2,
                    //delay: 500,
                });

                item.find('.product_category').autocomplete({
                    source:function (request, response) {
                        $.getJSON('{{ route('get_categoryItem_suggestion') }}?term='+request.term, function (data) {
                            console.log(data);
                            var array = $.map(data, function (row) {
                                if(row.type == 1) {
                                    return {
                                        value: row.name,
                                        label: row.name+" "+"China",
                                    }
                                }else {
                                    return {
                                        value: row.name,
                                        label: row.name+" "+"Bangla",
                                    }
                                }
                            });

                            response($.ui.autocomplete.filter(array, request.term));
                        })
                    },
                    minLength: 2,
                    //delay: 500,
                });

                $('#product-container').append(item);

                if ($('.product-item').length + $('.service-item').length >= 1 ) {
                    $('.btn-remove').show();
                    $('.btn-remove-service').show();
                }
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    //$('.btn-remove').hide();
                }
            });

            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');

            var selectedBranch = '{{ old('branch') }}';
            var selectedAccount = '{{ old('account') }}';

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
                            if (selectedBranch == item.id)
                                $('#modal-branch').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('#modal-branch').trigger('change');
                    });
                }

                $('#modal-branch').trigger('change');
            });

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
                            if (selectedAccount == item.id)
                                $('#modal-account').append('<option value="'+item.id+'" selected>'+item.account_no+'</option>');
                            else
                                $('#modal-account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });

            $('#modal-bank').trigger('change');

            $('body').on('keyup', '#discount', function () {
                $('#discount_percentage').val(0);
            });
            $('body').on('keyup', '.quantity, #transport_cost, #discount,#discount_percentage,#paid, .unit_price', function () {
                calculate();
            });
            $('body').on('keyup', '.quantity, .unit_price, #transport_cost, #return_amount, #discount_percentage, #vat, #discount, #paid', function () {
                calculate();
            });

            $('body').on('change', '.quantity, .unit_price, #transport_cost, #return_amount, #discount_percentage, #previous_due', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            initProduct();
            calculate();

            var timer = null;
            $('body').on('keyup','.product_item','.product_category', function(){
                clearTimeout(timer);
                timer = setTimeout(getPrice,6000);
            })
        });

        function getPrice() {
            $('.product-item').each(function(i, obj) {
                var product_item = $('.product_item:eq('+i+')').val();
                var product_category = $('.product_category:eq('+i+')').val();

                if ($('.unit_price:eq('+i+')').val() && $('.selling_price:eq('+i+')').val() <= 0){
                    if (product_item && product_category != null){
                        $.ajax({
                            method: 'POST',
                            url: '{{ route("get_unit_price") }}',
                            data: {'product_item': product_item, 'product_category': product_category},
                        }).done(function (response) {
                            //console.log(response);
                            if (response.id) {
                                $('.unit_price:eq('+i+')').val(response.unit_price);
                                $('.selling_price:eq('+i+')').val(response.selling_price);
                                calculate();
                            }
                        })
                    }
                }
            });
        }

        function calculate() {
            var productSubTotal = 0;
            var totalQuantity = 0;
            var vat = parseFloat($('#vat').val()||0);
            var discount = parseFloat($('#discount').val()||0);
            var transport_cost = parseFloat($('#transport_cost').val()||0);
            var return_amount = parseFloat($('#return_amount').val()||0);
            var paid = parseFloat($('#paid').val()||0);
            var previous_due = parseFloat($('#previous_due').val()||0);

            $('.product-item').each(function(i, obj) {
                var quantity = $('.quantity:eq('+i+')').val();
                var unit_price = $('.unit_price:eq('+i+')').val();
                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                productSubTotal += quantity * unit_price;
                totalQuantity += parseFloat(quantity);
            });

            if ($('#discount_percentage').val() > 0) {
                discount = ($('#discount_percentage').val()*productSubTotal)/100;
                $('#discount').val(discount);

            }else{
                $('#discount_percentage').val(0);
            }
            var discount = parseFloat($("#discount").val()||0);
            var productTotalVat = (productSubTotal * vat) / 100;
            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#total-amount').html('৳' + productSubTotal.toFixed(2));
            $('#vat_total').html('৳' + productTotalVat.toFixed(2));

            var total = parseFloat(productSubTotal) + transport_cost + parseFloat(productTotalVat) - parseFloat(discount) - return_amount;

            var due = parseFloat(total) + previous_due - parseFloat(paid);
            $('#total-quantity').html(totalQuantity);
            $('#final-amount').html('৳' + total.toFixed(2));
            $('#final_total').html('৳' + (total+previous_due).toFixed(2));
            $('#due').html('৳' + due.toFixed(2));
            $('#total').val(total.toFixed(2));
            $('#due_total').val(due.toFixed(2));

            if (due > 0) {
                $('#tr-next-payment').show();
            } else {
                $('#tr-next-payment').hide();
            }
        }

        // function calculate() {
        //     var total = 0;
        //     var allTotal = 0;
        //     var transport_cost = parseFloat($("#transport_cost").val()||0);
        //
        //     $('.product-item').each(function(i, obj) {
        //         var quantity = $('.quantity:eq('+i+')').val();
        //         var unit_price = $('.unit_price:eq('+i+')').val();
        //
        //         if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
        //             quantity = 0;
        //
        //         if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
        //             unit_price = 0;
        //
        //         $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
        //         total += quantity * unit_price;
        //     });
        //
        //     if ($('#discount_percentage').val() > 0) {
        //         discount = ($('#discount_percentage').val()*total)/100;
        //         $('#discount').val(discount);
        //
        //     }else{
        //         $('#discount_percentage').val(0);
        //     }
        //     var discount = parseFloat($("#discount").val()||0);
        //
        //     allTotal = parseFloat(total) + transport_cost - discount;
        //
        //     $('#total').html('৳' + allTotal.toFixed(2));
        //     $('#total-amount').html('৳' + total.toFixed(2));
        // }

        function initProduct() {
            $('.product').select2();
        }

        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });

        $(function () {
            $('body').on('click', '.submission', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Save The Purchase Order",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Save The Order!'

                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#purchase-form').submit();
                    }
                })

            });
        });
    </script>
@endsection
