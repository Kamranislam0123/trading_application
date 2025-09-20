@extends('layouts.app')
@section('title','Dashboard')
@section('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        img{
            border-radius: 25px;
            margin-top: -20px;
            margin-bottom: 26px;
        }
        /*.payment{*/
        /*    margin-bottom: 25px;*/
        /*    margin-top: -26px;*/
        /*    float: right;*/
        /*    margin-right: 41px;*/
        /*}*/
    </style>
@endsection

@section('content')

    @if(Auth::user()->id != 36)

    @if (Auth::user()->company_branch_id == 0 )
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                {{-- <div class="row">
                    <div class="col-md-5">
                        <img src="{{asset('img/it_department.jpeg')}}" height="250">
                    </div>
                    <div class="col-md-5">
                        <img src="{{asset('img/it_department_2.jpeg')}}" height="250">
                    </div>
                    <div class="col-md-2">
                        <a  class="btn btn-primary btn-sm text-right payment" href="{{ route('payment_info') }}">Payment Details</a>
                    </div>
                </div> --}}
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Invoice Amount</span>
                    <span class="info-box-number">৳{{ number_format($totalInvoiceAmount * nbrCalculation(), 2) }}</span>
                </div>
                
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>


        
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Received Amount</span>
                    <span class="info-box-number">৳{{ number_format($totalReceivedAmount * nbrCalculation(), 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-spinner"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Due</span>
                    <span class="info-box-number">৳{{ number_format($totalDue * nbrCalculation(), 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
      

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Total Due Collection</span>
                    <span class="info-box-number">৳{{ number_format($todayDueCollection * nbrCalculation(), 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fa fa-shopping-bag"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Total Collection</span>
                    <span class="info-box-number">৳{{ number_format($todaySale * nbrCalculation(), 2) }}</span>
                    <small class="text-muted">Due + Received</small>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-orange"><i class="fa fa-money"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Cash Sales</span>
                    <span class="info-box-number">৳{{ number_format($todayCashSale * nbrCalculation(), 2) }}</span>
                </div>
                
            </div>
            
        </div> -->
        
        <!-- <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-maroon"><i class="fa fa-credit-card"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Total Expense</span>
                    <span class="info-box-number">৳{{ number_format($todayExpense * nbrCalculation(), 2) }}</span>
                </div>
               
            </div>
            
        </div> -->
        <!-- /.col -->


        {{-- <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Total Expense</span>
                    <span class="info-box-number">৳{{ number_format($todayExpense, 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col --> --}}

        {{-- @if (Auth::user()->company_branch_id == 0)
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #303d99 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Datascape IT Sale</span>
                        <span class="info-box-number">৳{{ number_format($todayYourChoiceSale * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #b61f4f !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Datascape IT Plus Sale</span>
                        <span class="info-box-number">৳{{ number_format($todayYourChoicePlusSale * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #077ee5 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice Due</span>
                        <span class="info-box-number">৳{{ number_format($todayYourChoiceDue * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #0de3ed !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice Plus Due</span>
                        <span class="info-box-number">৳{{ number_format($todayYourChoicePlusDue * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #551831 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice CashSale</span>
                        <span class="info-box-number">৳{{ number_format($todayChoiceCashSale * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #040b2c !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice Plus CashSale</span>
                        <span class="info-box-number">৳{{ number_format($todayChoicePlusCashSale * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #093c20 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice DueCollection</span>
                        <span class="info-box-number">৳{{ number_format($todayChoiceDueCollection * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #dd6612 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice Plus DueCollection</span>
                        <span class="info-box-number">৳{{ number_format($todayChoicePlusDueCollection * nbrCalculation(), 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #f70505 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice Expense</span>
                        <span class="info-box-number">৳{{ number_format($todayChoiceExpense, 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span style="background-color: #b00dc4 !important;" class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Today's Your Choice Plus Expense</span>
                        <span class="info-box-number">৳{{ number_format($todayChoicePlusExpense, 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        @endif --}}
    </div>

    <!-- Pending Cheques Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">All Due List</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="pending-cheques-table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Payment Method</th>
                                <th>Opening Due</th>
                                <th>Total Amount</th>
                                <th>Receive Amount</th>
                                <th>Due Amount</th>
                                <th>Note</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pendingCheques as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('d-m-Y') }}</td>
                                    <td>{{ $payment->invoice_no ?? 'N/A' }}</td>
                                    <td>{{ $payment->customer->name ?? 'N/A' }}</td>
                                    <td>
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
                                    <td>৳{{ number_format($payment->opening_due_amount ?? 0, 2) }}</td>
                                    <td>৳{{ number_format($payment->total_sales_amount ?? $payment->amount, 2) }}</td>
                                    <td>৳{{ number_format($payment->receive_amount ?? $payment->amount, 2) }}</td>
                                    <td>৳{{ number_format($payment->due_amount ?? 0, 2) }}</td>
                                    <td>{{ $payment->note ?? 'N/A' }}</td>
                                    <td>
                                        @if($payment->status == 1)
                                            <span class="label label-warning">Pending</span>
                                        @elseif($payment->status == 2)
                                            <span class="label label-success">Approved</span>
                                        @else
                                            <span class="label label-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
               
            </div>
        </div>
    </div>

    <!-- Next Day Payments Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Next Day Payments (Due Tomorrow)</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="next-day-payments-table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Sales Person</th>
                                <th>Payment Method</th>
                                <th>Opening Due</th>
                                <th>Total Amount</th>
                                <th>Receive Amount</th>
                                <th>Due Amount</th>
                                <th>Next Payment Date</th>
                                <th>Note</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nextDayPayments as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('d-m-Y') }}</td>
                                    <td>{{ $payment->invoice_no ?? 'N/A' }}</td>
                                    <td>{{ $payment->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->salesPerson->name ?? 'N/A' }}</td>
                                    <td>
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
                                    <td>৳{{ number_format($payment->opening_due_amount ?? 0, 2) }}</td>
                                    <td>৳{{ number_format($payment->total_sales_amount ?? $payment->amount, 2) }}</td>
                                    <td>৳{{ number_format($payment->receive_amount ?? $payment->amount, 2) }}</td>
                                    <td>৳{{ number_format($payment->due_amount ?? 0, 2) }}</td>
                                    <td>
                                        @php
                                            $nextPaymentDate = $payment->next_approximate_payment_date ?? $payment->next_payment_date;
                                        @endphp
                                        @if($nextPaymentDate)
                                            {{ \Carbon\Carbon::parse($nextPaymentDate)->format('d-m-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $payment->note ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Payments Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Today's Payments</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="today-payments-table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Sales Person</th>
                                <th>Payment Method</th>
                                <th>Opening Due</th>
                                <th>Total Amount</th>
                                <th>Receive Amount</th>
                                <th>Due Amount</th>
                                <th>Note</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todayPayments as $payment)
                                <tr>
                                    <td>{{ $payment->date->format('d-m-Y') }}</td>
                                    <td>{{ $payment->invoice_no ?? 'N/A' }}</td>
                                    <td>{{ $payment->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->salesPerson->name ?? 'N/A' }}</td>
                                    <td>
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
                                    <td>৳{{ number_format($payment->opening_due_amount ?? 0, 2) }}</td>
                                    <td>৳{{ number_format($payment->total_sales_amount ?? $payment->amount, 2) }}</td>
                                    <td>৳{{ number_format($payment->receive_amount ?? $payment->amount, 2) }}</td>
                                    <td>৳{{ number_format($payment->due_amount ?? 0, 2) }}</td>
                                    <td>{{ $payment->note ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Today's Sales Order</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div  class="table-responsive">
                        <table id="table_id" class="table no-margin">
                            <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Branch Name</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todaySaleReceipt as $receipt)
                                <tr>
                                    @if(auth()->user()->role != 2)
                                        <td><a href="{{ route('sale_receipt.details', ['order' => $receipt->id]) }}">{{ $receipt->order_no }}</a></td>
                                    @else
                                        <td>{{ $receipt->order_no }}</td>
                                    @endif
                                    <td>{{ $receipt->customer->name }}</td>
                                    <td>{{ $receipt->customer->branch->name }}</td>
                                    <td>{{ number_format($receipt->quantity(), 2) }}</td>
                                    <td>৳{{ number_format($receipt->total * nbrCalculation(), 2) }}</td>
                                    <td>৳{{ number_format($receipt->paid * nbrCalculation(), 2) }}</td>
                                    <td>৳{{ number_format($receipt->current_due * nbrCalculation(), 2) }}</td>
                                    <td>{{ $receipt->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
               <div class="box-footer clearfix">
                   {{ $todaySaleReceipt->links() }}
                </div>
                <!-- /.box-footer -->
            </div>
        </div> 

        {{-- @if (Auth::user()->company_branch_id == 0)
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Today's Purchase Order</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="p_table" class="table no-margin">
                                <thead>
                                <tr>
                                    <th>Order No.</th>
                                    <th>Supplier</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Created At</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($todayPurchaseReceipt as $receipt)
                                    <tr>
                                        <td><a href="{{ route('purchase_receipt.details', ['order' => $receipt->id]) }}">{{ $receipt->order_no }}</a></td>
                                        <td>{{ $receipt->supplier->name }}</td>
                                        <td>{{ $receipt->quantity() }}</td>
                                        <td>৳{{ number_format($receipt->total, 2) }}</td>
                                        <td>{{ $receipt->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
{{--                    <div class="box-footer clearfix">--}}
{{--                        {{ $todaySaleReceipt->links() }}--}}
{{--                    </div>--}}
                    <!-- /.box-footer -->
                </div>
            </div>
        {{-- @else
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Best Selling Products</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Count</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bestSellingProducts as $product)
                                        <tr>
                                            <td>{{ $product->productItem->name }}</td>
                                            <td>{{ $product->count }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

                @if (Auth::user()->company_branch_id == 0)
                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Recently Added Products</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-margin">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Created At</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($recentlyProducts as $product)
                                            <tr>
                                                <td>{{ $product->productItem->name }}</td>
                                                <td>{{ $product->created_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                @endif
            </div>
        @endif --}}

    </div>

    {{-- @if (Auth::user()->company_branch_id == 0)
        <div class="row">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Best Selling Products</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bestSellingProducts as $product)
                                    <tr>
                                        <td>{{ $product->productItem->name }}</td>
                                        <td>{{ $product->count }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>

            @if (Auth::user()->company_branch_id == 0)
                <div class="col-md-6">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Recently Added Products</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Created At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recentlyProducts as $product)
                                        <tr>
                                            <td>{{ $product->productItem->name }}</td>
                                            <td>{{ $product->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            @endif
        </div>
    @endif --}}

    {{-- <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Sales History</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="">
                    <canvas id="chart-sales-amount" width="100%" height="30"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div> --}}

    {{-- <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Order Count History</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="">
                    <canvas id="chart-order-count" width="100%" height="30"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div> --}}
    @endif
@endsection

@section('script')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable({
            ordering: false
            });

            $('#p_table').DataTable({ordering: false});
            
            $('#pending-cheques-table').DataTable({
                ordering: false,
                pageLength: 10,
                responsive: true
            });

            $('#next-day-payments-table').DataTable({
                ordering: false,
                pageLength: 10,
                responsive: true
            });

            $('#today-payments-table').DataTable({
                ordering: false,
                pageLength: 10,
                responsive: true
            });
        } );
    </script>

@endsection
