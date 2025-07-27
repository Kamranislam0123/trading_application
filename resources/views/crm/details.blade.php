@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Client Operation Details
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
{{--                    <a class="btn btn-primary" href="{{ route('marketing_jyoti.add') }}">Add Client Jyoti </a>--}}

                    <hr>

                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Client</th>
                            <th>Company</th>
                            <th>Mobile</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Service Type</th>
                            <th>Remark</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($clients as $client)
                            <tr>

                                <td>{{ $client->client_name }}</td>
                                <td>{{ $client->company_name }}</td>
                                <td>{{ $client->mobile }}</td>
                                <td>{{$client->date}}</td>
                                <td>
                                    @if ($client->status == 1)
                                        <span class="label label-warning">Low</span>
                                    @elseif($client->status == 2)
                                        <span class="label label-primary">Medium</span>
                                    @elseif($client->status == 3)
                                        <span class="label label-info">High</span>
                                    @elseif($client->status == 4)
                                        <span class="label label-success">Work Order</span>
                                    @elseif($client->status == 5)
                                        <span class="label label-danger">Negative</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $client->client_service->name??'' }}
                                </td>
                                <td>{{$client->remark}}</td>
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
