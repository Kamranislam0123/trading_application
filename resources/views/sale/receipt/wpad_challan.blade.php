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
            <strong> Challan </strong>
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
            </tr>
        </thead>

        <tbody>
            @php
                $total_quantity = 0;
            @endphp
             @foreach($order->product_items as $product)
                <tr>
                    <td>{{ $product->product_item->name??'' }}</td>
                    <td>
                        @foreach ($product->item_products($product->sales_order_id, $product->product_item_id)??[] as $item)
                            {{ $item->product->name??'' }} ,
                        @endforeach
                    </td>
                    <td>{{ $product->product_item->description??'' }}</td>
                    <td>
                        {{ $product->item_products($product->sales_order_id, $product->product_item_id)->sum('quantity') }}
                        @php
                            $total_quantity += $product->item_products($product->sales_order_id, $product->product_item_id)->sum('quantity');
                        @endphp
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-right"> <b> Total </b> </td>
                <td> <b> {{ $total_quantity }} </b> </td>
            </tr>
        </tbody>
    </table>
@endif


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
