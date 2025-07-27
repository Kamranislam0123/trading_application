<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th>Order No.</th>
                    <td>{{ $order->order_no }}</td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td>{{ $order->date->format('j F, Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center">Supplier Info</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $order->supplier->name }}</td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td>{{ $order->supplier->mobile }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $order->supplier->address }}</td>
                </tr>
            </table>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th> Code </th>
                            <th> Model </th>
                            <th> Category </th>
                            <th> Warehouse </th>
                            <th> Quantity </th>
                            <th> Unit Price </th>
                            <th> Total </th>
                        </tr>
                    </thead>

                    @php
                        $totalQuantity = 0;
                    @endphp

                    <tbody>
                        @foreach($order->products as $product)
                            @php
                                $totalQuantity += $product->quantity;
                            @endphp
                            <tr>
                                <td>{{ $product->serial }}</td>
                                <td>{{ $product->productItem->name??'' }}</td>
                                <td>{{ $product->productCategory->name??'' }}</td>
                                <td>{{ $product->warehouse->name??'' }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>৳{{ number_format($product->unit_price, 2) }}</td>
                                <td>৳{{ number_format($product->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tr>
                        <th colspan="3"></th>
                        <th>Total</th>
                        <td>{{$totalQuantity}}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-offset-6 col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th>Total Amount</th>
                    <td class="text-right">৳{{ number_format($order->total, 2) }}</td>
                </tr>
                <tr>
                    <th> Transport Cost </th>
                    <td class="text-right">৳{{ number_format($order->transport_cost, 2) }}</td>
                </tr>
                <tr>
                    <th> Discount </th>
                    <td class="text-right">৳{{ number_format($order->discount, 2) }}</td>
                </tr>
                <tr>
                    <th> Total </th>
                    <td class="text-right">৳{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
