@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Manual Stock Receipt Invoice
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('stock_product_invoice.print', ['order' => $order->id]) }}" class="btn btn-primary">Print</a>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-6 table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Order Type</th>
                                    <td>Manual Stock Order</td>
                                </tr>
                                <tr>
                                    <th>Order No.</th>
                                    <td>{{ $order->order_no }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->date }}</td>
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
                                    <td>{{ $order->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{ $order->customer->mobile_no }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $order->customer->address }}</td>
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
                                @php
                                $totalQuantity = 0;
                                @endphp

                                <tbody>
                                @foreach($order->logs as $log)
                                    @php
                                        $totalQuantity += $log->quantity;
                                    @endphp
                                    <tr>
                                        <td>{{ $log->serial }}</td>
                                        <td>{{ $log->productItem->name??'' }}</td>
                                        <td>{{ $log->productCategory->name??'' }}</td>
                                        <td>{{ $log->warehouse->name??'' }}</td>
                                        <td>{{ $log->quantity }}</td>
                                        <td>৳{{ number_format($log->unit_price, 2) }}</td>
                                        <td>৳{{ number_format($log->total, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total Quantity</th>
                                    <td>{{ number_format($totalQuantity, 2) }}</td>
                                    <th class="text-right">Total Amount</th>
                                    <td>৳{{ number_format($order->total, 2) }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
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
            $('#table-payments').DataTable({
                "order": false,
            });
        });
    </script>
@endsection
