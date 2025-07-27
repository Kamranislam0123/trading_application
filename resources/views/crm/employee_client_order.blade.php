@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Client Work Order
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
                   <a class="btn btn-primary" href="{{ route('employee.client.order.print') }}" target="_blank"> Print </a>

                    <hr>

                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th> Employee </th>
                            <th> Mobile </th>
                            <th>Low</th>
                            <th>Medium</th>
                            <th>High</th>
                            <th>Work Order</th>
                            <th>Negative</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>{{ $client->marketing->name??'' }}</td>
                                    <td>{{ $client->marketing->mobile??'' }}</td>
                                    <td>{{ $client->employee_client_orders($client->marketing_id)->where('status',1)->count() }}</td>
                                    <td>{{ $client->employee_client_orders($client->marketing_id)->where('status',2)->count() }}</td>
                                    <td>{{ $client->employee_client_orders($client->marketing_id)->where('status',3)->count() }}</td>
                                    <td>{{ $client->employee_client_orders($client->marketing_id)->where('status',4)->count() }}</td>
                                    <td>{{ $client->employee_client_orders($client->marketing_id)->where('status',5)->count() }}</td>
                                    <td>{{ $client->employee_client_orders($client->marketing_id)->count() }}</td>
                                    <td>
                                        <a href="{{ route('employee.work.orders', $client->marketing_id) }}" class="btn btn-sm btn-info" target="_blank"> Details </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--    update Modal start--}}


@endsection


@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $('#table').DataTable();
        })


    </script>
@endsection
