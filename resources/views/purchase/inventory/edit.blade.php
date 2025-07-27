@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Purchase Inventory Edit
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Purchase Inventory Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('purchase_inventory.edit', ['purchase_inventory'=>$purchase_inventory->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Serial </th>
                                    <th>Product Model </th>
                                    <th>Product Category </th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Unit Price</th>
                                    <th width="10%">Selling Price</th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                <tr class="product-item">
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_serial" name="product_serial" value="{{$purchase_inventory->serial??''}}" readonly required style="width: 100%;" >
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_item" name="product_item" value="{{$purchase_inventory->productItem->name??''}}" readonly required style="width: 100%;">
                                            <input type="hidden" class="form-control" name="purchase_inventory_id" value="{{$purchase_inventory->id}}">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control product_category" name="product_category" value="{{$purchase_inventory->productCategory->name??''}}" readonly required style="width: 100%;" >
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="number" step="any" class="form-control quantity" value="{{ $purchase_inventory->quantity }}" name="quantity" required>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control unit_price" name="unit_price" value="{{ $purchase_inventory->unit_price }}" required>
                                        </div>
                                    </td>


                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control selling_price" name="selling_price" value="{{ $purchase_inventory->selling_price }}" required>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
{{--                    <!-- /.box-body -->--}}

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
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection
