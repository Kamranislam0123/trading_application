@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Sale Return Receipt Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
{{--                            <a target="_blank" href="{{ route('sale_receipt.super_market_requisition_print', ['order' => $purchase_inventory_log->id]) }}" class="btn btn-primary"> Req.Super Market</a>--}}
{{--                            <a target="_blank" href="{{ route('sale_receipt.flat_requisition_print', ['order' => $purchase_inventory_log->id]) }}" class="btn btn-primary"> Req.Flat  </a>--}}
{{--                            <a target="_blank" href="{{ route('sale_receipt.chalan.print', ['order' => $purchase_inventory_log->id,]) }}" class="btn btn-primary"> Challan </a>--}}
                            <a target="_blank" href="{{ route('sale_return_receipt.print', ['purchase_inventory_log' => $purchase_inventory_log->id]) }}" class="btn btn-success"> Invoice Print </a>
{{--                             <a target="_blank" href="{{ route('sale_receipt.wpad_print', ['order' => $purchase_inventory_log->id]) }}" class="btn btn-success"> W Pad Invoice </a> --}}
                        </div>
                    </div>

                    <hr>

                    <div class="row">
{{--                        <div class="col-md-6">--}}
{{--                            <table class="table table-bordered">--}}
{{--                                <tr>--}}
{{--                                    <th>Order No.</th>--}}
{{--                                    <td>{{ $purchase_inventory_log->order_no }}</td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <th>Order Date</th>--}}
{{--                                    <td>{{ $purchase_inventory_log->date->format('j F, Y') }}</td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <th> Received by: </th>--}}
{{--                                    <td>{{ $purchase_inventory_log->received_by }}</td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <th>Next Payment Date</th>--}}
{{--                                    <td>{{ $purchase_inventory_log->next_payment ? $purchase_inventory_log->next_payment->format('j F, Y') : '' }}</td>--}}
{{--                                </tr>--}}
{{--                            </table>--}}
{{--                        </div>--}}

                        <div class="col-md-offset-3 col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Product Return Invoice</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-center"> Customer Info</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $purchase_inventory_log->customer->name??'' }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile No.</th>
                                    <td>{{ $purchase_inventory_log->customer->mobile_no??'' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $purchase_inventory_log->customer->address??'' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center" > Code </th>
                                    <th> Model </th>
                                    <th> Category </th>
                                    <th> Warehouse </th>
                                    <th> Quantity </th>
                                    <th class="text-right" >Salling Price</th>
                                    <th class="text-right" >Total</th>
                                </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td class="text-center" >{{ $purchase_inventory_log->serial }}</td>
                                        <td>
                                            {{ $purchase_inventory_log->productItem->name??'' }}
                                        </td>
                                        <td>
                                            {{ $purchase_inventory_log->productCategory->name??'' }}
                                        </td>
                                        <td>
                                            {{ $purchase_inventory_log->warehouse->name??'' }}
                                        </td>
                                        <td>
                                            {{ $purchase_inventory_log->quantity }}
                                        </td>
                                        <td class="text-right" width="100">
                                            @if(auth()->user()->role == 2)
                                            ৳ {{ number_format(($purchase_inventory_log->unit_price + nbrSellCalculation($purchase_inventory_log->unit_price)) * $purchase_inventory_log->quantity, 2) }}
                                            @else
                                            ৳ {{ number_format($purchase_inventory_log->selling_price, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right" width="100">
                                            @if(auth()->user()->role == 2)
                                                ৳ {{ number_format($purchase_inventory_log->unit_price + nbrSellCalculation($purchase_inventory_log->unit_price), 2) }}
                                            @else
                                                ৳ {{ number_format($purchase_inventory_log->sale_total, 2) }}
                                            @endif

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-offset-8 col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th> Invoice Total</th>
                                    <td class="text-right" >৳{{ number_format($purchase_inventory_log->sale_total, 2) }}</td>
                                </tr>
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
