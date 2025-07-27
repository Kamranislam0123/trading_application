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
    Manually Stcok 
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('product_stock.add') }}">
    @csrf
    <input type="hidden" name="sale_type" value="1">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Stock Information</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                <label>Date *</label>

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

                        <div class="col-md-8">
                            <div class="form-group {{ $errors->has('note') ? 'has-error' :'' }}">
                                <label> Note </label>

                                <input class="form-control" type="text" name="note" value="{{ old('note') }}">

                                @error('note')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Products</h3>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <div class="form-group">
                        <input type="search" class="form-control serial" id="serial" name="serial[]" value="" placeholder="Enter product code" autofocus autocomplete="off">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th> Code </th>
                                <th> Model </th>
                                <th> Category </th>
                                <th> Color </th>
                                <th> Size </th>
                                <th> Warehouse </th>
                                <th width="80">Quantity</th>
                                <th width="80">Unit Price</th>
                                <th width="80">Selling Price</th>
                                <th>Total Cost</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody id="product-container">
                            @if (old('product_serial') != null && sizeof(old('product_serial')) > 0)
                                @foreach(old('product_serial') as $item)
                                    <tr class="product-item">
                                        <td>
                                            <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                               <input type="hidden" readonly class="form-control purchase_inventory" name="purchase_inventory[]" value="{{ old('purchase_inventory.'.$loop->index) }}">
                                                <input type="text" readonly class="form-control product_serial" name="product_serial[]" value="{{ old('product_serial.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('product_item.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" readonly class="form-control product_item" name="product_item[]" value="{{ old('product_item.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('product_category.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" readonly class="form-control product_category" name="product_category[]" value="{{ old('product_category.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('product_color.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" readonly class="form-control product_color" name="product_color[]" value="{{ old('product_color.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('product_size.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" readonly class="form-control product_size" name="product_size[]" value="{{ old('product_size.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('warehouse.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" readonly class="form-control warehouse" name="warehouse[]" value="{{ old('warehouse.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group {{ $errors->has('selling_price.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="number" step="any" class="form-control selling_price" name="selling_price[]" value="{{ old('selling_price.'.$loop->index) }}">
                                            </div>
                                        </td>

                                        <td class="total-cost">৳0.00</td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>

                                    {{-- <tr>
                                        <td colspan="7" class="available-quantity" style="font-weight: bold"></td>
                                    </tr> --}}
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- <a role="button" class="btn btn-info btn-sm" id="btn-add-product" style="margin-bottom: 10px">Add Product</a> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    {{-- <h3 class="box-title">Payment</h3> --}}
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

    <template id="template-product">
        <tr class="product-item">
            <td>
                <div class="form-group">
                    <input type="hidden" readonly class="form-control purchase_inventory" name="purchase_inventory[]" value="">
                    <input type="text" readonly class="form-control product_serial" name="product_serial[]" value="">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly class="form-control product_item" name="product_item[]" value="">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly class="form-control product_category" name="product_category[]" value="">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly class="form-control product_color" name="product_color[]" value="">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly class="form-control product_size" name="product_size[]" value="">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly class="form-control warehouse" name="warehouse[]" value="">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control quantity" name="quantity[]"  value="6">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]" value="0">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" readonly class="form-control selling_price" name="selling_price[]" value="">
                </div>
            </td>

            <td class="total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>

        <tr>
            <td colspan="7" class="available-quantity" style="font-weight: bold"></td>
        </tr>
    </template>

