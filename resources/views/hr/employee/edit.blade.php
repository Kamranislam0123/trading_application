@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Employee Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Employee Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('employee.edit', ['employee' => $employee->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $employee->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('employee_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee ID *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Employee ID"
                                       name="employee_id" value="{{ $employee->employee_id }}" readonly>

                                @error('employee_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Email *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Email"
                                       name="email" value="{{ old('email', $employee->email) }}" required>

                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- <div class="form-group {{ $errors->has('password') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Password </label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control"
                                       name="password" value="{{ old('password') }}">

                                @error('password')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> -->

                        <div class="form-group {{ $errors->has('date_of_birth') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Date of Birth </label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="dob" name="date_of_birth"
                                           value="{{ empty(old('date_of_birth')) ? ($errors->has('date_of_birth') ? '' : $employee->dob) : old('date_of_birth') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('date_of_birth')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('joining_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Joining Date </label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" name="joining_date"
                                           value="{{ empty(old('joining_date')) ? ($errors->has('joining_date') ? '' : $employee->joining_date) : old('joining_date') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('joining_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('confirmation_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Confirmation Date </label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" name="confirmation_date"
                                           value="{{ empty(old('confirmation_date')) ? ($errors->has('confirmation_date') ? '' : $employee->confirmation_date) : old('confirmation_date') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('confirmation_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('education_qualification') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Education Qualification </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Education Qualification"
                                       name="education_qualification" value="{{ empty(old('education_qualification')) ? ($errors->has('education_qualification') ? '' : $employee->education_qualification) : old('education_qualification') }}">

                                @error('education_qualification')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        {{--<div class="form-group {{ $errors->has('department') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Department *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="department" id="department">
                                    <option value="">Select Department</option>

                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ empty(old('department')) ? ($errors->has('department') ? '' : ($employee->department_id == $department->id ? 'selected' : '')) :
                                            (old('department') == $department->id ? 'selected' : '') }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>

                                @error('department')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('designation') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Designation *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="designation" id="designation">
                                    <option value="">Select Designation</option>
                                </select>

                                @error('designation')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>--}}

                        <div class="form-group {{ $errors->has('employee_type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="employee_type" >
                                    <option value="">Select Employee Type</option>
                                    <option value="1" {{ empty(old('employee_type')) ? ($errors->has('employee_type') ? '' : ($employee->employee_type == '1' ? 'selected' : '')) :
                                            (old('employee_type') == '1' ? 'selected' : '') }}>Permanent</option>
                                    <option value="2" {{ empty(old('employee_type')) ? ($errors->has('employee_type') ? '' : ($employee->employee_type == '2' ? 'selected' : '')) :
                                            (old('employee_type') == '2' ? 'selected' : '') }}>Temporary</option>
                                </select>

                                @error('employee_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('reporting_to') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Reporting To </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Reporting To"
                                       name="reporting_to" value="{{ empty(old('reporting_to')) ? ($errors->has('reporting_to') ? '' : $employee->reporting_to) : old('reporting_to') }}">

                                @error('reporting_to')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('gender') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Gender *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="gender" >
                                    <option value="">Select Gender</option>
                                    <option value="1" {{ empty(old('gender')) ? ($errors->has('gender') ? '' : ($employee->gender == '1' ? 'selected' : '')) :
                                            (old('gender') == '1' ? 'selected' : '') }}>Male</option>
                                    <option value="2" {{ empty(old('gender')) ? ($errors->has('gender') ? '' : ($employee->gender == '2' ? 'selected' : '')) :
                                            (old('gender') == '2' ? 'selected' : '') }}>Female</option>
                                </select>

                                @error('gender')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('marital_status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Marital Status *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="marital_status" >
                                    <option value="">Select Marital Status</option>
                                    <option value="1" {{ empty(old('marital_status')) ? ($errors->has('marital_status') ? '' : ($employee->marital_status == '1' ? 'selected' : '')) :
                                            (old('marital_status') == '1' ? 'selected' : '') }}>Single</option>
                                    <option value="2" {{ empty(old('marital_status')) ? ($errors->has('marital_status') ? '' : ($employee->marital_status == '2' ? 'selected' : '')) :
                                            (old('marital_status') == '2' ? 'selected' : '') }}>Married</option>
                                </select>

                                @error('marital_status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mobile No. </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile No."
                                       name="mobile_no" value="{{ empty(old('mobile_no')) ? ($errors->has('mobile_no') ? '' : $employee->mobile_no) : old('mobile_no') }}">

                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('father_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Father Name </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Father Name"
                                       name="father_name" value="{{ empty(old('father_name')) ? ($errors->has('father_name') ? '' : $employee->father_name) : old('father_name') }}">

                                @error('father_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mother_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mother Name </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mother Name"
                                       name="mother_name" value="{{ empty(old('mother_name')) ? ($errors->has('mother_name') ? '' : $employee->mother_name) : old('mother_name') }}">

                                @error('mother_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('emergency_contact') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Emergency Contact *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Emergency Contact"
                                       name="emergency_contact" value="{{ empty(old('emergency_contact')) ? ($errors->has('emergency_contact') ? '' : $employee->emergency_contact) : old('emergency_contact') }}">

                                @error('emergency_contact')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('signature') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Signature</label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="signature">

                                @error('signature')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('photo') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Photo</label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="photo">

                                @error('photo')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('present_address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Present Address *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Present Address"
                                       name="present_address" value="{{ empty(old('present_address')) ? ($errors->has('present_address') ? '' : $employee->present_address) : old('present_address') }}">

                                @error('present_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('permanent_address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Permanent Address *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Permanent Address"
                                       name="permanent_address" value="{{ empty(old('permanent_address')) ? ($errors->has('permanent_address') ? '' : $employee->permanent_address) : old('permanent_address') }}">

                                @error('permanent_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('religion') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Religion *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="religion" >
                                    <option value="">Select Religion</option>
                                    <option value="1" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '1' ? 'selected' : '')) :
                                            (old('religion') == '1' ? 'selected' : '') }}>Muslim</option>
                                    <option value="2" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '2' ? 'selected' : '')) :
                                            (old('religion') == '2' ? 'selected' : '') }}>Hindu</option>
                                    <option value="3" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '3' ? 'selected' : '')) :
                                            (old('religion') == '3' ? 'selected' : '') }}>Christian</option>
                                    <option value="4" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '4' ? 'selected' : '')) :
                                            (old('religion') == '4' ? 'selected' : '') }}>Other</option>
                                </select>

                                @error('religion')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('cv') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">CV</label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="cv">

                                @error('cv')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="form-group {{ $errors->has('bank_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Bank Name </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Bank Name"
                                       name="bank_name" value="{{ empty(old('bank_name')) ? ($errors->has('bank_name') ? '' : $employee->bank_name) : old('bank_name') }}">

                                @error('bank_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('bank_branch') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Bank Branch </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Bank Branch"
                                       name="bank_branch" value="{{ empty(old('bank_branch')) ? ($errors->has('bank_branch') ? '' : $employee->bank_branch) : old('bank_branch') }}">

                                @error('bank_branch')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('bank_account') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Bank Account </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Bank Account"
                                       name="bank_account" value="{{ empty(old('bank_account')) ? ($errors->has('bank_account') ? '' : $employee->bank_account) : old('bank_account') }}">

                                @error('bank_account')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> -->

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(function () {
            //Date picker
            $('#dob').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

            $('.date-picker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            });

            var designationSelected = '{{ empty(old('designation')) ? ($errors->has('designation') ? '' : $employee->designation_id) : old('designation') }}';

            $('#department').change(function () {
                var departmentId = $(this).val();
                $('#designation').html('<option value="">Select Designation</option>');

                if (departmentId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_designation') }}",
                        data: { departmentId: departmentId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (designationSelected == item.id)
                                $('#designation').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#designation').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#department').trigger('change');

            $('#salary_in_jolshiri').change(function () {
                var value = $(this).val();

                if (value == 1) {
                    $('#salary').show();
                } else {
                    $('#salary').hide();
                }
            });

            $('#salary_in_jolshiri').trigger('change');
        });
    </script>
@endsection
