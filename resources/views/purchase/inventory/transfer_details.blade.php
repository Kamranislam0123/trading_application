
@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Stock Transfer Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('stock_transfer_challan', ['order' => $order->id]) }}" class="btn btn-primary">Print</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Warehouse Info</th>
                                </tr>
                                <tr>
                                    <th>Source Warehouse</th>
                                    <td>{{ $order->sourchWarehouse->name??'' }}</td>
                                </tr>
                                <tr>
                                    <th>Target Warehouse</th>
                                    <td>{{ $order->targetWarehouse->name??'' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6 table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Order Info</th>
                                </tr>
                                <tr>
                                    <th>Challan No</th>
                                    <td>{{ $order->order_no }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $order->date }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if(count($order->products) > 0)
                        <br>
                        <div class="table-responsive">
                        <table class="table table-bordered product-table pt-4">
                            <thead>
                            <tr>
                                <th class="text-center" > Code </th>
                                <th>Model</th>
                                <th>Category</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            @php
                                $totalQuantity = 0;
                            @endphp

                            <tbody>
                            @foreach($order->products as $key => $item)
                                @php
                                    $totalQuantity += $item->quantity;
                                @endphp
                                <tr>
                                    <td class="text-center" >{{ $item->serial }}</td>
                                    <td>
                                        {{ $item->productItem->name??'' }}
                                    </td>
                                    <td>
                                        {{ $item->productCategory->name??'' }}
                                    </td>
                                    <td>
                                        {{ $item->quantity }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="text-right">Total Quantity</th>
                                <th>{{$totalQuantity}}</th>
                            </tr>
                        </table>
                        </div>
                    @endif
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



