@extends('layouts.app')

@section('style')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection

@section('title')
    Salary Sheet
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.salary.sheet') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Year</label>

                                    <select class="form-control" name="year" id="year" required>
                                        <option value="">Select Year</option>
                                       @foreach($salaryDates as $item)
                                            <option value="{{ $item->year}}">{{ $item->year}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Month</label>

                                    <select class="form-control" name="month" id="month" required>
                                        <option value="">Select Month</option>

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">
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
    @if($salaries)
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>
                    <div id="prinarea">
                        <div style="padding:10px; width:100%; text-align:center;">
                            <p style="font-family: 'Times New Roman'; font-size: 33px; font-style: italic; margin: 0px; font-weight: bold; text-align:center">
                                {{ config('app.name', 'Your Choice') }}<br>

                            <h5 style="text-align:center"><strong>IMPORTER &amp; WHOLESALER OF FOREIGN SHOES</strong></h5>

                            </p>
                            <p style="margin: 0px; font-size: 16px; text-align:center">
                                Shop# 20-21, Fubaria Super Market-1 (1st Floor)Dhaka-1000<br>
                                Phone : +8802223381027,, Mobile : 01591-148251(MANAGER)<br>
                                EMAIL:YOURCHOICE940@YAHOO.COM<br>
                                HELPLINE: IT DEPARTMENT,,,,MD.PORAN BHUYAIN<br>
                                MOBILE:01985-511918
                            </p>
                            <h4>For the month of
                            @if (count($salaries) > 0)
                                {{date('F, Y',strtotime($salaries[0]->date))}}
                            @endif

                            </h4>
                        </div>
                        @if($salaries)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center" colspan="4"></th>
                                    <th class="text-center" class="text-center" colspan="3">Present</th>
                                    <th class="text-center" colspan="5"></th>
                                    <th class="text-center" class="text-center" colspan="2">Deduction</th>
                                    <th class="text-center" colspan="2"></th>
                                </tr>
                                <tr>
                                    <th class="text-center">Sl</th>
                                    <th class="text-center">Employee Name</th>
                                    <th class="text-center">Designation</th>
                                    <th class="text-center">Date of Joining</th>
                                    <th class="text-center">Working Day</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center">Basic</th>
                                    <th class="text-center">House Rent</th>
                                    <th class="text-center">Conveyance</th>
                                    <th class="text-center">Medical Allowance</th>
                                    <th class="text-center">Gross Salary</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center">Advance</th>
                                    <th class="text-center">Net Salary</th>
                                    <th class="text-center">Signature</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($salaries as $salary)
                                    <tr>
                                        <td class="text-center">{{$loop->iteration}}</td>
                                        <td class="text-center">{{$salary->employee->name}}</td>
                                        <td class="text-center">{{$salary->employee->designation->name}}</td>
                                        <td class="text-center">
                                            @if (!empty($salary->employee->joining_date))
                                                {{date('d-m-Y',strtotime($salary->employee->joining_date))}}
                                            @endif

                                        </td>
                                        <td class="text-center">{{$working_days}}</td>
                                        <td class="text-center">{{$working_days-$salary->absent+$salary->late}}</td>
                                        <td class="text-center">{{$salary->absent+$salary->late}}</td>
                                        <td class="text-center">৳ {{number_format($salary->basic_salary,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->house_rent,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->travel,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->medical,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->gross_salary,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->absent_deduct,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->others_deduct,2)}}</td>
                                        <td class="text-center">৳ {{number_format($salary->gross_salary-$salary->others_deduct-$salary->absent_deduct,2)}}</td>
                                        <td class="text-center"></td>
                                    </tr>
                                @endforeach

                                </tbody>

                                <tfoot>

                                <tr>
                                    <th class="text-center" colspan="7">Total</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('basic_salary'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('house_rent'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('travel'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('medical'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('gross_salary'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('absent_deduct'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('others_deduct'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($salaries->sum('gross_salary')-$salaries->sum('absent_deduct')-$salaries->sum('others_deduct'),2)}}</th>
                                    <th class="text-center"></th>
                                </tr>

                                </tfoot>
                            </table>
                            <strong>Taka (in words): </strong>

                        </div>
                        @endif
                    </div>

                </div>
            </section>
        </div>
    </div>
    @endif
@endsection

@section('script')
    <!-- date-range-picker -->
    <script src="{{ asset('themes/backend/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(function () {


            $('#year').change(function () {
                var year = $(this).val();
                $('#month').html('<option value="">Select Month</option>');

                if (year != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_month_salary_sheet') }}",
                        data: { year: year }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('#month').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });

                }
            });

        });
    </script>
    <script>


        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {

            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
