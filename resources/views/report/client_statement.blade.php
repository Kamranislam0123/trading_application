@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .content-wrapper {
            margin-left: 230px !important;
            padding: 15px !important;
        }

        .content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .box {
            margin-bottom: 0;
        }

        /* Responsive handling for smaller screens */
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0 !important;
            }
        }

        .header-section {
            margin-bottom: 30px;
        }

        .company-logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
            margin-left: 20px;
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
            padding-bottom: 10px;
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
@endsection

@section('title')
    Client Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Filter</h3>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <form action="{{ route('report.client_statement') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> Report type </label>

                                    <select class="form-control select2" name="report_type" required>
                                        <option @if (request()->get('report_type')==1) selected @endif value="1"> Due Customer Report</option>
                                        <option @if (request()->get('report_type')==2) selected @endif value="2"> All Customer Report</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label> Client </label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} - {{ $customer->address }} - {{ $customer->mobile_no }} - {{ $customer->branch->name??'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                    <div id="prinarea">

                        <div class="row">
                            <!-- <div class="col-xs-12">
                                @if (Auth::user()->company_branch_id == 2)
                                    <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                                @else
                                    <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                                @endif
                            </div> -->

                            <div style="margin-bottom: 10px;">
                @if (Auth::user()->company_branch_id == 2)
                    <img src="{{ asset('img/Silvia-logo-final.jpg') }}" class="company-logo" alt="Your Choice Plus">
                @else
                    <img src="{{ asset('img/Silvia-logo-final.jpg') }}" class="company-logo" alt="Your Choice">
                @endif
            </div>
                        </div>

                        <div class="company-details">
                                <p><strong>AT International</strong></p>
                                <p>House 07, road 25 (parise road), Block D, Mirpur 10</p>
                                <p>Dhaka, Bangladesh</p>
                                <p>Phone: +8801725838784</p>
                                <p>Email: jimnadvir@gmail.com
                                </p>
                            </div>
                            
                            @if($selected_customer && $selected_customer_data)
                                <div class="customer-details" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                    <h4 style="margin: 0 0 10px 0; color: #333;">Customer Information</h4>
                                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                                        <div><strong>Name:</strong> {{ $selected_customer_data->name }}</div>
                                        <div><strong>Mobile:</strong> {{ $selected_customer_data->mobile_no }}</div>
                                        <div><strong>Address:</strong> {{ $selected_customer_data->address }}</div>
                                        @if($selected_customer_data->branch)
                                            <div><strong>Branch:</strong> {{ $selected_customer_data->branch->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
{{--                        <div class="logo-pad">--}}
{{--                            <img src="{{ asset('img/logo.png') }}" style="position: absolute;opacity: 0.1;height: 553px;width: 650px;margin-top: 130px;margin-left: 65px;">--}}
{{--                        </div>--}}
                        <div class="table-responsive">
                         <table id="table" class="table table-bordered table-striped">
                             <thead>
                                @if($selected_customer)
                                    {{-- Individual Customer Invoice Details Table --}}
                                    <tr>
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Purchase Ammount</th>
                                        <th class="text-center">Receive Payment</th>
                                        <th class="text-center">Return Amount</th>
                                        <th class="text-center">Due Amount</th>
                                    </tr>
                                @else
                                    {{-- Customer Summary Table --}}
                                    <tr>
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Customer Name</th>
                                        <th class="text-center">Current Due</th>
                                        <th class="text-center">Last Payment Date</th>
                                        {{-- <th class="text-center"> Opening Due</th> --}}
                                        {{-- <th class="text-center"> Total Quantity </th> --}}
                                        {{-- <th class="text-center"> Invoice Total </th> --}}
                                        {{-- <th class="text-center"> Return </th> --}}
                                        {{-- <th class="text-center"> Paid </th> --}}
                                    </tr>
                                @endif
                             </thead>
                             <tbody>
                                @if($selected_customer)
                                    {{-- Individual Customer Invoice Details --}}
                                    @php
                                        $totalDue = 0;
                                        $totalAmount = 0;
                                        $totalPayment = 0;
                                        $totalReturn = 0;
                                        $serialNumber = 1;
                                    @endphp
                                    @if($customer_invoices && $customer_invoices->count() > 0)
                                        @foreach($customer_invoices as $key => $payment)
                                            @php
                                                $invoiceAmount = $payment->total_sales_amount ?? 0;
                                                $paymentAmount = $payment->amount ?? 0;
                                                $dueAmount = $invoiceAmount - $paymentAmount;
                                                
                                                $totalAmount += $invoiceAmount;
                                                $totalPayment += $paymentAmount;
                                                $totalReturn += 0; // SalePayment doesn't have return_amount field
                                                $totalDue += $dueAmount;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $serialNumber++ }}</td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($payment->date)->format('d-m-Y') }}</td>
                                                <td class="text-center">৳ {{ number_format($invoiceAmount * nbrCalculation(), 2) }}</td>
                                                <td class="text-center">৳ {{ number_format($paymentAmount * nbrCalculation(), 2) }}</td>
                                                <td class="text-center">৳ 0.00</td>
                                                <td class="text-center">৳ {{ number_format($dueAmount * nbrCalculation(), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="6" style="padding: 20px; color: #666; font-style: italic;">
                                                No invoice data found for this customer. This could mean:
                                                <br>• No sales orders have been created yet
                                                <br>• No payments with invoice amounts have been recorded
                                                <br>• The customer has no transaction history
                                            </td>
                                        </tr>
                                    @endif
                                    {{-- Total Row for Individual Customer --}}
                                    <tr>
                                        <th class="text-center" colspan="2">Total</th>
                                        <th class="text-center">৳ {{ number_format($totalAmount * nbrCalculation(), 2) }}</th>
                                        <th class="text-center">৳ {{ number_format($totalPayment * nbrCalculation(), 2) }}</th>
                                        <th class="text-center">৳ {{ number_format($totalReturn * nbrCalculation(), 2) }}</th>
                                        <th class="text-center">৳ {{ number_format($totalDue * nbrCalculation(), 2) }}</th>
                                    </tr>
                                @else
                                    {{-- Customer Summary --}}
                                    @php
                                        $totalDue = 0;
                                        $serialNumber = 1;
                                    @endphp
                                    @foreach($customers as $key => $customer)
                                    @if ($report_type==1 && $customer->due > 0)
                                        @php
                                            $totalDue += $customer->due;
                                            
                                            // Get the last payment date for this customer (both pending and approved)
                                            $lastPayment = \App\Model\SalePayment::where('customer_id', $customer->id)
                                                ->whereIn('status', [1, 2]) // Both pending and approved payments
                                                ->orderBy('date', 'desc')
                                                ->first();
                                            
                                            $lastPaymentDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->date)->format('d-m-Y') : 'No Payment';
                                            
                                            // Add payment count information
                                            $paymentCount = \App\Model\SalePayment::where('customer_id', $customer->id)->whereIn('status', [1, 2])->count();
                                            if ($paymentCount > 0) {
                                                $lastPaymentDate .= " ({$paymentCount} payments)";
                                            }
                                            
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $serialNumber++ }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td class="text-center">৳ {{ number_format($customer->due * nbrCalculation(), 2) }}</td>
                                            <td class="text-center">{{ $lastPaymentDate }}</td>
                                            {{-- <td class="text-center">৳ {{number_format($customer->opening_due * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->quantity,2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->total * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->return_amount * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->paid * nbrCalculation(),2)}}</td> --}}
                                        </tr>
                                    @elseif($report_type==2)
                                        @php
                                            $totalDue += $customer->due;
                                            
                                            // Get the last payment date for this customer (both pending and approved)
                                            $lastPayment = \App\Model\SalePayment::where('customer_id', $customer->id)
                                                ->whereIn('status', [1, 2]) // Both pending and approved payments
                                                ->orderBy('date', 'desc')
                                                ->first();
                                            
                                            $lastPaymentDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->date)->format('d-m-Y') : 'No Payment';
                                            
                                            // Add payment count information
                                            $paymentCount = \App\Model\SalePayment::where('customer_id', $customer->id)->whereIn('status', [1, 2])->count();
                                            if ($paymentCount > 0) {
                                                $lastPaymentDate .= " ({$paymentCount} payments)";
                                            }
                                            
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $serialNumber++ }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td class="text-center">৳ {{ number_format($customer->due * nbrCalculation(), 2) }}</td>
                                            <td class="text-center">{{ $lastPaymentDate }}</td>
                                            {{-- <td class="text-center">৳ {{number_format($customer->opening_due * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->quantity * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->total * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->return_amount * nbrCalculation(),2)}}</td> --}}
                                            {{-- <td class="text-center">৳ {{number_format($customer->paid * nbrCalculation(),2)}}</td> --}}
                                        </tr>
                                    @endif

                                    @endforeach
                                    {{-- Total Row for Customer Summary --}}
                                    <tr>
                                        <th class="text-center" colspan="2">Total</th>
                                        <th class="text-center">৳ {{ number_format($totalDue * nbrCalculation(), 2) }}</th>
                                        <th class="text-center">-</th>
                                        {{-- <th class="text-center">৳ {{number_format($opening_due * nbrCalculation(),2)}}</th> --}}
                                        {{-- <th class="text-center">৳ {{number_format($totalQuantity * nbrCalculation(),2)}}</th> --}}
                                        {{-- <th class="text-center">৳ {{number_format($total * nbrCalculation(),2)}}</th> --}}
                                        {{-- <th class="text-center">৳ {{number_format($return_amount * nbrCalculation(),2)}}</th> --}}
                                        {{-- <th class="text-center">৳ {{number_format($paid * nbrCalculation(),2)}}</th> --}}
                                    </tr>
                                @endif
                             </tbody>
                         </table>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>

        $(function (){
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

            $('.select2').select2();

        });
        var APP_URL = '{!! url()->current()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
