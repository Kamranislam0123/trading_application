@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Return product edit
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
                    <h3 class="box-title">Return Product Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('sales_return.edit', ['purchase_inventory_log'=>$purchase_inventory_log->id]) }}">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}" id="form-group-customer">
                                    <label>Customer *</label>
                                    <select class="form-control select2 customer" style="width: 100%;" id="customer" name="customer" required>
                                        <option value="">Select Customer </option>
                                        @foreach (App\Model\Customer::where('status',1)->get() as $customer)
                                            <option value="{{ $customer->id }}" @if (old('customer', $purchase_inventory_log->customer_id) == $customer->id) selected @endif>{{ $customer->name }}--{{ $customer->address }}--{{ $customer->mobile_no??'' }}--{{$customer->branch->name??'' }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date *</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date" name="date" value="{{ old('date', $purchase_inventory_log->date?date('Y-m-d', strtotime($purchase_inventory_log->date)):'') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group {{ $errors->has('sales_order_no') ? 'has-error' :'' }}">
                                    <label> Sales Order no </label>

                                    <input class="form-control" type="text" name="sales_order_no" value="{{ old('sales_order_no', $purchase_inventory_log->sales_order_no) }}">

                                    @error('sales_order_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
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
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Code </th>
                                        <th>Product Model </th>
                                        <th>Product Category </th>
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Unit Price (Sale)</th>
                                        <th>Total Cost</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody id="product-container">
                                    <tr class="product-item">
                                        <td>
                                            <div class="form-group">
                                                <input type="text" readonly class="form-control serial" value="{{ $purchase_inventory_log->serial }}" name="serial" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control product_item select2" style="width: 100%;" name="product_item" required>
                                                    <option value="{{ $purchase_inventory_log->product_item_id }}" selected > {{ $purchase_inventory_log->productItem->name??'' }} </option>

                                                    {{-- @foreach($productItems as $productItem)
                                                        <option value="{{ $productItem->id }}">{{ $productItem->name }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control product_category select2" style="width: 100%;" name="product_category" required>
                                                    <option value="{{ $purchase_inventory_log->product_category_id }}" selected > {{ $purchase_inventory_log->productCategory->name??'' }} </option>

                                                    {{-- @foreach($product_categories as $product_category)
                                                        <option value="{{ $product_category->id }}">{{ $product_category->name }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="number" step="any" class="form-control quantity" value="{{ $purchase_inventory_log->quantity }}" name="quantity" required>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control unit_price" name="unit_price" value="{{ $purchase_inventory_log->selling_price }}" required>
                                            </div>
                                        </td>


                                        {{-- <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control selling_price" name="selling_price" value="{{ $purchase_inventory_log->selling_price }}" required>
                                            </div>
                                        </td> --}}

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
                    <select class="form-control product_item select2" style="width: 100%;" name="product_item[]" required>
                        <option value="">Select Product Item</option>

                        @foreach($productItems as $productItem)
                            <option value="{{ $productItem->id }}">{{ $productItem->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <select class="form-control product_category select2" style="width: 100%;" name="product_category[]" required>
                        <option value="">Select Product Category</option>

                        @foreach($product_categories as $product_category)
                            <option value="{{ $product_category->id }}">{{ $product_category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" id="quantity" value="6" name="quantity[]" required>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" id="unit_price" name="unit_price[]" value="0" required>
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
                format: 'yyyy-mm-dd',
                todayHighlight: true
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                // item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));
                $('#product-container').append(item);
                initProduct();
                $('.select2').select2();
                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('keyup', '.quantity, .unit_price', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }
            calculate();
        });

        function calculate() {
            var transport_cost = parseFloat($("#transport_cost").val()||0);
            var quantity = parseFloat($(".quantity").val()||0);
            var unit_price = parseFloat($(".unit_price").val()||0);
            total = quantity * unit_price;
            allTotal = parseFloat(total);
            $('.total-cost').html('৳' + allTotal.toFixed(2));
            $('#total-amount').html('৳' + total.toFixed(2));
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
