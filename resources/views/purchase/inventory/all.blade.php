@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Purchase Inventory
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
                            <th>Product Code</th>
                            <th>Product Model </th>
                            <th>Product Category </th>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Selling Price</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-barcode">
        <div class="modal-dialog">
            <form action="{{ route('barcode_generate') }}" target="_blank">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"> Barcode </h4>
                    </div>
                    <div class="modal-body">
                        <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                            <div class="form-group">
                                <label> Product </label>
                                <input class="form-control" id="product_name" disabled>
                                <input type="hidden" class="form-control" id="purchase_inventory_id" name="purchase_inventory_id">
                            </div>


                            <div class="form-group">
                                <label> Quantity </label>
                                <input class="form-control" name="quantity" value="1" placeholder="Quantity">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> Close </button>
                        <button type="submit" class="btn btn-primary" id="barcode_generate"> Create barcode </button>
                    </div>
                </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('purchase_inventory.datatable') }}',
                columns: [
                    {data: 'serial', name: 'serial'},
                    {data: 'product_item', name: 'productItem.name'},
                    {data: 'product_category', name: 'productCategory.name'},
                    {data: 'warehouse', name: 'warehouse.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'action', name: 'action'},
                ],
            });

            $('body').on('click', '.barcode_modal', function () {
                var product_name = $(this).data('name')+' - '+$(this).data('code');
                var purchase_inventory_id = $(this).data('id');
                $('#product_name').val(product_name);
                $('#purchase_inventory_id').val(purchase_inventory_id);
                $('#modal-barcode').modal('show');
            });
        });
    </script>
@endsection
