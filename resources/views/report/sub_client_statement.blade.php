@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('title')
    Sub Client Report
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
                    <form action="{{ route('report.sub_client_statement') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Report type </label>

                                    <select class="form-control select2" name="report_type" required>
                                        <option @if (request()->get('report_type')==1) selected @endif value="1"> Due Sub Customer Report</option>
                                        <option @if (request()->get('report_type')==2) selected @endif value="2"> All Sub Customer Report</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Customer </label>

                                    <select class="form-control select2" name="customer_id">
                                        <option value=""> Select Customer </option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @if (old('customer_id', request()->get('customer_id'))==$customer->id) selected @endif>
                                                {{ $customer->name }} - {{ $customer->mobile_no }}
                                            </option>
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
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>

                    <div id="prinarea">
                        <div style="padding:10px; width:100%; text-align:center;">
                            <h2>{{ config('app.name', 'Bio-Access Tech Co.') }}</h2>
                            <h4>#House: 9, Road:2/2-1b,Banani, Dhaka-1213, Bangladesh.tel : 02-55040826</h4>
                            <h4>Sub Client Report</h4>
                        </div>
                        <div class="table-responsive">
                         <table id="table" class="table table-bordered table-striped">
                             <thead>
                                <tr>
                                    <th class="text-center"> Name </th>
                                    <th class="text-center" width="40%">Parent Client</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Paid</th>
                                    <th class="text-center">Due</th>
                                </tr>
                             </thead>
                             <tbody>
                                @php
                                    $total = 0;
                                    $paid = 0;
                                    $due = 0;
                                @endphp
                                @foreach($subCustomers as $customer)
                                    @if ($report_type==1 && $customer->due > 0)
                                        @php
                                            $total += $customer->total;
                                            $paid += $customer->paid;
                                            $due += $customer->due;
                                        @endphp
                                        <tr>
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->customer->name??''}}</td>
                                            <td class="text-center">৳ {{number_format($customer->total,2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->paid,2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->due,2)}}</td>
                                        </tr>
                                    @elseif($report_type==2)
                                        @php
                                            $total += $customer->total;
                                            $paid += $customer->paid;
                                            $due += $customer->due;
                                        @endphp
                                        <tr>
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->customer->name??''}}</td>
                                            <td class="text-center">৳ {{number_format($customer->total,2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->paid,2)}}</td>
                                            <td class="text-center">৳ {{number_format($customer->due,2)}}</td>
                                        </tr>
                                    @endif

                                @endforeach
                                <tr>
                                    <th class="text-right" colspan="2">Total</th>
                                    <th class="text-center">৳ {{number_format($total,2)}}</th>
                                    <th class="text-center">৳ {{number_format($paid,2)}}</th>
                                    <th class="text-center">৳ {{number_format($due,2)}}</th>
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
    <script>

        $('.select2').select2();

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {

            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
