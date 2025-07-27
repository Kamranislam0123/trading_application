@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Product Serial
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
                    {{-- <a class="btn btn-primary" href="{{ route('product.add') }}">Add Product</a> --}}

                    <hr>

                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th> Product Serial </th>
                                <th>Product Model </th>
                                <th>Unit </th>
                                <th>Catalog </th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        {{-- <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->productItem->name }}</td>
                                    <td>{{ $product->unit->name??'' }}</td>
                                    <td>{{ $product->catalog }}</td>
                                    <td>
                                        @if ($product->status == 1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('product.edit', ['product' => $product->id]) }}">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    {{-- <script>
        $(function () {
            $('#table').DataTable({
                order: [[ 1, "asc" ]],
            });
        })
    </script> --}}
    <script>
        $(function () {
            var selectedOrderId;

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("product_datatable") }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'product_item', name: 'product_item'},
                    {data: 'unit', name: 'unit.name'},
                    {data: 'catalog', name: 'catalog'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[ 0, "desc" ]],
            });
        });
    </script>
@endsection
