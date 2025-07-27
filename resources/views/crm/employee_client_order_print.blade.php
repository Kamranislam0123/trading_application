
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Bio Access Tech Co.') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>
<div class="container-fluid">
<div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <div style="padding:10px; width:100%; text-align:center;">
                            <h2>Bio-Access Tech Co.</h2>
                            <h4>#House: 9, Road:2/2-1b,Banani, Dhaka-1213, Bangladesh.tel : 02-55040826</h4>
                            <h4>Sale Report</h4>
                        </div>
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th> Employee </th>
                                <th> Mobile </th>
                                <th> Low </th>
                                <th> Medium </th>
                                <th> High </th>
                                <th> Work Order </th>
                                <th> Negative </th>
                                <th> Total </th>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>

