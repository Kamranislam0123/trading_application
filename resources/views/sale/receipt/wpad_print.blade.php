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

        @media screen {
            div.divFooter {
                display: none;
            }
        }
        @media print {
            div.divFooter {
                position: fixed;
                bottom: 0;
            }
            @page {
                size: A4; 
                margin-top: 1.5in;
                margin-bottom: 3.8cm;
            }
        }
    </style>
</head>
<body>
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
                    @if ($order->buyer_type==1)
                        @if ($order->sub_customer_id)
                            <strong>Name: </strong>{{ $order->subCustomer->name??'' }} <br>
                            <strong>Address: </strong>{{ $order->subCustomer->address??'' }} <br>
                            <strong>Mobile No. : </strong>{{ $order->subCustomer->mobile_no??'' }} <br>
                            <strong> P.O No : </strong>{{ $order->received_by }}
                        @else 
                            <strong>Name: </strong>{{ $order->customer->name??'' }} <br>
                            <strong>Address: </strong>{{$order->customer->address??'' }} <br>
                            <strong>Mobile No. : </strong>{{$order->customer->mobile_no??'' }} <br>
                            <strong> P.O No : </strong>{{ $order->received_by }}
                        @endif
                    @endif
                    
                    
                </div>

                <div class="col-xs-6 text-right">
                    <strong>ID : </strong>{{ $order->buyer_type == 1 ? $order->customer->id : $order->supplier->id }} <br>
                    <strong>Invoice No : </strong>{{ $order->order_no }} <br>
                    <strong>Date : </strong>{{ $order->date->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($order->product_items) > 0)
    <br>
    <table class="table table-bordered product-table pt-4" style="margin-top: 5px; margin-bottom: 1px !important; font-size: 12px">
        <thead>
            <tr>
                <th>Product Model</th>
                <th>Product Serial</th>
                <th> Description </th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
             @foreach($order->product_items as $product)
                <tr>
                    <td>{{ $product->product_item->name??'' }}</td>
                    <td>
                        @foreach ($product->item_products($order->id, $product->product_item_id)??[] as $item)
                            {{ $item->product->name??'' }} ,
                        @endforeach
                    </td>
                    <td>{{ $product->product_item->description??'' }}</td>
                    <td>
                        {{ $product->item_products($order->id, $product->product_item_id)->sum('quantity') }}
                    </td>
                    <td>
                        @php
                            $unit_price = $product->item_products($order->id, $product->product_item_id)->first();
                        @endphp
                        @if ($unit_price)
                            {{ $unit_price->unit_price }}
                        @endif
                    </td>
                    <td width="100">
                        ৳ {{ $product->item_products($order->id, $product->product_item_id)->sum('total') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(count($order->services) > 0)
    <br>
    <table class="table table-bordered product-table" style="margin-top: 5px; margin-bottom: 1px !important; font-size: 12px">
        <thead>
        <tr>
            <th style="background-color: lightgrey !important;">#</th>
            <th style="background-color: lightgrey !important;">Name</th>
            <th style="background-color: lightgrey !important;">Qty</th>
            <th style="background-color: lightgrey !important;">Unit Price</th>
            <th style="background-color: lightgrey !important;">Amount</th>
        </tr>
        </thead>

        <tbody>
        @foreach($order->services as $services)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $services->name }}</td>
                <td>{{ $services->quantity }}</td>
                <td>৳{{ number_format($services->unit_price, 2) }}</td>
                <td>৳{{ number_format($services->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<div class="table-summary" style="width: 60%; float: right; font-size: 12px">
    <br>
    <table class="table table-bordered" style="margin-top: 2px !important;">
        <tr>
            <th>Service Sub Total</th>
            <td>{{ number_format($order->service_sub_total, 2) }}</td>
            <th>Product Sub Total</th>
            <td>{{ number_format($order->sub_total, 2) }}</td>
        </tr>
        {{-- <tr>
            <th>Service Vat ({{ $order->service_vat_percentage }}%)</th>
            <td>{{ number_format($order->service_vat, 2) }}</td>
            <th>Product Vat ({{ $order->vat_percentage }}%)</th>
            <td>{{ number_format($order->vat, 2) }}</td>
        </tr> --}}
        <tr>
            <th>Service Discount</th>
            <td>{{ number_format($order->service_discount, 2) }}</td>
            <th>Product Discount</th>
            <td>{{ number_format($order->discount, 2) }}</td>
        </tr>
        <tr>
            <th colspan="3">Total</th>
            <td>{{ number_format($order->total, 2) }}</td>
        </tr>
    </table>
</div>

<div class="text-left" style="clear: both">
    @if($order->service_vat > 0  || $order->vat > 0)
        VAT Money was given to the customer. <br>
    @endif
    <strong>In Word: {{ $order->amount_in_word }} Only</strong>
</div>


<div class="divFooter" style="width: 100%">
    <div class="row">
        <div class="col-xs-6">
            <span style="border-top: 1px solid black">Received With Good Condition By</span>
        </div>

        <div class="col-xs-6 text-right">
            <span style="border-top: 1px solid black">Authorised Signature</span>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 text-center"> <br> 
            Software developed by 2A IT. Mobile: 01884697775
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
