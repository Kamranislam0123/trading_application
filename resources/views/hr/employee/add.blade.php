@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Employee Add
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
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('employee.add') }}">
                    @csrf
                    <input type="hidden" name="permission[]" value="crm" id="crm">
                    <input type="hidden" name="permission[]" value="marketing" id="marketing">
                    <input type="hidden" name="permission[]" value="proposal" id="proposal">
                    <input type="hidden" name="permission[]" value="proposal_create" id="proposal_create">
                    <input type="hidden" name="permission[]" value="proposals" id="proposals">
                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ old('name') }}" required>

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('employee_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee ID *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Employee ID"
                                       name="employee_id" value="{{ $employeeId }}" readonly required>

                                @error('employee_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Email *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Email"
                                       name="email" value="{{ old('email') }}" required>

                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Password *</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control"
                                       name="password" value="{{ old('password') }}" required>

                                @error('password')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        {{-- <div class="form-group {{ $errors->has('category_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Category *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="category_id" >
                                    <option value="">Select Category</option>
                                    <option value="1" {{ old('category_id') == '1' ? 'selected' : '' }}>Dhaka Office</option>
                                    <option value="2" {{ old('category_id') == '2' ? 'selected' : '' }}>Factory</option>
                                </select>

                                @error('category_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group {{ $errors->has('date_of_birth') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Date of Birth </label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="dob" name="date_of_birth" value="{{ old('date_of_birth') }}" autocomplete="off">
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
                                    <input type="text" class="form-control pull-right date-picker" name="joining_date" value="{{ old('joining_date') }}" autocomplete="off">
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
                                    <input type="text" class="form-control pull-right date-picker" name="confirmation_date" value="{{ old('confirmation_date') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('confirmation_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group {{ $errors->has('department') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Department *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="department" id="department" required>
                                    <option value="">Select Department</option>

                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
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
                                <select class="form-control" name="designation" id="designation" required>
                                    <option value="">Select Designation</option>
                                </select>

                                @error('designation')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group {{ $errors->has('education_qualification') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Education Qualification </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Education Qualification"
                                       name="education_qualification" value="{{ old('education_qualification') }}">

                                @error('education_qualification')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('employee_type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="employee_type" required>
                                    <option value="">Select Employee Type</option>
                                    <option value="1" {{ old('employee_type') == '1' ? 'selected' : '' }}>Permanent</option>
                                    <option value="2" {{ old('employee_type') == '2' ? 'selected' : '' }}>Temporary</option>
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
                                       name="reporting_to" value="{{ old('reporting_to') }}">

                                @error('reporting_to')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('gender') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Gender *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Male</option>
                                    <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>Female</option>
                                </select>

                                @error('gender')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('marital_status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Marital Status *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="marital_status" required>
                                    <option value="">Select Marital Status</option>
                                    <option value="1" {{ old('marital_status') == '1' ? 'selected' : '' }}>Single</option>
                                    <option value="2" {{ old('marital_status') == '2' ? 'selected' : '' }}>Married</option>
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
                                       name="mobile_no" value="{{ old('mobile_no') }}">

                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('father_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Father Name </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Father Name"
                                       name="father_name" value="{{ old('father_name') }}">

                                @error('father_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mother_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mother Name </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mother Name"
                                       name="mother_name" value="{{ old('mother_name') }}">

                                @error('mother_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('emergency_contact') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Emergency Contact *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Emergency Contact"
                                       name="emergency_contact" value="{{ old('emergency_contact') }}" required>

                                @error('emergency_contact')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('signature') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Signature </label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="signature">

                                @error('signature')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('photo') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Photo </label>

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
                                       name="present_address" value="{{ old('present_address') }}" required>

                                @error('present_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('permanent_address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Permanent Address *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Permanent Address"
                                       name="permanent_address" value="{{ old('permanent_address') }}" required>

                                @error('permanent_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('religion') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Religion *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="religion" required>
                                    <option value="">Select Religion</option>
                                    <option value="1" {{ old('religion') == '1' ? 'selected' : '' }}>Muslim</option>
                                    <option value="2" {{ old('religion') == '2' ? 'selected' : '' }}>Hindu</option>
                                    <option value="3" {{ old('religion') == '3' ? 'selected' : '' }}>Christian</option>
                                    <option value="4" {{ old('religion') == '4' ? 'selected' : '' }}>Other</option>
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


                        <div id="salary">
                            <div class="form-group {{ $errors->has('previous_salary') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Previous Salary </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Previous Salary"
                                           name="previous_salary" value="{{ old('previous_salary') }}">

                                    @error('previous_salary')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('gross_salary') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Gross Salary *</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Gross Salary"
                                           name="gross_salary" value="{{ old('gross_salary') }}" required>

                                    @error('gross_salary')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('bank_name') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Bank Name </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Bank Name"
                                           name="bank_name" value="{{ old('bank_name') }}">

                                    @error('bank_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('bank_branch') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Bank Branch </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Bank Branch"
                                           name="bank_branch" value="{{ old('bank_branch') }}">

                                    @error('bank_branch')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('bank_account') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Bank Account </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Bank Account"
                                           name="bank_account" value="{{ old('bank_account') }}">

                                    @error('bank_account')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
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

            var designationSelected = '{{ old('designation') }}';

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
