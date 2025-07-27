@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Manual Stock Receipt Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('stock_receipt.print', ['purchase_inventory_log' => $purchase_inventory_log->id]) }}" class="btn btn-primary">Print</a>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $purchase_inventory_log->date->format('j F, Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6 table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Customer Info</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $purchase_inventory_log->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{ $purchase_inventory_log->customer->mobile_no }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $purchase_inventory_log->customer->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th> Code </th>
                                    <th> Model </th>
                                    <th> Category </th>
                                    <th> Warehouse </th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>{{ $purchase_inventory_log->serial }}</td>
                                        <td>{{ $purchase_inventory_log->productItem->name??'' }}</td>
                                        <td>{{ $purchase_inventory_log->productCategory->name??'' }}</td>
                                        <td>{{ $purchase_inventory_log->warehouse->name??'' }}</td>
                                        <td>{{ $purchase_inventory_log->quantity }}</td>
                                        <td>৳{{ number_format($purchase_inventory_log->unit_price, 2) }}</td>
                                        <td>৳{{ number_format($purchase_inventory_log->total, 2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th class="text-right" colspan="6">Total Amount</th>
                                    <td>৳{{ number_format($purchase_inventory_log->total, 2) }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

{{--                    <div class="row">--}}
{{--                        <div class="col-md-offset-8 col-md-4">--}}
{{--                            <table class="table table-bordered">--}}
{{--                                <tr>--}}
{{--                                    <th>Total Amount</th>--}}
{{--                                    <td>৳{{ number_format($purchase_inventory_log->total, 2) }}</td>--}}
{{--                                </tr>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                    </div>--}}
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
            $('#table-payments').DataTable({
                "order": false,
            });
        });
    </script>
@endsection
