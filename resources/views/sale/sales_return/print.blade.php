
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
        }
    </style>
</head>
<body>
<header id="pageHeader" style="margin-bottom: 10px">
    <div class="row">
        <div class="col-xs-12">
            @if ($purchase_inventory_log->company_branch_id == 2)
                <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
            @else
                <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
            @endif
        </div>
    </div>
</header>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">

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
                    <div class="col-md-12">
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
                                    ৳ {{ number_format($purchase_inventory_log->selling_price, 2) }}
                                </td>
                                <td class="text-right" width="100">
                                    ৳ {{ number_format($purchase_inventory_log->sale_total, 2) }}
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
        <div class="col-xs-12 text-center"><br>
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

