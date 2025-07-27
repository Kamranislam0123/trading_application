@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Sale Receipt Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('sale_receipt.super_market_requisition_print', ['order' => $order->id]) }}" class="btn btn-primary"> Req.Super Market</a>
                            <a target="_blank" href="{{ route('sale_receipt.flat_requisition_print', ['order' => $order->id]) }}" class="btn btn-primary"> Req.Flat  </a>
                            <a target="_blank" href="{{ route('sale_receipt.chalan.print', ['order' => $order->id,]) }}" class="btn btn-primary"> Challan </a>
                            <a target="_blank" href="{{ route('sale_receipt.print', ['order' => $order->id]) }}" class="btn btn-success"> Invoice </a>
                            {{-- <a target="_blank" href="{{ route('sale_receipt.wpad_print', ['order' => $order->id]) }}" class="btn btn-success"> W Pad Invoice </a> --}}
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Order No.</th>
                                    <td>{{ $order->order_no }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->date->format('j F, Y') }}</td>
                                </tr>
                                <tr>
                                    <th> Received by: </th>
                                    <td>{{ $order->received_by }}</td>
                                </tr>
                                <tr>
                                    <th>Next Payment Date</th>
                                    <td>{{ $order->next_payment ? $order->next_payment->format('j F, Y') : '' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center"> Customer Info</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $order->customer->name??'' }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile No.</th>
                                    <td>{{ $order->customer->mobile_no??'' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $order->customer->address??'' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @php
                        $totalQuantity = 0;
                    @endphp

                    @if(count($order->products) > 0)
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
                                            <th class="text-right" >Unit Price</th>
                                            <th class="text-right" >Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                        $subTotal = 0;
                                        $total = 0;
                                    $totalAmount = 0;
                                    ?>
                                        @foreach($order->products as $key => $item)

                                            @php
                                              $totalQuantity += $item->quantity;
                                               if(auth()->user()->role == 2){
                                                   $subTotal  += ($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity;
                                                    $total = (($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity) + $order->transport_cost + $order->vat - $order->discount - $order->return_amount;
                                                    $totalAmount = ((($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity) + $order->transport_cost + $order->vat - $order->discount - $order->return_amount) + $order->paid;

                                               } else{
                                                   $subTotal = $order->sub_total;
                                                   $total = $order->total;
                                                   $totalAmount = $order->current_due + $order->paid;

                                               }
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
                                                    {{ $item->warehouse->name??'' }}
                                                </td>
                                                <td>
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="text-right" width="100">
                                                    @if(auth()->user()->role == 2)
                                                    ৳ {{ number_format($item->buy_price + nbrSellCalculation($item->buy_price), 2) }}
                                                    @else
                                                    ৳ {{ number_format($item->unit_price, 2) }}
                                                    @endif
                                                </td>
                                                <td class="text-right" width="100">
                                                    @if(auth()->user()->role == 2)
                                                        ৳ {{ number_format(($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity, 2) }}
                                                    @else
                                                    ৳ {{ number_format($item->total, 2) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th class="text-right" colspan="4">Total Quantity</th>
                                            <td class="text-left" >{{$totalQuantity}}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-offset-6 col-md-6 table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Product Sub Total</th>
                                    <td class="text-right" >৳{{ number_format($subTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Invoice Total</th>
                                    <td class="text-right" >৳{{ number_format($total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Previous Due</th>
                                    {{--                                    <td class="text-right" >৳{{ number_format($order->customer->due - ($order->total-$order->paid), 2) }}</td>--}}
                                    <td class="text-right" >৳{{ number_format($order->previous_due, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Transport Cost</th>
                                    <td class="text-right" >৳{{ number_format($order->transport_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Return Amount </th>
                                    <td class="text-right" >৳{{ number_format($order->return_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Discount</th>
                                    <td class="text-right" >৳{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Total Amount </th>
                                <!--<td class="text-right" >৳{{ number_format($order->customer->due + $order->paid, 2) }}</td>-->
                                    <td class="text-right" >৳{{ number_format($totalAmount,2) }}</td>
                                </tr>
                                @if(auth()->user()->role == 1)
                                <tr>
                                    <th>Cash Paid </th>
                                    <td class="text-right" >৳{{ number_format($order->paid, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Cheque Due </th>
                                    <td class="text-right" >৳{{ number_format($order->client_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Current Due</th>
                                    <td class="text-right" >৳{{ number_format($order->current_due, 2) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
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
