@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Stock Transfer
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
                    <h3 class="box-title">Stock Transfer Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('stock_transfer_edit',['order'=> $order->id]) }}">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('source_warehouse') ? 'has-error' :'' }}">
                                    <label>Source Warehouse *</label>

                                    <select class="form-control select2 source_warehouse" style="width: 100%;" name="source_warehouse" id="source_warehouse" data-placeholder="source_warehouse" required>
                                        @foreach($source_warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('source_warehouse',$order->sourch_warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('source_warehouse')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('target_warehouse') ? 'has-error' :'' }}">
                                    <label>Target Warehouse *</label>

                                    <select class="form-control select2" style="width: 100%;" name="target_warehouse" id="target_warehouse" data-placeholder="target_warehouse" required>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('target_warehouse',$order->target_warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('target_warehouse')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product Name *</th>
                                    <th width="15%">Available Stock</th>
                                    <th width="20%">Transfer Quantity *</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                @if (old('product') != null && sizeof(old('product')) > 0)
                                    @foreach(old('product') as $item)
                                        <tr class="product-item">
                                            <td>
                                                <div class="form-group {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                    <select class="form-control product" style="width: 100%;" name="product[]">
                                                        <option value="">Select Product</option>

                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" {{ old('product.'.$loop->parent->index) == $product->id ? 'selected' : '' }}>
                                                                {{ $product->serial }} - {{ $product->productItem->name??'' }} - {{ $product->productCategory->name??'' }} - {{ $product->warehouse->name??'' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td><span class="available_qty"></span></td>

                                            <td>
                                                <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else

                                    @foreach($order->products as $orderProduct)
                                    <tr class="product-item">
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control product" style="width: 100%;" name="product[]">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ $product->id == $orderProduct->purchase_inventory_id ? 'selected' : '' }}>
                                                            {{ $product->serial }} - {{ $product->productItem->name??'' }} - {{ $product->productCategory->name??'' }} - {{ $product->warehouse->name??'' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td><span class="available_qty"></span></td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{$orderProduct->quantity}}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td>
                                        <a role="button" class="btn btn-info btn-sm" id="btn-add-product">Add More</a>
                                    </td>
                                    <td id="total_stock_quantity">0</td>
                                    <td id="transfer_quantity">0</td>
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
        <tr class="product-item">
            <td>
                <div class="form-group">
                    <select class="form-control product" style="width: 100%;" name="product[]" required>
                        <option value="">Select Product</option>

                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->serial }} - {{ $product->productItem->name??'' }} - {{ $product->productCategory->name??'' }} - {{ $product->warehouse->name??'' }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td><span class="available_qty"></span></td>
            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $(function () {

            var error = '{{ session('error') }}';

            if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
                if (error != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error,
                    });
                }
            }


            //Initialize Select2 Elements
            $('.select2,.product').select2();

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                $('#product-container').append(item);

                initProduct();

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }
                calculate();
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
                calculate();
            });


            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            initProduct();

            $('body').on('change','.product', function () {
                var productId = $(this).val();
                var productItem = $(this);
                productItem.closest('tr').find('.available_qty').html('');
                productItem.closest('tr').find('.available_warehouse').html('');
                if (productId) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_inventory_details') }}",
                        data: { productId: productId }
                    }).done(function( response ) {
                        console.log(response);
                        productItem.closest('tr').find('.available_qty').html(response.quantity);
                        calculate();
                    });
                }
            });
            $(".product").trigger("change");

            $('body').on('keyup', '.quantity', function () {
                calculate();
            });

            $('body').on('change', '.quantity', function () {
                calculate();
            });

        });

        function calculate() {
            var totalQuantity = 0;
            var total_transfer_quantity = 0;

            $('.product-item').each(function(i, obj) {
                var quantity = $('.available_qty:eq('+i+')').html();
                var transfer_quantity = $('.quantity:eq('+i+')').val();

                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (transfer_quantity == '' || transfer_quantity < 0 || !$.isNumeric(transfer_quantity))
                    transfer_quantity = 0;

                //$('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                total_transfer_quantity += parseFloat(transfer_quantity);
                totalQuantity += parseFloat(quantity);
            });

            $('#total_stock_quantity').html(totalQuantity);
            $('#transfer_quantity').html(total_transfer_quantity);
        }


        function initProduct() {
            $('.select2,.product').select2();
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
