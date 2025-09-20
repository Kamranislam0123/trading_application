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
                    <a class="btn btn-primary" href="{{ route('employee.add') }}">Add </a>

                    <hr>

                    <table id="table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
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
                    <h4 class="modal-title"> Sales Person Monthly Target </h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form-target" name="modal-form-target">
                        <div class="form-group">
                            <label>Sales Person ID</label>
                            <input class="form-control" id="modal-employee-id-target" disabled>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name-target" disabled>
                        </div>

                        <input type="hidden" name="employee_id" id="modal-id-target">

                        <div class="row">
                            <div class="form-group col-xs-6">
                                <label> From Date </label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="modal-from-date" name="from_date" 
                                           value="{{ date('Y-m-d') }}" autocomplete="off" readonly>
                                </div>
                            </div>

                            <div class="form-group col-xs-6">
                                <label> To Date </label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="modal-to-date" name="to_date" 
                                           value="{{ date('Y-m-d') }}" autocomplete="off" readonly>
                                </div>
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

            //Date picker for target modal
            $('#modal-from-date, #modal-to-date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
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

            $('#modal-from-date, #modal-to-date').change(function () {
                getEmployeeTarget();
            });

            function getEmployeeTarget(){
                var from_date = $('#modal-from-date').val();
                var to_date = $('#modal-to-date').val();
                var employee_id = $('#modal-id-target').val();

                if (employee_id != '' && from_date != '' && to_date != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_employee_target') }}",
                        data: { from_date: from_date, to_date: to_date, employee_id: employee_id }
                    }).done(function( response ) {
                        $('#target_amount').val(response);
                    });
                }
            }

            $('#modal-btn-target-update').click(function () {
                var fromDate = $('#modal-from-date').val();
                var toDate = $('#modal-to-date').val();
                
                // Validate date range
                if (fromDate && toDate && fromDate > toDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'From date cannot be after To date',
                    });
                    return;
                }
                
                var formData = new FormData($('#modal-form-target')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('employee.target_update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response) {
                            $('#modal-employee-target').modal('hide');
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
