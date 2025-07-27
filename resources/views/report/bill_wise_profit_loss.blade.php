@extends('layouts.app')

@section('style')

    <!-- bootstrap datepicker -->
    <link rel="stylesheet"
          href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Bill Wise Profit Loss Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.bill_wise_profit_loss') }}">
                        <div class="row">

                            {{--                            <div class="col-md-3">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label>Sale Order</label>--}}

                            {{--                                    <select class="form-control" name="orderId">--}}
                            {{--                                        @foreach($saleOrders as $saleOrder)--}}
                            {{--                                        <option value="{{ $saleOrder->id }}" {{ request()->get('orderId') == $saleOrder->id ? 'selected' : '' }}>{{ $saleOrder->order_no }}</option>--}}
                            {{--                                        @endforeach--}}
                            {{--                                    </select>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}"
                                               autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end')  }}"
                                               autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> &nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <section class="panel">

                <div class="panel-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button>
                    <br><br>
                    <div id="prinarea">
                        <div class="row">
                            <div class="col-xs-12">

                                @if (Auth::user()->company_branch_id == 2)
                                    <img src="{{ asset('img/your_choice_bfbf.png') }}"
                                         style="margin-top: 10px; float:inherit">
                                @else
                                    <img src="{{ asset('img/your_choice_bfbf.png') }}"
                                         style="margin-top: 10px; float:inherit">
                                @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No.</th>
                                    <th>Customer</th>
                                    <th>Product Details</th>
                                    <th>Quantity</th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price</th>
                                    <th>Profit</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                @php
                                    $total_quantity=0;
                                    $totalPurchasePrice=0;
                                    $totalSellingPrice=0;
                                    $totalProfit=0;
                                @endphp
                                <tbody>

                                @foreach($orders as $key => $order)
                                    @php
                                        $total_quantity += $order->quantity();
                                        $totalPurchasePrice += $purchase_prices[$key] * $order->quantity();
                                        $totalSellingPrice += $selling_prices[$key] * $order->quantity();
                                        $totalProfit += ($selling_prices[$key] * $order->quantity()) - ($purchase_prices[$key] * $order->quantity());
                                    @endphp
                                    <tr>
                                        <td>{{ $order->date->format('j F, Y') }}</td>
                                        <td>{{ $order->order_no }}</td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>{{ $order->product_name }}</td>
                                        <td>{{ $order->quantity() }}</td>
                                        <td>{{number_format($purchase_prices[$key] * $order->quantity(), 2) }}</td>
                                        <td>{{number_format(($selling_prices[$key] * $order->quantity()),2) }}</td>
                                        <td>{{number_format(($selling_prices[$key] * $order->quantity()) - ($purchase_prices[$key] * $order->quantity()), 2) }}</td>
                                        <td><a href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">View Invoice</a></td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>

                                    <th colspan="4" class="text-right">Total</th>
                                    <td>{{ number_format($total_quantity, 2) }}</td>
                                    <td>{{ number_format($totalPurchasePrice, 2) }}</td>
                                    <td>{{ number_format($totalSellingPrice, 2) }}</td>
                                    <td>{{ number_format($totalProfit, 2) }}</td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                            {{ $orders->appends($appends)->links() }}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <!-- date-range-picker -->

    <script
        src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        var APP_URL = '{!! url()->full()  !!}';

        function getprint(prinarea) {

            $('body').html($('#' + prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
        $(function () {
            var selectedProduct = '{{ request()->get('product') }}';

            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            $('#product_item').change(function () {
                var productItemId = $(this).val();
                $('#product').html('<option value="">All Product</option>');

                if (productItemId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_products') }}",
                        data: {productItemId: productItemId}
                    }).done(function (response) {
                        $.each(response, function (index, item) {
                            if (selectedProduct == item.id)
                                $('#product').append('<option value="' + item.id + '" selected>' + item.name + '</option>');
                            else
                                $('#product').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    });
                }
            });

            $('#product_item').trigger('change');
        });

    </script>
@endsection
