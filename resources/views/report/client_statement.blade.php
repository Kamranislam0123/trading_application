@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

    <style>
        .logo-pad img{

        }
    </style>
@endsection

@section('title')
    Client Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Filter</h3>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <form action="{{ route('report.client_statement') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> Report type </label>

                                    <select class="form-control select2" name="report_type" required>
                                        <option @if (request()->get('report_type')==1) selected @endif value="1"> Due Customer Report</option>
                                        <option @if (request()->get('report_type')==2) selected @endif value="2"> All Customer Report</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label> Client </label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} - {{ $customer->address }} - {{ $customer->mobile_no }} - {{ $customer->branch->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                    <div id="prinarea">

                        <div class="row">
                            <div class="col-xs-12">
                                @if (Auth::user()->company_branch_id == 2)
                                    <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                                @else
                                    <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                                @endif
                            </div>
                        </div>
{{--                        <div class="logo-pad">--}}
{{--                            <img src="{{ asset('img/logo.png') }}" style="position: absolute;opacity: 0.1;height: 553px;width: 650px;margin-top: 130px;margin-left: 65px;">--}}
{{--                        </div>--}}
                        <div class="table-responsive">
                         <table id="table" class="table table-bordered table-striped">
                             <thead>
                                <tr>
                                    <th class="text-center"> Name </th>
                                    <th class="text-center"> Opening Due</th>
                                    <th class="text-center"> Total Quantity </th>
                                    <th class="text-center"> Invoice Total </th>
                                    <th class="text-center"> Return </th>
                                    <th class="text-center"> Paid </th>
                                    <th class="text-center"> Due </th>
                                </tr>
                             </thead>
                             <tbody>
                                @php
                                    $total = 0;
                                    $opening_due = 0;
                                    $totalQuantity = 0;
                                    $paid = 0;
                                    $return_amount = 0;
                                    $due = 0;
                                @endphp
                                @foreach($customers as $key => $customer)
                                    @if ($report_type==1 && $customer->due > 0)
                                        @php
                                            $total += $customer->total;
                                            $totalQuantity += $customer->quantity;
                                            $opening_due += $customer->opening_due;
                                            $paid += $customer->paid;
                                            $return_amount += $customer->return_amount;
                                            $due += $customer->due;
                                        @endphp
                                        <tr>
                                            <td>{{$customer->name}}</td>
                                            <td class="text-center">৳ {{number_format($customer->opening_due * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->quantity,2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->total * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->return_amount * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->paid * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->due * nbrCalculation(),2)}}</td>
                                        </tr>
                                    @elseif($report_type==2)
                                        @php
                                            $total += $customer->total;
                                            $totalQuantity += $customer->quantity;
                                            $opening_due += $customer->opening_due;
                                            $paid += $customer->paid;
                                            $return_amount += $customer->return_amount;
                                            $due += $customer->due;
                                        @endphp
                                        <tr>
                                            <td>{{$customer->name}}</td>
                                            <td class="text-center">৳ {{number_format($customer->opening_due * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->quantity * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->total * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->return_amount * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->paid * nbrCalculation(),2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->due * nbrCalculation(),2)}}</td>
                                        </tr>
                                    @endif

                                @endforeach
                                <tr>
                                    <th class="text-center" colspan="1">Total</th>
                                    <th class="text-center">৳ {{number_format($opening_due * nbrCalculation(),2)}}</th>
                                    <th class="text-center">৳ {{number_format($totalQuantity * nbrCalculation(),2)}}</th>
                                    <th class="text-center">৳ {{number_format($total * nbrCalculation(),2)}}</th>
                                    <th class="text-center">৳ {{number_format($return_amount * nbrCalculation(),2)}}</th>
                                    <th class="text-center">৳ {{number_format($paid * nbrCalculation(),2)}}</th>
                                    <th class="text-center">৳ {{number_format($due * nbrCalculation(),2)}}</th>
                                </tr>
                             </tbody>
                         </table>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>

        $(function (){
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

            $('.select2').select2();

        });
        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
