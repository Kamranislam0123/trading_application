<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <style>
        #receipt-content{
            font-size: 18px;
        }

        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            border: 1px solid black !important;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        @if (Auth::user()->company_branch_id == 2)
            <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
        @else
            <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
        @endif
        <br>

        <div class="col-xs-6 text-left">
            <b>Date: </b> {{ $payment->date->format('j F, Y') }}
        </div>

        <div class="col-xs-6 text-right">
            <b>No: </b> {{ str_pad($payment->id, 5, 0, STR_PAD_LEFT) }}
        </div>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-xs-12">
            <table class="table table-bordered">
                <tr>
                    <th width="20%">
                        From
                    </th>
                    <td>
                        {{ $payment->customer->name??'' }}
                    </td>
                    <th width="10%">Amount</th>
                    <td width="15%">৳{{ number_format($payment->amount, 2) }}</td>
                </tr>

                <tr>
                    <th>Amount (In Word)</th>
                    <td colspan="3">{{ $payment->amount_in_word }}</td>
                </tr>

                {{-- <tr>
                    <th>For Payment of</th>
                    <td colspan="3">Order No. {{ $payment->salesOrder->order_no }}</td>
                </tr> --}}

                <tr>
                    <th>Paid By</th>
                    @if ($payment->status == 1)
                        <td colspan="4">Bank-Cheque Pending</td>
                    @else
                        <td colspan="3">
                            @if($payment->transaction_method == 1)
                                Cash
                            @elseif($payment->transaction_method == 3)
                                Mobile Banking
                            @elseif($payment->transaction_method == 4)
                                Sale Adjustment Discount
                            @elseif($payment->transaction_method == 5)
                                Return Adjustment Amount
                            @elseif(empty($payment->bank->name))
                                Cheque CashIn
                            @else
                                Bank - {{ $payment->bank->name??''.' - '.$payment->branch->name??''.' - '.$payment->account->account_no??'' }}
                            @endif
                        </td>
                    @endif
                </tr>

                @if ($payment->status == 1)
                    <tr>
                        <th>Pending Cheque No.</th>
                        <td colspan="3">{{ $payment->client_cheque_no }}</td>
                    </tr>
                @else
                    @if($payment->transaction_method == 2)
                        <tr>
                            <th>Cheque No.</th>
                            <td colspan="3">{{ $payment->cheque_no }}</td>
                        </tr>
                    @endif

                @endif

                <tr>
                    <th>Note</th>
                    <td colspan="3">{{ $payment->note }}</td>
                </tr>
                <tr>
                    <th width="20%">
                        Current Due
                    </th>
                    <td>

                    </td>
                    <th width="15%">Amount</th>
                    <td width="15%">৳{{ number_format($payment->customer->due, 2) }}</td>
                </tr>

                @if ($payment->status == 2)

                    @if($payment->transaction_method == 2)
                        <tr>
                            <th>Cheque Image</th>
                            <td colspan="3" class="text-center">
                                <img src="{{ asset($payment->cheque_image) }}" height="300px">
                            </td>
                        </tr>
                    @endif
                @endif
            </table>
        </div>
    </div>
</div>


<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
