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
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header-section {
            margin-bottom: 30px;
        }

        .company-logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            padding: 10px 0;
        }

        .company-details {
            font-size: 11px;
            line-height: 1.3;
            margin-top: 40px;
        }

        .company-details p {
            margin: 2px 0;
        }

        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            border: 1px solid black !important;
            padding: 8px !important;
            font-size: 12px;
        }

        .signature-section {
            margin-top: 40px;
        }

        .signature-line {
            border-top: 1px solid black;
            width: 200px;
            margin-top: 50px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding-top: 5px;
        }

        /* Hide print buttons when printing */
        @media print {
            .print-buttons {
                display: none !important;
            }
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row header-section">
        <div class="col-xs-4">
            <div class="company-details">
                <p><strong>AT International</strong></p>
                <p>Company Address</p>
                <p>City, Country</p>
                <p>Phone: +880-XXX-XXX-XXX</p>
                <p>Email: info@atinternational.com</p>
            </div>
        </div>
        
        <div class="col-xs-4 text-center">
            <h1 class="document-title">DEBIT VOUCHER</h1>
        </div>
        
        <div class="col-xs-4 text-right">
            <div style="margin-bottom: 10px;">
                @if (Auth::user()->company_branch_id == 2)
                    <img src="{{ asset('img/your_choice_plus.png') }}" class="company-logo" alt="Your Choice Plus">
                @else
                    <img src="{{ asset('img/logo.png') }}" class="company-logo" alt="Your Choice">
                @endif
            </div>
            <div class="company-details">
                <p><strong>{{ $payment->customer->name ?? 'Customer Name' }}</strong></p>
                <p>{{ $payment->customer->address ?? 'Customer Address' }}</p>
                <p>{{ $payment->customer->mobile_no ?? 'Customer Mobile' }}</p>
                <p>{{ $payment->customer->email ?? 'Customer Email' }}</p>
            </div>
        </div>
    </div>
    
    <!-- Document Info Section -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-6">
            <strong>Date:</strong> {{ $payment->date->format('j F, Y') }}
        </div>
        <div class="col-xs-6 text-right">
            <strong>Invoice No:</strong> {{ $payment->invoice_no ?? 'N/A' }}
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
                    <th width="10%"> Received Amount</th>
                    <td width="15%">৳{{ number_format($payment->receive_amount ?? $payment->amount, 2) }}</td>
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
                    <td colspan="3">
                        @if($payment->transaction_method == 1)
                            Cash
                        @elseif($payment->transaction_method == 2)
                            Bank
                        @elseif($payment->transaction_method == 3)
                            Mobile Banking
                        @elseif($payment->transaction_method == 4)
                            Sale Adjustment Discount
                        @elseif($payment->transaction_method == 5)
                            Return Adjustment Amount
                        @else
                            {{ $payment->payment_method ?? 'Cash' }}
                        @endif
                    </td>
                </tr>

                {{-- @if ($payment->status == 1)
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

                @endif --}}

                <tr>
                    <th>Note</th>
                    <td colspan="3">{{ $payment->note }}</td>
                </tr>
                <tr>
                    <th width="20%">
                        Due Amount
                    </th>
                    <td>
                        ৳{{ number_format($payment->due_amount ?? 0, 2) }}
                    </td>
                    <th width="15%">Total Amount</th>
                    <td width="15%">৳{{ number_format($payment->total_sales_amount ?? 0, 2) }}</td>
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

<!-- Signature Section -->
<div class="row signature-section">
    <div class="col-xs-6">
        <div class="signature-line">
            Received By
        </div>
    </div>
    <div class="col-xs-6">
        <div class="signature-line" style="margin-left: auto;">
            Approved By
        </div>
    </div>
</div>

<div class="row print-buttons" style="margin-top: 20px; text-align: center;">
    <div class="col-xs-12">
        <button onclick="printDocument()" class="btn btn-primary btn-lg">Print</button>
        <button onclick="window.close()" class="btn btn-default btn-lg" style="margin-left: 10px;">Close</button>
    </div>
</div>

<script>
    function printDocument() {
        window.print();
    }
    
    // Optional: Close window after printing
    window.onafterprint = function(){ 
        // Uncomment the line below if you want to auto-close after printing
        // window.close();
    };
</script>
</body>
</html>
