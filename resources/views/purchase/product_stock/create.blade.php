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
    Stock product
@endsection

@section('content')
    @if(session('error'))
	    <div class="alert alert-danger alert-dismissable">
		  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  {{session('error')}}
	    </div>
	@endif
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Stock Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('product_stock.add') }}">
                    @csrf

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer_type') ? 'has-error' :'' }}">
                                    <label>Customer Type </label>
                                    <select class="form-control" id="customer_type" name="customer_type">
                                        <option {{ old('customer_type') == 2 ? 'selected' : '' }} value="2">Old</option>
                                        <option {{ old('customer_type') == 1 ? 'selected' : '' }} value="1">New</option>
                                    </select>
                                    @error('customer_type')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div id="old_customer_area">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('customer_id') ? 'has-error' :'' }}">
                                        <label>Customer</label>

                                        <select class="form-control select2" style="width: 100%;" name="customer_id">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name.' - '.$customer->address.' - '.$customer->mobile_no }} - {{$customer->branch->name??''}}</option>
                                            @endforeach
                                        </select>

                                        @error('customer_id')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div id="new_customer_area">
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('customer_name') ? 'has-error' :'' }}">
                                        <label>Customer Name </label>
                                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="form-control">
                                        @error('customer_name')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                                        <label>Customer Mobile</label>
                                        <input type="text" id="mobile_no" value="{{ old('mobile_no') }}" name="mobile_no" class="form-control" >
                                        @error('mobile_no')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                                        <label>Customer Address</label>
                                        <input type="text" id="address" value="{{ old('address') }}" name="address" class="form-control"  >
                                        @error('address')
                                        <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
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

                            <div class="col-md-3">
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
                                            @for ($i = 1; $i <=10; $i++)
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
                                            <th>
                                                <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add Product</a>
                                            </th>
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
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
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
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            // $('.product').select2();
            $('.select2').select2();

            //Date picker
            $('#date,#next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('#customer_type').change(function (){
                var customerType = $(this).val();
                if (customerType == '1'){

                    $("#old_customer_area").hide();
                    $("#new_customer_area").show();

                    $('#address').autocomplete({
                        source:function (request, response) {
                            $.getJSON('{{ route("get_customer_address_suggestion") }}?term='+request.term, function (data) {
                                console.log(data);
                                var array = $.map(data, function (row) {
                                    return {
                                        value: row.address,
                                        label: row.address,
                                    }
                                });
                                response($.ui.autocomplete.filter(array, request.term));
                            })
                        },
                        minLength: 2,
                        delay: 500,
                    });

                    $('#mobile_no').autocomplete({
                        source:function (request, response) {
                            $.getJSON('{{ route("get_customer_mobile_no_suggestion") }}?term='+request.term, function (data) {
                                console.log(data);
                                var array = $.map(data, function (row) {
                                    return {
                                        value: row.mobile_no,
                                        label: row.mobile_no,
                                    }
                                });
                                response($.ui.autocomplete.filter(array, request.term));
                            })
                        },
                        minLength: 2,
                        delay: 500,
                    });

                    $('#customer_name').autocomplete({
                        source:function (request, response) {
                            $.getJSON('{{ route("get_customer_name_suggestion") }}?term='+request.term, function (data) {
                                console.log(data);
                                var array = $.map(data, function (row) {
                                    return {
                                        value: row.name,
                                        label: row.name,
                                    }
                                });
                                response($.ui.autocomplete.filter(array, request.term));
                            })
                        },
                        minLength: 2,
                        delay: 500,
                    });

                }else{
                    $("#new_customer_area").hide();
                    $("#old_customer_area").show();
                }

            });

            $('#customer_type').trigger("change");

            var timer = null;
            $('body').on('keyup','.product_item','.product_category', function(){
                clearTimeout(timer);
                timer = setTimeout(getPrice,7000);
            })

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
            // $('body').on('keyup', '.product_item','.product_category', function () {
            //     getPrice();
            // });
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
        });

        function getPrice() {
            $('.product-item').each(function(i, obj) {
                var product_item = $('.product_item:eq('+i+')').val();
                var product_category = $('.product_category:eq('+i+')').val();

                if ($('.unit_price:eq('+i+')').val() && $('.selling_price:eq('+i+')').val() <= 0) {

                    if (product_item && product_category != null) {

                        $.ajax({
                            method: 'POST',
                            url: '{{ route("get_unit_price") }}',
                            data: {'product_item': product_item, 'product_category': product_category},
                        }).done(function (response) {
                            //console.log(response);
                            if (response.id) {
                                $('.unit_price:eq(' + i + ')').val(response.unit_price);
                                $('.selling_price:eq(' + i + ')').val(response.selling_price);
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
    </script>
@endsection
