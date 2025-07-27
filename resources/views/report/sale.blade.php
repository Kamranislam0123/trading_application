@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Sale Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.sale') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off" >
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
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" >
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>

                                    <select class="form-control" name="customer">
                                        <option value="">All Customer</option>

                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Report type </label>

                                    <select class="form-control select2" name="report_type">
                                        <option value="">All Item</option>
                                        <option @if (request()->get('report_type')==1) selected @endif value="1"> Due</option>
                                        <option @if (request()->get('report_type')==2) selected @endif value="2"> Paid</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Order No.</label>
                                    <input type="text" class="form-control" name="order_no" value="{{ request()->get('order_no') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Product Item </label>


                                    <select name="product_item" class="form-control" id="product_item">
                                        <option value="">All Product Item</option>
                                        @foreach($product_items as $item)
                                            <option value="{{$item->id}}" {{ $item->id == request()->get('product_item') ? 'selected' : '' }}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            @if ($branches)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Company Branch </label>
                                        <select name="company_branch" class="form-control" id="company_branch">
                                            <option value="">All Company Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" {{ $branch->id == request()->get('company_branch') ? 'selected' : '' }}>{{$branch->name}}</option>
                                            @endforeach
                                        </select>
                                        <!-- /.input group -->
                                    </div>
                                </div>
                            @endif
{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label> Product</label>--}}

{{--                                    <select name="product" class="form-control" id="product">--}}
{{--                                        <option value="">All Product</option>--}}
{{--                                    </select>--}}
{{--                                    <!-- /.input group -->--}}
{{--                                </div>--}}
{{--                            </div>--}}


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

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
                <div class="panel-heading">
                    <form target="_blank" action="{{ route('report.sale_print') }}">
                        <input type="hidden" name="start" value="{{ request()->get('start') }}">
                        <input type="hidden" name="end" value="{{ request()->get('end') }}">
                        <input type="hidden" name="product" value="{{ request()->get('product') }}">
                        <input type="hidden" name="product_item" value="{{ request()->get('product_item') }}">
                        <input type="hidden" name="order_no" value="{{ request()->get('order_no') }}">
                        <input type="hidden" name="customer" value="{{ request()->get('customer') }}">
                        <input type="hidden" name="report_type" value="{{ request()->get('report_type') }}">
                        <input type="hidden" name="company_branch" value="{{ request()->get('company_branch') }}">
                        <button class="btn btn-primary"><i class="fa fa-print"></i></button>
                    </form>
                    <hr>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
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
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->date->format('j F, Y') }}</td>
                                    <td>{{ $order->order_no }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->product_name  }}</td>
                                    <td>{{ number_format($order->total * nbrCalculation(), 2) }}</td>
                                    <td>{{ number_format($order->paid * nbrCalculation(), 2) }}</td>
                                    <td>{{ number_format($order->due * nbrCalculation(), 2) }}</td>
                                    <td><a href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">View Invoice</a></td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <td>{{ number_format($orders->sum('total') * nbrCalculation(), 2) }}</td>
                                <td>{{ number_format($orders->sum('paid') * nbrCalculation(), 2) }}</td>
                                <td>{{ number_format($orders->sum('due') * nbrCalculation(), 2) }}</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                        {{ $orders->appends($appends)->links() }}
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')

    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            var selectedProduct = '{{ request()->get('product') }}';
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('#type').change(function () {
                if ($(this).val() == '') {
                    $('#customer').hide();
                    $('#supplier').hide();
                }else if ($(this).val() == 1) {
                    $('#customer').show();
                    $('#supplier').hide();
                } else {
                    $('#customer').hide();
                    $('#supplier').show();
                }
            });

            $('#type').trigger('change');

            $('#product_item').change(function() {
                var productItemId = $(this).val();
                $('#product').html('<option value="">All Product</option>');

                if (productItemId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_products') }}",
                        data: {productItemId: productItemId}
                    }).done(function (response) {
                        $.each(response, function( index, item ) {
                            if (selectedProduct == item.id)
                                $('#product').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#product').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#product_item').trigger('change');
        });
    </script>
@endsection
