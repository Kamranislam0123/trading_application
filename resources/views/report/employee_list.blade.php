@extends('layouts.app')



@section('title')
    Employee List
@endsection

@section('content')
    {{-- <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.employee_list') }}">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Category</label>

                                    <select class="form-control" name="category">
                                        <option value="">All</option>
                                        <option value="1" {{ request()->get('category') == '1' ? 'selected' : '' }}>Dhaka Office</option>
                                        <option value="2" {{ request()->get('category') == '2' ? 'selected' : '' }}>Factory</option>
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
    </div> --}}
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>

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
                        </div>
                         <table id="table" class="table table-bordered table-striped">
                             <thead>
                             <tr >
                                 <th class="text-center">ID</th>
                                 <th class="text-center">Name</th>
                                 <th class="text-center">Designation</th>
                                 <th class="text-center">Department</th>
                                 <th class="text-center">Mobile</th>
                             </tr>
                             </thead>
                             <tbody>
                             @foreach($employees as $employee)
                                 <tr>
                                     <td class="text-center">{{$employee->employee_id}}</td>
                                     <td>{{$employee->name}}</td>
                                     <td class="text-center">{{$employee->designation->name}}</td>
                                     <td class="text-center">{{$employee->department->name}}</td>
                                     <td class="text-center">{{$employee->mobile_no}}</td>
                                 </tr>
                             @endforeach
                             </tbody>
                         </table>

                     </div>

                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>


        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {

            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
