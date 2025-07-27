@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Monthly CRM Report
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
                    <form action="{{ route('report.monthly_crm') }}">
                        <div class="row">
                            <div class="form-group col-xs-4">
                                <label> Month </label>
                                <select class="form-control" id="modal-month" name="month" required>
                                    <option value=""> Select Month </option>
                                    <option @if ($month==1) selected @endif value="01"> January </option>
                                    <option @if ($month == 2) selected @endif value="02"> February </option>
                                    <option @if ($month == 3) selected @endif value="03"> March </option>
                                    <option @if ($month == 4) selected @endif value="04"> April </option>
                                    <option @if ($month == 5) selected @endif value="05"> May </option>
                                    <option @if ($month == 6) selected @endif value="06"> June </option>
                                    <option @if ($month == 7) selected @endif value="07"> July </option>
                                    <option @if ($month == 8) selected @endif value="08"> August </option>
                                    <option @if ($month == 9) selected @endif value="09"> September </option>
                                    <option @if ($month == 10) selected @endif value="10"> October </option>
                                    <option @if ($month == 11) selected @endif value="11"> Nobember </option>
                                    <option @if ($month == 12) selected @endif value="12"> December </option>
                                </select>
                            </div>

                            <div class="form-group col-xs-4">
                                <label> Year </label>
                                <select class="form-control" id="modal-year" name="year" required>
                                    <option value="">Select Year</option>
                                    @for ($i = 2020; $i < 2035; $i++)
                                        <option @if ($year == $i) selected @endif value="{{ $i }}"> {{ $i }} </option>
                                    @endfor
                                </select>
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
        <div class="col-sm-12" style="min-height:300px">
            @if($clients)
                <section class="panel">

                    <div class="panel-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                        <div class="adv-table" id="prinarea">
                            <div style="padding:10px; width:100%; text-align:center;">
                                @if (Auth::user()->company_branch_id == 1)

                                    <h2>{{ config('app.name', 'Your Choice') }}</h2>
                                    <p style="margin: 0px; font-size: 16px; text-align:center">
                                        Shop# 20-21, Fubaria Super Market-1 (1st Floor)Dhaka-1000<br>
                                        Phone : +8802223381027,, Mobile : 01591-148251(MANAGER)<br>
                                        EMAIL:YOURCHOICE940@YAHOO.COM<br>
                                        HELPLINE: IT DEPARTMENT,,,,MD.PORAN BHUYAIN<br>
                                        MOBILE:01985-511918
                                    </p>
                                @elseif (Auth::user()->company_branch_id == 2)
                                    <h2>Your Choice Plus</h2>
                                    <p style="margin: 0px; text-align:center">
                                        Shop# 23-24, Fubaria Super Market-1 (2nd Floor)Dhaka-1000,<br>
                                        Mobile : 01876-864470(Manager)<br>
                                        EMAIL:YOURCHOICE940@YAHOO.COM<br>
                                        HELPLINE: IT DEPARTMENT,,,,MD.PORAN BHUYAIN<br>
                                        MOBILE: 01985-511918
                                    </p>
                                @endif
                            </div>
                            <table class="table table-bordered" style="margin-bottom: 0px">
                                <tr>
                                    <th class="text-center">{{ date("F, Y", strtotime('01-'.$month.'-'.$year)) }}</th>
                                </tr>
                            </table>

                            <div style="clear: both">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2"> Employee </th>
                                            <th> Mobile </th>
                                            <th> Work Order </th>
                                            <th> Order Amount </th>
                                            <th> Target Amount </th>
                                            <th> Balance </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($clients as $client)
                                            @php
                                                $order_amount = $client->employee_monthly_client_orders($client->marketing_id,$month,$year)['data']->sum('propose_amount');
                                                $target_amount = $client->employee_monthly_client_orders($client->marketing_id,$month,$year)['target'];
                                            @endphp
                                            <tr>
                                                <td>{{ $client->marketing->name??'' }}</td>
                                                <td>{{ $client->marketing->mobile??'' }}</td>
                                                <td>{{ $client->employee_monthly_client_orders($client->marketing_id,$month,$year)['data']->count() }}</td>
                                                <td>{{ number_format($order_amount,2) }}</td>
                                                <td>{{ number_format($target_amount,2) }}</td>
                                                <td>
                                                    {{ number_format($order_amount-$target_amount,2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
