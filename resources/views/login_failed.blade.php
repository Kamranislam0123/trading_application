<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Fevicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/iCheck/square/blue.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        p.login-box-msg {
            font-size: 18px;
            color: #000;
        }
        i.fa.fa-exclamation-triangle {
            color: #f44336;
            font-size: 64px;
        }
        .login-logo span {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body class="hold-transition login-page">

<div class="login-box" style="width: 700px">
    <div class="login-box-body">
        <div class="login-logo">
            <i class="fa fa-exclamation-triangle"></i><br>
            <span>Your Account is Suspended, Can't Access.</span>
        </div>

        <h3 class="login-box-msg text-danger">Payment Query : 01740059414</h3>
        <p class="login-box-msg">
            Maintenance payment last date 7<sup>th</sup> of the month.<br>
            Maintenance amount: {{ '1500' }}
        </p>
          <table class="table table-bordered">

                            <tr>
                                <th class="text-center" colspan="2">Bkash Payment</th>
                            </tr>
                            <tr>
                                <td>Personal Bkash Number</td>
                                <td>01727843280</td>
                            </tr>
                              <tr>
                                <td>Amount</td>
                                <td>1500</td>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="2">Bank Payment</th>
                            </tr>
                            <tr>
                                <td>Account Name</td>
                                <td>2A IT Limited</td>
                            </tr>
                             <tr>
                                <td>Account Number</td>
                                <td>1401974595001</td>
                            </tr>
                             <tr>
                                <td>Bank Name</td>
                                <td>City Bank</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>1500</td>
                            </tr>

                    </table>
        <hr>
        <a target="_blank" href="http://2aitbd.com">
            <img style="display: block; margin-left: auto; margin-right: auto; height: 50px;" src="{{ asset('img/2ait.png') }}">
        </a>
        <p style="text-align:center; font-size:14px; color:Blue;">
            <a target="_blank" href="http://2aitbd.com">
                <strong style="color: #0b4444">Design & Develop</strong>
            </a>
        </p>
    </div>
</div>
<!-- jQuery 3 -->
<script src="{{ asset('themes/backend/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('themes/backend/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('themes/backend/plugins/iCheck/icheck.min.js') }}"></script>
</body>
</html>
