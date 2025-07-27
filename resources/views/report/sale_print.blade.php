
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
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
{{--                        <div style="padding:10px; width:100%; text-align:center;">--}}
{{--                            @if (Auth::user()->company_branch_id == 1)--}}

{{--                                <h2>{{ config('app.name', 'Your Choice') }}</h2>--}}
{{--                                <p style="margin: 0px; text-align:center">--}}
{{--                                    <strong>Shop# 20-21, Fubaria Super Market-1 (1st Floor)Dhaka-1000</strong><br>--}}
{{--                                    <strong>Phone : +8802223381027 Mobile : 01591-148251(MANAGER)</strong><br>--}}
{{--                                    <strong>EMAIL:yourchoice940@yahoo.com</strong><br>--}}
{{--                                    <strong style="color: red">HELPLINE: IT DEPARTMENT(MD.PORAN BHUYAIN)</strong><br>--}}
{{--                                    <strong>MOBILE:01985-511918</strong>--}}
{{--                                </p>--}}
{{--                            @elseif (Auth::user()->company_branch_id == 2)--}}
{{--                                <h2>Your Choice Plus</h2>--}}
{{--                                <strong>Shop# 23-24, Fubaria Super Market-1 (2nd Floor)Dhaka-1000,</strong><br>--}}
{{--                                <strong>Mobile : 01876-864470(Manager)</strong><br>--}}
{{--                                <strong>EMAIL:yourchoice940@yahoo.com</strong><br>--}}
{{--                                <strong style="color: red">HELPLINE: IT DEPARTMENT(MD.PORAN BHUYAIN)</strong><br>--}}
{{--                                <strong>MOBILE: 01985-511918</strong>--}}
{{--                            @endif--}}
{{--                        </div>--}}
                        <div class="row">
                            <div class="col-xs-12">
                                @if (Auth::user()->company_branch_id == 2)
                                    <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                                @else
                                    <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                                @endif
                            </div>
                            <h4 style="text-align: center">Sale Report</h4>
                        </div>
{{--                        <div class="logo-pad">--}}
{{--                            <img src="{{ asset('img/logo.png') }}" style="position: absolute;opacity: 0.1;height: 553px;width: 650px;margin-top: 130px;margin-left: 65px;">--}}
{{--                        </div>--}}
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order No.</th>
                                <th>Name</th>
                                <th>Product Details</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->date->format('j F, Y') }}</td>
                                    <td>{{ $order->order_no }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->product_name  }}</td>
                                    <td>{{ number_format($order->total, 2) }}</td>
                                    <td>{{ number_format($order->paid, 2) }}</td>
                                    <td>{{ number_format($order->due, 2) }}</td>
                               </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <td>{{ number_format($orders->sum('total'), 2) }}</td>
                                <td>{{ number_format($orders->sum('paid'), 2) }}</td>
                                <td>{{ number_format($orders->sum('due'), 2) }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>

