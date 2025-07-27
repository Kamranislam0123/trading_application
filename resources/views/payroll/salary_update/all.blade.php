@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Salary Update
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
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Type</th>
                            <th>Mobile</th>
                            <th>Gross Salary</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-update-salary">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Salary</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <div class="form-group">
                            <label>Employee ID</label>
                            <input class="form-control" id="modal-employee-id" disabled>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <input class="form-control" id="modal-department" disabled>
                        </div>

                        <div class="form-group">
                            <label>Designation</label>
                            <input class="form-control" id="modal-designation" disabled>
                        </div>

                        <input type="hidden" name="id" id="modal-id">

                        <div class="form-group">
                            <label>Basic Salary</label>
                            <input type="text" class="form-control" name="basic_salary" id="modal-basic-salary" placeholder="Enter Basic Salary" readonly>
                        </div>

                        <div class="form-group">
                            <label>House Rent</label>
                            <input type="text" class="form-control" name="house_rent" id="modal-house-rent" placeholder="Enter House Rent" readonly>
                        </div>

                        <div class="form-group">
                            <label>Travel</label>
                            <input type="text" class="form-control" name="travel" id="modal-travel" placeholder="Enter Travel" readonly>
                        </div>

                        <div class="form-group">
                            <label>Medical</label>
                            <input type="text" class="form-control" name="medical" id="modal-medical" placeholder="Enter Medical" readonly>
                        </div>

                        <div class="form-group">
                            <label>Tax</label>
                            <input type="text" class="form-control" name="tax" id="modal-tax" placeholder="Enter Tax">
                        </div>

                        <div class="form-group">
                            <label>Others Deduct</label>
                            <input type="text" class="form-control" name="others_deduct" id="modal-others-deduct" placeholder="Enter Others Deduct">
                        </div>

                        <div class="form-group">
                            <label>Gross Salary</label>
                            <input type="text" class="form-control" name="gross_salary" id="modal-gross-salary" placeholder="Enter Gross Salary">
                        </div>

                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type">
                                <option value="">Select Type</option>
                                <option value="1">Confirmation</option>
                                <option value="2">Yearly Increment</option>
                                <option value="3">Promotion</option>
                                <option value="5">Advance</option>
                                <option value="4">Other</option>
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
    <!-- /.modal -->
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('payroll.salary_update.datatable') }}',
                columns: [
                    {data: 'photo', name: 'photo', orderable: false},
                    {data: 'employee_id', name: 'employee_id'},
                    {data: 'name', name: 'name'},
                    {data: 'department', name: 'department.name'},
                    {data: 'designation', name: 'designation.name'},
                    {data: 'employee_type', name: 'employee_type'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'gross_salary', name: 'gross_salary'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[ 1, "asc" ]],
            });

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('body').on('click', '.btn-update', function () {
                var employeeId = $(this).data('id');

                $.ajax({
                    method: "GET",
                    url: "{{ route('get_employee_details') }}",
                    data: { employeeId: employeeId }
                }).done(function( response ) {
                    console.log(response);
                    $('#modal-employee-id').val(response.employee_id);
                    $('#modal-name').val(response.name);
                    $('#modal-department').val(response.department.name);
                    $('#modal-designation').val(response.designation.name);
                    $('#modal-id').val(response.id);
                    $('#modal-basic-salary').val(response.basic_salary);
                    $('#modal-house-rent').val(response.house_rent);
                    $('#modal-travel').val(response.travel);
                    $('#modal-medical').val(response.medical);
                    $('#modal-tax').val(response.tax);
                    $('#modal-others-deduct').val(response.others_deduct);
                    $('#modal-gross-salary').val(response.gross_salary);

                    $('#modal-update-salary').modal('show');
                });
            });

            $('#modal-btn-update').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('payroll.salary_update.post') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-update-salary').modal('hide');
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
        })
    </script>
@endsection
