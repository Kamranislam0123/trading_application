@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Stock Product Invoice
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
                <div class="box-body">
                    <a class="btn btn-primary" href="{{ route('product_stock.add') }}">Add Manual Stock</a>

                    <hr>
                    <div class="table-responsive">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order No</th>
                            <th>Customer</th>
                            <th>Mobile No</th>
                            <th>Warehouse</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($manualStockOrders as $manualStockOrder)
                            <tr>
                                <td>{{ $manualStockOrder->date }}</td>
                                <td>{{ $manualStockOrder->order_no }}</td>
                                <td>{{ $manualStockOrder->customer->name }}</td>
                                <td>{{ $manualStockOrder->customer->mobile_no }}</td>
                                <td>{{ $manualStockOrder->warehouse->name }}</td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('stock_product.invoice', ['order' => $manualStockOrder->id]) }}">Details</a>
                                    <a class="btn btn-warning btn-sm" href="{{ route('stock_product.barcode', ['order' => $manualStockOrder->id]) }}">Barcode</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $('#table').DataTable();
        })
    </script>
@endsection
