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
        @if (Auth::user()->company_branch_id == 2)
            <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
        @else
            <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
        @endif
        <br>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <table class="table table-bordered">
                <tr>
                    <th>Order Date</th>
                    <td>{{ $purchase_inventory_log->date->format('j F, Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="col-xs-6">
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
        <div class="col-xs-12">
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
                <tr>
                    <th class="text-right" colspan="6">Total Amount</th>
                    <td colspan="6">৳{{ number_format($purchase_inventory_log->total, 2) }}</td>
                </tr>
                </tbody>
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