@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Date picker
            $('#date, #next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('.serial').autocomplete({
                source:function (request, response) {
                    $.getJSON('{{ route("get_serial_suggestion") }}?term='+request.term, function (data) {
                        // console.log(data);
                        var array = $.map(data, function (row) {
                            return {
                                value: row.serial,
                                label: row.serial+" - "+row.product_item.name+" - "+row.product_category.name+" - "+row.product_color.name+" - "+row.product_size.name+" - "+row.warehouse.name
                            }
                        });
                        response($.ui.autocomplete.filter(array, request.term));
                    })
                },
                minLength: 2,
                delay: 500,
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

            var serials = [];

            $( "#serial" ).each(function( index ) {
                if ($(this).val() != '') {
                    serials.push($(this).val());
                }
            });

            $('body').on('click', '.btn-remove', function () {
                var serial = $(this).closest('tr').find('.serial').val();
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length + $('.service-item').length <= 1 ) {
                    $('.btn-remove').hide();
                    $('.btn-remove-service').hide();
                }

                serials = $.grep(serials, function(value) {
                    return value != serial;
                });

            });

            $('body').on('keypress', '.serial', function (e) {
                if (e.keyCode == 13) {
                    var serial = $(this).val();
                    $this = $(this);

                    if($.inArray(serial, serials) != -1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Already exist in list.',
                        });

                        return false;
                    }

                    if (serial == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please enter produce code.',
                        });
                    } else {
                        $.ajax({
                            method: "GET",
                            url: "{{ route('sale_product.details') }}",
                            data: { serial: serial }
                        }).done(function( response ) {
                            // console.log(response);
                            if (response.success) {
                                var html = '<tr class="product-item"> <td> <div class="form-group"> <input type="hidden" readonly class="form-control purchase_inventory" name="purchase_inventory[]" value="'+response.data.id+'"> <input type="text" readonly class="form-control product_serial" name="product_serial[]" value="'+response.data.serial+'"> </div></td><td> <div class="form-group"> <input type="text" readonly class="form-control product_item" name="product_item[]" value="'+response.data.product_item.name+'"> </div></td><td> <div class="form-group"> <input type="text" readonly class="form-control product_category" name="product_category[]" value="'+response.data.product_category.name+'"> </div></td><td> <div class="form-group"> <input type="text" readonly class="form-control product_color" name="product_color[]" value="'+response.data.product_color.name+'"> </div></td><td> <div class="form-group"> <input type="text" readonly class="form-control product_size" name="product_size[]" value="'+response.data.product_size.name+'"> </div></td><td> <div class="form-group"> <input type="text" readonly class="form-control warehouse" name="warehouse[]" value="'+response.data.warehouse.name+'"> </div></td><td> <div class="form-group"> <input type="number" class="form-control quantity" name="quantity[]" max="'+response.data.quantity+'" value="6"> </div></td><td> <div class="form-group"> <input type="text" class="form-control unit_price" name="unit_price[]" value="'+response.data.unit_price+'"> </div></td><td> <div class="form-group"> <input type="text" class="form-control selling_price" name="selling_price[]" value="'+response.data.selling_price+'"> </div></td><td class="total-cost">৳0.00</td><td class="text-center"> <a role="button" class="btn btn-danger btn-sm btn-remove">X</a> </td></tr>';
                                $('#product-container').append(html);
                                // console.log(response.data);
                                serials.push(response.data.serial);
                                $('.serial').val('');
                                calculate();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'This product is not available',
                                });
                                calculate();
                            }
                        });
                    }
                    return false; // prevent the button click from happening
                }
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);
                $('#product-container').append(item);
            });


            $('body').on('change', '.customer', function (e) {
                var customer_id = $(this).val();
                if (customer_id != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('customer_due') }}",
                        data: { customer_id: customer_id }
                    }).done(function( response ) {
                        if (response) {
                            // console.log(response);
                            $('#previous_due').val(response);
                            calculate();
                        }
                    });
                }else{
                    $('#previous_due').val(0);
                    calculate();
                }
            });

            $('body').on('click', '.btn-remove', function () {
                var index = $('.btn-remove').index(this);
                $(this).closest('.product-item').remove();

                $('.available-quantity:eq('+index+')').closest('tr').remove();
                calculate();
            });

            $('body').on('keyup', '.quantity, .unit_price, #transport_cost, #return_amount, #discount_percentage, #vat, #discount, #paid', function () {
                calculate();
            });

            $('body').on('change', '.quantity, .unit_price, #transport_cost, #return_amount, #discount_percentage, #previous_due', function () {
                calculate();
            });

            calculate();
        });

        function calculate() {
            var productSubTotal = 0;
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
            $('#vat_total').html('৳' + productTotalVat.toFixed(2));

            var total = parseFloat(productSubTotal) + transport_cost + parseFloat(productTotalVat) - parseFloat(discount) - return_amount;

            var due = parseFloat(total) + previous_due - parseFloat(paid);
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

    </script>
@endsection
