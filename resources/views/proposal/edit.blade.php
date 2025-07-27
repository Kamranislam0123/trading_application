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
    Update Proposal
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('proposal.edit', ['proposal'=>$proposal->id]) }}">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Proposal Information</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}" id="form-group-customer">
                                <label>Customer *</label>

                                <select class="form-control select2 customer" style="width: 100%;" id="customer" name="customer" required>
                                    <option value="">Select Customer </option>
                                    @foreach (App\Model\Customer::all() as $customer)
                                        <option value="{{ $customer->id }}" @if (old('customer', $proposal->customer_id) == $customer->id) selected @endif>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('sub_customer') ? 'has-error' :'' }}" id="form-group-customer">
                                <label> Sub Customer</label>

                                <select class="form-control select2" style="width: 100%;" id="sub_customer" name="sub_customer">
                                    <option value="{{ $proposal->sub_customer_id }}"> {{ $proposal->subCustomer->name??'Select Sub Customer' }} </option>
                                    
                                </select>
                                @error('sub_customer')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                <label>Date *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : $proposal->date->format('Y-m-d')) : old('date') }}" autocomplete="off" required>
                                </div>
                                <!-- /.input group -->

                                @error('date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('title') ? 'has-error' :'' }}">
                                <label>Proposal Title *</label>

                                <input class="form-control" type="text" name="title" value="{{ old('title', $proposal->title) }}" required>

                                @error('title')
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Product Item</th>
                                <th>Warehouse</th>
                                {{-- <th>Product</th> --}}
                                <th width="10%">Quantity</th>
                                <th width="15%">Unit Price</th>
                                <th>Total Cost</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody id="product-container">
                            @if (old('product_item') != null && sizeof(old('product_item')) > 0)
                                @foreach(old('product_item') as $item)
                                    <tr class="product-item">
                                        <input type="hidden" name="product[]" value="1">
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control product_item select2" style="width: 100%;" name="product_item[]" required>
                                                    <option value="">Select Product Item</option>

                                                    @foreach($productItems as $productItem)
                                                        <option value="{{ $productItem->id }}" {{ old('product_item.'.$loop->parent->index) == $productItem->id ? 'selected' : '' }}>{{ $productItem->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <select class="form-control warehouse select2" style="width: 100%;" name="warehouse[]" required>
                                                    {{-- <option value="">Select Warehouse</option> --}}

                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}" {{ old('warehouse.'.$loop->parent->index) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        {{-- <td>
                                            <div class="form-group">
                                                <select class="form-control product select2" style="width: 100%;" name="product[]" data-selected="{{ old('product.'.$loop->index) }}" required>
                                                    <option value="">Select Product</option>
                                                </select>
                                            </div>
                                        </td> --}}

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

                                        <td class="total-cost">৳0.00</td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="7" class="available-quantity" style="font-weight: bold"></td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($proposal->products as $item)
                                    <tr class="product-item">
                                        <input type="hidden" name="product[]" value="1">
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control product_item select2" style="width: 100%;" name="product_item[]" required>
                                                    <option value="">Select Product Item</option>

                                                    @foreach($productItems as $productItem)
                                                        <option value="{{ $productItem->id }}" {{ old('product_item',$item->product_item_id) == $productItem->id ? 'selected' : '' }}>{{ $productItem->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <select class="form-control warehouse select2" style="width: 100%;" name="warehouse[]" required>
                                                    {{-- <option value="">Select Warehouse</option> --}}

                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}" {{ old('warehouse', $item->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        {{-- <td>
                                            <div class="form-group">
                                                <select class="form-control product select2" style="width: 100%;" name="product[]" required>
                                                    <option value="{{ $item->product_id }}">{{ $item->product->name??'' }}</option>
                                                </select>
                                            </div>
                                        </td> --}}

                                        <td>
                                            <div class="form-group {{ $errors->has('quantity') ? 'has-error' :'' }}">
                                                <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity',$item->quantity) }}">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price',$item->unit_price) }}">
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
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                    <a role="button" class="btn btn-info btn-sm" id="btn-add-product" style="margin-bottom: 10px">Add Product</a>
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
                <!-- /.box-header -->

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered">
                            <tr>
                                <th colspan="4" class="text-right">Product Sub Total</th>
                                <th id="product-sub-total">৳0.00</th>
                            </tr>
                            {{-- <tr>
                                <th colspan="4" class="text-right">Service Sub Total</th>
                                <th id="service-sub-total">৳0.00</th>
                            </tr> --}}
                            <tr>
                                <th colspan="4" class="text-right"> Installation Charge </th>
                                <td>
                                    <div class="form-group {{ $errors->has('installation_charge') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="installation_charge" id="installation_charge" value="{{ old('service_discount', $proposal->installation_charge) }}">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">TAX </th>
                                <td>
                                    <div class="form-group {{ $errors->has('tax') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="tax" id="tax" value="{{ old('tax',$proposal->tax) }}">
                                        {{-- <span id="tax_total">৳0.00</span> --}}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">VAT</th>
                                <td>
                                    <div class="form-group {{ $errors->has('vat') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="vat" id="vat" value="{{ old('vat',$proposal->vat) }}">
                                        {{-- <span id="vat_total">৳0.00</span> --}}
                                    </div>
                                </td>
                            </tr>
                            {{-- <tr>
                                <th colspan="4" class="text-right">Service VAT (%)</th>
                                <td>
                                    <div class="form-group {{ $errors->has('service_vat') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="service_vat" id="service_vat" value="{{ empty(old('service_vat')) ? ($errors->has('service_vat') ? '' : '0') : old('service_vat') }}">
                                        <span id="service_vat_total">৳0.00</span>
                                    </div>
                                </td>
                            </tr> --}}
                            <tr>
                                <th colspan="4" class="text-right">Product Discount</th>
                                <td>
                                    <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="discount" id="discount" value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : $proposal->discount) : old('discount',$proposal->discount) }}">
                                    </div>
                                </td>
                            </tr>
                            {{-- <tr>
                                <th colspan="4" class="text-right">Service Discount</th>
                                <td>
                                    <div class="form-group {{ $errors->has('service_discount') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="service_discount" id="service_discount" value="{{ empty(old('service_discount')) ? ($errors->has('service_discount') ? '' : '0') : old('service_discount') }}">
                                    </div>
                                </td>
                            </tr> --}}
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th id="final-amount">৳0.00</th>
                            </tr>
                            {{-- <tr>
                                <th colspan="4" class="text-right">Paid</th>
                                <td>
                                    <div class="form-group {{ $errors->has('paid') ? 'has-error' :'' }}">
                                        <input type="text" class="form-control" name="paid" id="paid" value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid') }}">
                                    </div>
                                </td>
                            </tr> --}}
                            {{-- <tr>
                                <th colspan="4" class="text-right">Due</th>
                                <th id="due">৳0.00</th>
                            </tr> --}}
                            {{-- <tr id="tr-next-payment">
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
                            </tr> --}}
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <input type="hidden" name="total" id="total">
                    <input type="hidden" name="due_total" id="due_total">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

    <template id="template-product">
        <tr class="product-item">
            <input type="hidden" name="product[]" value="1">
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
                    <select class="form-control warehouse select2" style="width: 100%;" name="warehouse[]" required>
                        {{-- <option value="">Select Warehouse</option> --}}

                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            {{-- <td>
                <div class="form-group">
                    <select class="form-control product select2" style="width: 100%;" name="product[]" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
            </td> --}}

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]">
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

    <template id="template-service">
        <tr class="service-item">
            <td>
                <div class="form-group">
                    <input type="text" class="form-control service_name" name="service_name[]" autocomplete="off">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control service_quantity" name="service_quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control service_unit_price" name="service_unit_price[]">
                </div>
            </td>

            <td class="service-total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove-service">X</a>
            </td>
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
                $('#product-container').append(item);

                initProduct();
            });

            $('body').on('change', '.product_item', function () {
                var productItemId = $(this).val();
                var productHtml = $(this).closest('.product-item').find('.product')
                productHtml.html('<option value="">Select Product</option>');
                var index = $(".product_item").index(this);

                if (productItemId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_products') }}",
                        data: {productItemId: productItemId}
                    }).done(function (response) {
                        var selected = $('.product:eq('+index+')').data('selected');

                        $.each(response, function( index, item ) {
                            if (selected == item.id)
                                productHtml.append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                productHtml.append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        // $('.warehouse').trigger('change');
                    });
                }
            });

            // $('.product_item').trigger('change');

            // product description start
            $('body').on('change', '.product', function () {
                var product_Id = $(this).val();
                var descriptionHtml = $(this).closest('.product').find('.description')
                descriptionHtml.html('<option value="">Select Description</option>');
                var index = $(".product").index(this);

                if (product_Id != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_description') }}",
                        data: {product_Id: product_Id}
                    }).done(function (response) {

                        var selected = $('.description:eq('+index+')').data('selected');

                        $.each(response, function( index, item ) {
                            if (selected == item.id)
                                $('.description').append('<option value="'+item.id+'" selected>'+item.description+'</option>');
                            else
                                $('.description').append('<option value="'+item.id+'">'+item.description+'</option>');
                        });

                        // $('.warehouse').trigger('change');
                    });
                }
            });
            // $('.product').trigger('change');
            // product description end

            $('body').on('change', '.product, .warehouse', function (e) {
                var productId = $(this).closest('.product-item').find('.product').val();
                var warehouseId = $(this).closest('.product-item').find('.warehouse').val();
                $this = $(this);
                var index = $('.' + e.target.name.slice(0, -2)).index(this);

                if (productId != '' && warehouseId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('sale_product.details') }}",
                        data: { productId: productId, warehouseId: warehouseId }
                    }).done(function( response ) {
                        if (response.success) {
                            $this.closest('tr').find('.quantity').val(1);
                            $this.closest('tr').find('.quantity').attr({
                                "max" : response.count,
                                "min" : 1
                            });
                            $this.closest('tr').find('.unit_price').val(response.data.last_selling_price);
                            // $('.available-quantity:eq('+index+')').html('Available: ' + response.count);
                            calculate();
                        } else {
                            $this.closest('tr').find('.quantity').val(1);
                            $this.closest('tr').find('.unit_price').val('');
                            // $('.available-quantity:eq('+index+')').html('');
                            calculate();
                        }
                    });
                }
            });

            // $('.warehouse').trigger('change');

            $('body').on('click', '.btn-remove', function () {
                var index = $('.btn-remove').index(this);
                $(this).closest('.product-item').remove();

                $('.available-quantity:eq('+index+')').closest('tr').remove();
                calculate();
            });

            $('body').on('keyup', '.quantity, .unit_price, .service_quantity, .service_unit_price, #vat, #service_vat, #discount, #service_discount, #paid,#installation_charge, #tax', function () {
                calculate();
            });

            $('body').on('change', '.quantity, .unit_price, .service_quantity, .service_unit_price, #tax', function () {
                calculate();
            });

            calculate();

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

            // Service
            $('#btn-add-service').click(function () {
                var html = $('#template-service').html();
                var item = $(html);

                $('#service-container').append(item);

                if ($('.product-item').length + $('.service-item').length >= 1 ) {
                    $('.btn-remove').show();
                    $('.btn-remove-service').show();
                }
            });

            $('body').on('click', '.btn-remove-service', function () {
                $(this).closest('.service-item').remove();
                calculate();

                if ($('.product-item').length + $('.service-item').length <= 1 ) {
                    $('.btn-remove').hide();
                    $('.btn-remove-service').hide();
                }
            });
        });

        function calculate() {
            var productSubTotal = 0;
            var serviceSubTotal = 0;

            var vat = $('#vat').val();
            var tax = $('#tax').val();
            var discount = $('#discount').val();
            var serviceVat = $('#service_vat').val();
            var serviceDiscount = $('#service_discount').val();
            var paid = $('#paid').val();
            var installation_charge = $('#installation_charge').val();

            if (vat == '' || vat < 0 || !$.isNumeric(vat))
                vat = 0;

            if (tax == '' || tax < 0 || !$.isNumeric(tax))
                tax = 0;

            if (discount == '' || discount < 0 || !$.isNumeric(discount))
                discount = 0;

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;

            if (serviceVat == '' || serviceVat < 0 || !$.isNumeric(serviceVat))
                serviceVat = 0;

            if (serviceDiscount == '' || serviceDiscount < 0 || !$.isNumeric(serviceDiscount))
                serviceDiscount = 0;

            if (installation_charge == '' || installation_charge < 0 || !$.isNumeric(installation_charge))
                installation_charge = 0;

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

            $('.service-item').each(function(i, obj) {
                var quantity = $('.service_quantity:eq('+i+')').val();
                var unit_price = $('.service_unit_price:eq('+i+')').val();


                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                $('.service-total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                serviceSubTotal += quantity * unit_price;
            });


            var productTotalVat = (productSubTotal * vat) / 100;
            var serviceTotalVat = (serviceSubTotal * serviceVat) / 100;


            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#service-sub-total').html('৳' + serviceSubTotal.toFixed(2));

            $('#vat_total').html('৳' + productTotalVat.toFixed(2));
            $('#service_vat_total').html('৳' + serviceTotalVat.toFixed(2));

            var total = parseFloat(productSubTotal) + parseFloat(serviceSubTotal) +
                parseFloat(vat) + parseFloat(serviceTotalVat) + parseFloat(installation_charge) -
                parseFloat(discount) - parseFloat(serviceDiscount)+parseFloat(tax);

            var due = parseFloat(total) - parseFloat(paid);
            $('#final-amount').html('৳' + total.toFixed(2));
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
            $('.product, .warehouse, .product_item').select2();
        }
    </script>
@endsection
