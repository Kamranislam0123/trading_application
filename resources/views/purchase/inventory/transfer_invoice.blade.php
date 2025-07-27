@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
     Stock Transfer list
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
                <div class="box-body">
                    <a class="btn btn-primary" href="{{ route('purchase_stock_transfer') }}">Add Stock Transfer</a>
                    <hr>
                    <div class="table-responsive">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th> Date</th>
                            <th> Transfer Order No </th>
                            <th> Source Warehouse </th>
                            <th> Target Warehouse </th>
                            <th> Total Quantity </th>
                            <th> Action</th>
                        </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(function () {
            var selectedOrderId;

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                'order': false,
                ajax: '{{ route("stock_transfer.datatable") }}',
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'order_no', name: 'order_no'},
                    {data: 'source_warehouse', name: 'sourchWarehouse.name'},
                    {data: 'target_warehouse', name: 'targetWarehouse.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'action', name: 'action', orderable: false},
                ],
            });
        });
    </script>
@endsection
