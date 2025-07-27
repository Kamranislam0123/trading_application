@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Employee
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <a class="btn btn-primary" href="{{ route('employee.add') }}">Add Employee</a>

                    <hr>

                    <table id="table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Type</th>
                            <th>Mobile</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-update-designation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Designation</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form" name="modal-form">
                        <div class="form-group">
                            <label>Employee ID</label>
                            <input class="form-control" id="modal-employee-id" disabled>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>

                        <input type="hidden" name="id" id="modal-id">

                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control" id="modal-department" name="department">
                                <option value="">Select Department</option>

                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Designation</label>
                            <select class="form-control" id="modal-designation" name="designation">
                                <option value="">Select Designation</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-update">Update</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-employee-target">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Employee Monthly Target </h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form-target" name="modal-form-target">
                        <div class="form-group">
                            <label>Employee ID</label>
                            <input class="form-control" id="modal-employee-id-target" disabled>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name-target" disabled>
                        </div>

                        <input type="hidden" name="employee_id" id="modal-id-target">

                        <div class="row">
                            <div class="form-group col-xs-6">
                                <label> Month </label>
                                <select class="form-control" id="modal-month" name="month">
                                    <option value="">Select Month </option>
                                    <option @if (date('m') == 1) selected @endif value="01"> January </option>
                                    <option @if (date('m') == 2) selected @endif value="02"> February </option>
                                    <option @if (date('m') == 3) selected @endif value="03"> March </option>
                                    <option @if (date('m') == 4) selected @endif value="04"> April </option>
                                    <option @if (date('m') == 5) selected @endif value="05"> May </option>
                                    <option @if (date('m') == 6) selected @endif value="06"> June </option>
                                    <option @if (date('m') == 7) selected @endif value="07"> July </option>
                                    <option @if (date('m') == 8) selected @endif value="08"> August </option>
                                    <option @if (date('m') == 9) selected @endif value="09"> September </option>
                                    <option @if (date('m') == 10) selected @endif value="10"> October </option>
                                    <option @if (date('m') == 11) selected @endif value="11"> Nobember </option>
                                    <option @if (date('m') == 12) selected @endif value="12"> December </option>
                                </select>
                            </div>

                            <div class="form-group col-xs-6">
                                <label> Year </label>
                                <select class="form-control" id="modal-year" name="year">
                                    <option value="">Select Year</option>
                                    @for ($i = 2020; $i < 2035; $i++)
                                        <option @if (date('Y') == $i) selected @endif value="{{ $i }}"> {{ $i }} </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label> Target Amount </label>
                            <input type="text" class="form-control pull-right" id="target_amount" name="amount" value="" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-target-update">Update</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>s

    <script>
        $(function () {
            var designationSelected;

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('employee.datatable') }}',
                columns: [
                    {data: 'photo', name: 'photo', orderable: false},
                    {data: 'employee_id', name: 'employee_id'},
                    {data: 'name', name: 'name'},
                    {data: 'department', name: 'department.name'},
                    {data: 'designation', name: 'designation.name'},
                    {data: 'employee_type', name: 'employee_type'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[ 1, "asc" ]],
            });

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('body').on('click', '.btn-change-designation', function () {
                var employeeId = $(this).data('id');

                $.ajax({
                    method: "GET",
                    url: "{{ route('get_employee_details') }}",
                    data: { employeeId: employeeId }
                }).done(function( response ) {
                    $('#modal-employee-id').val(response.employee_id);
                    $('#modal-name').val(response.name);
                    $('#modal-id').val(response.id);
                    $('#modal-department').val(response.department_id);
                    designationSelected = response.designation_id;
                    $('#modal-department').trigger('change');

                    $('#modal-update-designation').modal('show');
                });
            });

            $('#modal-department').change(function () {
                var departmentId = $(this).val();
                $('#modal-designation').html('<option value="">Select Designation</option>');

                if (departmentId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_designation') }}",
                        data: { departmentId: departmentId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (designationSelected == item.id)
                                $('#modal-designation').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#modal-designation').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        designationSelected = '';
                    });
                }
            });

            $('#modal-btn-update').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('employee.designation_update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-update-designation').modal('hide');
                            Swal.fire(
                                'Updated!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });

            // Employee Target setup 
            $('body').on('click', '.btn-employee-target', function () {
                var employeeId = $(this).data('id');

                $.ajax({
                    method: "GET",
                    url: "{{ route('get_employee_details') }}",
                    data: { employeeId: employeeId }
                }).done(function( response ) {
                    $('#modal-employee-id-target').val(response.employee_id);
                    $('#modal-name-target').val(response.name);
                    $('#modal-id-target').val(response.id);
                    $('#modal-employee-target').modal('show');
                    // Get monthly employee Target
                    getEmployeeTarget();
                });
            });

            $('#modal-month, #modal-year').change(function () {
                getEmployeeTarget();
            });

            function getEmployeeTarget(){
                var year = $('#modal-year').val();
                var month = $('#modal-month').val();
                var employee_id = $('#modal-id-target').val();
                // alert(employee_id);

                if (employee_id != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_employee_target') }}",
                        data: { year: year,month:month,employee_id:employee_id }
                    }).done(function( response ) {
                        // alert(response);
                        $('#target_amount').val(response);
                    });
                }
            }

            $('#modal-btn-target-update').click(function () {
                var formData = new FormData($('#modal-form-target')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('employee.target_update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response) {
                            $('#modal-btn-target-update').modal('hide');
                            Swal.fire(
                                'Updated!',
                                response,
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });
            });

        })
    </script>
@endsection
