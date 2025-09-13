@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
   Branch wise Sale Return
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.branch_wise_sale_return') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select class="form-control select2" name="branch" required>
                                        <option value="">Select Company Branch</option>
                                        @foreach($companyBranches as $companyBranch)
                                            <option value="{{ $companyBranch->id }}" {{ request()->get('branch') == $companyBranch->id ? 'selected' : '' }}>{{ $companyBranch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" required
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off">
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
                                        <input type="text" class="form-control pull-right" required
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

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
                    <a onclick="getprint('prinarea')" role="button" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</a>
                </div>
                <div class="panel-body" id="prinarea">
                    <div class="row">
                        <div class="col-xs-12">
                            @if (Auth::user()->company_branch_id == 2)
                                <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                            @else
                                <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th class="text-center">Date</th>
                                <th class="text-center">Order No</th>
                                <th class="text-center">Customer Name</th>
                                <th class="text-center">Customer Address</th>
                                <th class="text-center">Customer Phone</th>
                                <th class="text-center">Quantity</th>
                            </tr>
                            <?php
                            $totalQuantity = 0;
                            ?>
                            @if(count($productReturnOrders) > 0)

                                @foreach($productReturnOrders as $key => $productReturnOrder)
                                    <?php
                                    $totalQuantity += $productReturnOrder->quantity;
                                    ?>
                                    <tr>
                                        <td class="text-center">{{ $productReturnOrder->date }}</td>
                                        <td class="text-center">{{ $productReturnOrder->order_no }}</td>
                                        <td class="text-center">{{ $productReturnOrder->customer->name??'' }}</td>
                                        <td class="text-center">{{ $productReturnOrder->customer->address??'' }}</td>
                                        <td class="text-center">{{ $productReturnOrder->customer->mobile_no??''  }}</td>
                                        <td class="text-center">{{ $productReturnOrder->quantity??''  }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <th colspan="5" class="text-right">Total</th>
                                <th class="text-center">{{ number_format($totalQuantity,2) }}</th>
                            </tr>
                        </table>

                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                todayHighlight: true
            });

        });
        $('.select2').select2();

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
