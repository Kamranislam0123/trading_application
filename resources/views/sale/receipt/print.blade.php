<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <style>
        @page {
            @top-center {
                content: element(pageHeader);
            }
        }
        #pageHeader{
            position: running(pageHeader);
        }

        table.table-bordered{
            border:1px solid black !important;
            margin-top:20px;
        }
        table.table-bordered th{
            border:1px solid black !important;
        }
        table.table-bordered td{
            border:1px solid black !important;
        }

        .product-table th, .table-summary th {
            padding: 2px !important;
            text-align: center !important;
        }

        .product-table td, .table-summary td {
            padding: 2px !important;
            text-align: center !important;
        }

        /*@media screen {*/
        /*    div.divFooter {*/
        /*        display: none;*/
        /*    }*/
        /*}*/
        /*@media print {*/
        /*    div.divFooter {*/
        /*        position: fixed;*/
        /*        bottom: 0;*/
        /*    }*/
        /*}*/
    </style>
</head>
<body>
<header id="pageHeader" style="margin-bottom: 10px">
    <div class="row">
        {{-- <div class="col-xs-3 col-xs-offset-1">
            <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px">
        </div> --}}

        <div class="col-xs-12">
            @if ($order->company_branch_id == 2)
                <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
            @else
            <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
            @endif
            <br>
        </div>
    </div>
</header>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 text-center" style="border: 1px solid black; padding: 3px; border-radius: 7px">
            <strong>Invoice</strong>
        </div>
    </div>
    <br>
    <div class="row" style="border: 1px solid black; margin-top: 3px; font-size: 12px">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-6">
                    <strong>Name: </strong>{{ $order->customer->name??'' }} <br>
                    <strong>Address: </strong>{{$order->customer->address??'' }} <br>
                    <strong>Mobile No. : </strong>{{$order->customer->mobile_no??'' }} <br>
                    <strong> Received by : </strong>{{ $order->received_by }}
                </div>

                <div class="col-xs-6 text-right">
                    <strong>Customer ID : </strong>{{ $order->customer->id }} <br>
                    <strong>Invoice No : </strong>{{ $order->order_no }} <br>
                    <strong>Date : </strong>{{ $order->date->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $totalQuantity = 0;
@endphp

     @if(count($order->products) > 0)
    <br>
    <table class="table table-bordered product-table pt-4" style="margin-top: 5px; margin-bottom: 1px !important; font-size: 12px">
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
            <?php
                $subTotal = 0;
                $total = 0;
                $totalAmount = 0;
            ?>
        <tbody>
            @foreach($order->products as $key => $item)
                @php
                    $totalQuantity += $item->quantity;
                     if(auth()->user()->role == 2){
                            $subTotal  += ($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity;
                            $total += (($item->buy_price + nbrSellCalculation($item->buy_price)) * $item->quantity) + $order->transport_cost + $order->vat - $order->discount - $order->return_amount;
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
        </tbody>
            <tr>
                <th class="text-right" colspan="4">Total Quantity</th>
                <th class="text-left" >{{$totalQuantity}}</th>
                <td></td>
                <td></td>
            </tr>

    </table>
@endif

<div class=" table-summary-one" style="width: 40%; float: left; font-size: 12px;margin-top: 20px">
    <strong><h4>Cheque Information</h4></strong>
    <table class="table table-bordered">
        @if ($order->client_amount > 0)
            <tr>
                <th class="text-center"> Cheque Due </th>
                <td class="text-center">৳{{ number_format($order->client_amount, 2) }}</td>
            </tr>
        @endif

        @if ($order->client_cheque_no)
            <tr>
                <th class="text-center">Cheque No</th>
                <td class="text-center">{{ $order->client_cheque_no }}</td>
            </tr>
        @endif
            @if ($order->cheque_date)
                <tr>
                    <th class="text-center">Cheque Date</th>
                    <td class="text-center">{{ date('d-m-Y',strtotime($order->cheque_date)) }}</td>
                </tr>
            @endif
    </table>

</div>

        <div class="table-summary" style="width: 50%; float: right; font-size: 12px">
            <br>
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
                    {{--            <td class="text-right" >৳{{ number_format($order->customer->due - ($order->total+$order->paid), 2) }}</td>--}}
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
                <!--<td class="text-right" >৳{{ number_format($order->previous_due+$order->paid, 2) }}</td>-->
                    <td class="text-right" >৳{{ number_format($totalAmount, 2) }}</td>
                </tr>
                @if(auth()->user()->role == 1)
                <tr>
                    <th>Cash Paid </th>
                    <td class="text-right" >৳{{ number_format($order->paid, 2) }}</td>
                </tr>
                <tr>
                    <th> Current Due</th>
                    <td class="text-right" >৳{{ number_format($order->current_due, 2) }}</td>
                </tr>
               @endif
            </table>
        </div>
@if(auth()->user()->role == 1)
    <div class="text-left" style="clear: both">
        <strong>In Word: {{ $order->amount_in_word??'' }} Only</strong>
    </div>
@endif

    <div class="text-center" style="clear: both;margin-top: 30px;margin-bottom: 30px">
        <strong>বিঃদ্রঃ বিক্রিত মাল ফেরত হয় না, চায়না মালে কোন গ্যারান্টি নাই। ধন্যবাদ</strong>
    </div>


    <div class="divFooter" style="width: 100%">
        <div class="row" style="margin-top: 80px;">
            <div class="col-xs-6">
                <span style="border-top: 1px solid black">Received With Good Condition By</span>
            </div>

            <div class="col-xs-6 text-right">
                <span style="border-top: 1px solid black">Authorised Signature</span>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 text-center"> <br>
                Software developed by 2A IT. Mobile: 01740059414
            </div>
        </div>
        <br>
    </div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
