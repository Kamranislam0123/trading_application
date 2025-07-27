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
    Stock product edit
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
                <form method="POST" action="{{ route('product_stock.edit', ['purchase_inventory_log'=>$purchase_inventory_log->id]) }}">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('warehouse_id') ? 'has-error' :'' }}">
                                    <label>Warehouse</label>

                                    <select class="form-control select2" style="width: 100%;" name="warehouse_id" data-placeholder="Select Warehouse">
                                        <option value="{{ $purchase_inventory_log->warehouse_id }}"> {{ $purchase_inventory_log->warehouse->name??'' }} </option>

                                        {{-- @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach --}}
                                    </select>

                                    @error('warehouse')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('supplier_id') ? 'has-error' :'' }}">
                                    <label>Supplier</label>

                                    <select class="form-control select2" style="width: 100%;" name="supplier_id">
                                        <option value="{{ $purchase_inventory_log->customer_id }}"> {{ $purchase_inventory_log->customer->name??'' }} </option>
{{--                                        @foreach($suppliers as $supplier)--}}
{{--                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $order->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>--}}
{{--                                        @endforeach--}}
                                    </select>

                                    @error('supplier_id')
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
                                        <input type="text" class="form-control pull-right" id="date" name="date" value="{{ old('date', date('Y-m-d', strtotime($purchase_inventory_log->date))) }}" autocomplete="off">
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
                                <tr class="product-item">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_item" name="product_item" value="{{$purchase_inventory_log->productItem->name??''}}" style="width: 100%;">
                                            <input type="hidden" class="form-control" value="{{$purchase_inventory_log->serial??''}}" name="serial">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_category" name="product_category" value="{{$purchase_inventory_log->productCategory->name??''}}" style="width: 100%;" >
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="number" step="any" class="form-control quantity" value="{{ $purchase_inventory_log->quantity }}" name="quantity" required>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control unit_price" name="unit_price" value="{{ $purchase_inventory_log->unit_price }}" required>
                                        </div>
                                    </td>


                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control selling_price" name="selling_price" value="{{ $purchase_inventory_log->selling_price }}" required>
                                        </div>
                                    </td>

                                    <td class="total-cost">৳0.00</td>
                                    <td class="text-center">
                                        <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                    </td>
                                </tr>
                                </tbody>
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
                    <input type="hidden" class="form-control" name="serial[]">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control product_category" name="product_category[]" style="width: 100%;" >
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" value="6" name="quantity[]" required>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]" value="0" required>
                </div>
            </td>



            <td>
                <div class="form-group">
                    <input type="text" class="form-control selling_price" value="0" name="selling_price[]">
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
            $('#date').datepicker({
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
                    delay: 300,
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
                    delay: 300,
                });

                $('#product-container').append(item);

                if ($('.product-item').length + $('.service-item').length >= 1 ) {
                    $('.btn-remove').show();
                    $('.btn-remove-service').show();
                }

            });

            // $('#btn-add-product').click(function () {
            //     var html = $('#template-product').html();
            //     var item = $(html);
            //
            //     // item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));
            //     $('#product-container').append(item);
            //     initProduct();
            //     $('.select2').select2();
            //     if ($('.product-item').length >= 1 ) {
            //         $('.btn-remove').show();
            //     }
            // });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').show();
                }
            });

            $('body').on('keyup', '#discount', function () {
                $('#discount_percentage').val(0);
            });
            $('body').on('keyup', '.quantity,#paid, #transport_cost, #discount,#discount_percentage, .unit_price', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').show();
            } else {
                $('.btn-remove').show();
            }

            initProduct();
            calculate();
        });

        function calculate() {
            var total = 0;
            var totalQuantity = 0;
            var allTotal = 0;
            var transport_cost = parseFloat($("#transport_cost").val()||0);
            var paid = parseFloat($('#paid').val()||0);

            $('.product-item').each(function(i, obj) {
                var quantity = $('.quantity:eq('+i+')').val();
                var unit_price = $('.unit_price:eq('+i+')').val();

                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;


                $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                total += (quantity * unit_price);
                totalQuantity += parseFloat(quantity);
            });

            if ($('#discount_percentage').val() > 0) {
                discount = ($('#discount_percentage').val()*total)/100;
                $('#discount').val(discount);

            }else{
                $('#discount_percentage').val(0);
            }
            var discount = parseFloat($("#discount").val()||0);

            allTotal = (parseFloat(total) + transport_cost) - (discount + paid);
            //due = allTotal - paid;

            $('#due').html('৳' + allTotal.toFixed(2));
            $('#total').html('৳' + allTotal.toFixed(2));
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total_amount').val(total.toFixed(2));
            $('#total-quantity').html(totalQuantity);
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
