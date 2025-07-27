@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

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
                    <form action="{{ route('report.branch_wise_client') }}" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label> Branch </label>

                                    <select class="form-control select2" name="branch" id="branch">
                                        <option value="">Select Branch</option>
                                        @foreach($companyBranches as $companyBranch)
                                            <option value="{{ $companyBranch->id }}" {{ request()->get('branch') == $companyBranch->id ? 'selected' : '' }}>{{ $companyBranch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

{{--                            <div class="col-md-5">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label> Client </label>--}}

{{--                                    <select class="form-control select2" name="customer" id="customer">--}}
{{--                                        <option value="">Select Customer</option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

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
                        <div class="logo-pad">
                            <img src="{{ asset('img/logo.png') }}" style="position: absolute;opacity: 0.1;height: 553px;width: 650px;margin-top: 130px;margin-left: 65px;">
                        </div>

                        @if (count($customers) > 0)
                            <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center"> Name </th>
                                    <th class="text-center"> Opening Due</th>
                                    <th class="text-center"> Invoice Total </th>
                                    <th class="text-center"> Return </th>
                                    <th class="text-center"> Paid </th>
                                    <th class="text-center"> Due </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>{{$customer->name}}</td>
                                        <td class="text-center">৳ {{number_format($customer->opening_due * nbrCalculation(),2)}}</td>
                                        <td class="text-center">৳ {{number_format($customer->total * nbrCalculation(),2)}}</td>
                                        <td class="text-center">৳ {{number_format($customer->return_amount * nbrCalculation(),2)}}</td>
                                        <td class="text-center">৳ {{number_format($customer->paid * nbrCalculation(),2)}}</td>
                                        <td class="text-center">৳ {{number_format($customer->due * nbrCalculation(),2)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>
                        @endif
{{--                        @if ($customer)--}}
{{--                            <table id="table" class="table table-bordered table-striped">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th class="text-center"> Name </th>--}}
{{--                                    <th class="text-center"> Opening Due</th>--}}
{{--                                    <th class="text-center"> Invoice Total </th>--}}
{{--                                    <th class="text-center"> Return </th>--}}
{{--                                    <th class="text-center"> Paid </th>--}}
{{--                                    <th class="text-center"> Due </th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                <tr>--}}
{{--                                    <td>{{$customer->name}}</td>--}}
{{--                                    <td class="text-center">৳ {{number_format($customer->opening_due,2)}}</td>--}}
{{--                                    <td class="text-center">৳ {{number_format($customer->total,2)}}</td>--}}
{{--                                    <td class="text-center">৳ {{number_format($customer->return_amount,2)}}</td>--}}
{{--                                    <td class="text-center">৳ {{number_format($customer->paid,2)}}</td>--}}
{{--                                    <td class="text-center">৳ {{number_format($customer->due,2)}}</td>--}}
{{--                                </tr>--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        @endif--}}
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
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>

        $(function (){
            $('#table').DataTable();
            var customerSelected = '{{ request()->get('customer') }}';

            $('#branch').change(function () {
                var branchId = $(this).val();

                $('#customer').html('<option value="">Select Customer</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_customer') }}",
                        data: { branchId: branchId }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (customerSelected == item.id)
                                $('#customer').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#customer').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#branch').trigger('change');

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
