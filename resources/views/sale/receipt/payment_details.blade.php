@extends('layouts.app')

@section('style')
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
            border-radius: 50%;
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
    </style>
@endsection

@section('title')
    Payment Details
@endsection

@section('content')
    <div class="row" id="receipt-content">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button onclick="openPrintPreview()" class="btn btn-primary">Print Preview</button>
                        </div>
                    </div>

                    <hr>

                    <!-- Header Section -->
                    <div class="row header-section">
                        <div class="col-xs-4">
                            <div style="margin-bottom: 10px;">
                                @if (Auth::user()->company_branch_id == 2)
                                    <img src="{{ asset('img/your_choice_plus.png') }}" class="company-logo" alt="Your Choice Plus">
                                @else
                                    <img src="{{ asset('img/Silvia-logo-final.jpg') }}" class="company-logo" alt="Your Choice">
                                @endif
                            </div>
                            <div class="company-details">
                                <p><strong>AT International</strong></p>
                                <p>House 07, road 25 (parise road), Block D, Mirpur 10</p>
                                <p>Dhaka, Bangladesh</p>
                                <p>Phone: +8801725838784</p>
                                <p>Email: jimnadvir@gmail.com
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-xs-4 text-center">
                            <h1 class="document-title">DEBIT VOUCHER</h1>
                        </div>
                        
                        <div class="col-xs-4 text-right">
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
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">
                                            From
                                    </th>
                                    <td>
                                        {{ $payment->customer->name??'' }}
                                    </td>
                                    <th width="15%"> Received Amount</th>
                                    <td width="15%">৳{{ number_format($payment->receive_amount ?? $payment->amount, 2) }}</td>
                                </tr>

                                <tr>
                                    <th>Received Amount (In Word)</th>
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
                                    <td width="15%">
                                        ৳{{ number_format($payment->total_sales_amount ?? ($payment->salesOrder ? $payment->salesOrder->total : $payment->amount) ?? 0, 2) }}
                                    </td>
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
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPrintPreview() {
            // Open the print view in a new tab without automatic printing
            window.open('{{ route("sale_receipt.payment_print", ["payment" => $payment->id]) }}', '_blank', 'width=800,height=600');
        }
    </script>
@endsection
