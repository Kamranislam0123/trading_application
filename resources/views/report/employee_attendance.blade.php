@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Employee Attendance report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.employee_attendance') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off" required>
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
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
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
    @isset($attendances)
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>
                    <div id="prinarea">
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
                            <h4>Attendance Sheet <br>{{ date('F d, Y',strtotime(request()->get('start')))  }} to {{ date('F d, Y',strtotime(request()->get('end')))  }}</h4>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">Sl</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Designation</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">present</th>
                                <th class="text-center">present Time</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Late Time</th>
                                <th class="text-center">Note</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($attendances as $attendance)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}</td>
                                    <td class="text-center">{{date('d-m-Y',strtotime($attendance->date))}}</td>
                                    <td >{{$attendance->employee->name}}</td>
                                    <td class="text-center">{{$attendance->employee->designation->name}}</td>
                                    <td class="text-center">{{$attendance->employee->department->name}}</td>
                                    <td class="text-center">
                                        @if ($attendance->present_or_absent==1)
                                            <span class="text-success"><i class="fa fa-check" aria-hidden="true"></i></span>
                                        @else
                                            <span class="text-red"><i class="fa fa-times" aria-hidden="true"></i></span>
                                        @endif

                                    </td>
                                    <td><span class="text-success">{{$attendance->present_time ? date('h:i A', strtotime($attendance->present_time)) : ''}}</span></td>

                                    <td class="text-center">
                                        @if ($attendance->late==1)
                                            <span class="text-red">Late</span>
                                        @endif
                                    </td>
                                    <td><span class="text-red">{{$attendance->late_time ? date('h:i A', strtotime($attendance->late_time)) : ''}}</span></td>
                                    <td class="text-center">{{$attendance->note}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endisset
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
        function getprint(prinarea) {

            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
