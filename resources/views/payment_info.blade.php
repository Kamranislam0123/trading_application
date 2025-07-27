@extends('layouts.app')

@section('title')
    <!--ড্যাশবোর্ড-->
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-8 col-xs-12 col-sm-offset-2">
            <div class="info-box">
                <br>
                <h3 class="login-box-msg text-danger">Payment Query : 01740059414</h3>
                <p class="login-box-msg" style="font-size: 18px;">
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
                                <td>2A IT</td>
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
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('themes/backend/plugins/chartjs/Chart.bundle.min.js') }}"></script>

@endsection
