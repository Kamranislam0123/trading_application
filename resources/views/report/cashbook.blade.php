 @extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Cashbook
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
                    <form action="{{ route('report.cashbook') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
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
        <div class="col-sm-12" style="min-height:300px">
            @if($result)
                <section class="panel">

                    <div class="panel-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                        <div class="adv-table" id="prinarea">
                            <div class="row">
                                <div class="col-xs-12">
                                    @if (Auth::user()->company_branch_id == 2)
                                        <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                                    @else
                                        <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                                    @endif
                                </div>
                            </div>
                            <div style="padding:10px; width:100%; text-align:center;">
                                @if (Auth::user()->company_branch_id == 1)

                                    <h2>{{ config('app.name', 'Your Choice') }}</h2>
                                    <p style="margin: 0px; font-size: 16px; text-align:center">
                                        Shop# 20-21, Fubaria Super Market-1 (1st Floor)Dhaka-1000<br>
                                        Phone : +8802223381027,, Mobile : 01591-148251(MANAGER)<br>
                                        EMAIL:YOURCHOICE940@YAHOO.COM<br>
                                        HELPLINE: IT DEPARTMENT,,,,MD.PORAN BHUYAIN<br>
                                        MOBILE:01985-511918
                                    </p>
                                @elseif (Auth::user()->company_branch_id == 2)
                                    <h2>Your Choice Plus</h2>
                                    <p style="margin: 0px; text-align:center">
                                        Shop# 23-24, Fubaria Super Market-1 (2nd Floor)Dhaka-1000,<br>
                                        Mobile : 01876-864470(Manager)<br>
                                        EMAIL:YOURCHOICE940@YAHOO.COM<br>
                                        HELPLINE: IT DEPARTMENT,,,,MD.PORAN BHUYAIN<br>
                                        MOBILE: 01985-511918
                                    </p>
                                @endif

                                <h4> <b>Cashbook Report</b> </h4>

                            </div>
                            <div class="table-responsive">
                            <table class="table table-bordered" style="margin-bottom: 0px">
                                <tr>
                                    <th>Opening Balance</th>
                                    <th class="text-center">{{ number_format($openingBalance,2) }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </table>
                            </div>
                            @php
                                $totalIncome = 0;
                                $totalExpense = 0;
                            @endphp
                            @foreach($result as $item)
                                <table class="table table-bordered" style="margin-bottom: 0px">
                                    <tr>
                                        <th class="text-center">{{ date("F d, Y", strtotime($item['date'])) }}</th>
                                    </tr>
                                </table>

                                <div style="clear: both" class="table-responsive">
                                    <table class="table table-bordered" style="width:50%; float:left">
                                        <tr>
                                            <th colspan="5" class="text-center">Income(Credit)</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" width="30%">Particular</th>
                                            <th class="text-center" width="20%">Note</th>
                                            <th class="text-center" width="15%">Payment Type</th>
                                            <th class="text-center" width="20%">Bank Details</th>
                                            <th class="text-center" width="15%">Amount</th>
                                        </tr>

                                        @foreach($item['incomes'] as $income)

                                            <tr>
                                                <td class="text-center">{{ $income->particular }}</td>
                                                <td class="text-center">{{ $income->note }}</td>
                                                <td class="text-center">{{ $income->transaction_method == 1 ? 'Cash' : 'Bank' }}</td>
                                                <td class="text-center">
                                                    @if ($income->transaction_method == 2)
                                                        {{ $income->bank->name??''.' - '.$income->account->account_no??'' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">৳ {{ number_format($income->amount * nbrCalculation(),2) }}</td>
                                            </tr>
                                        @endforeach

                                        <?php
                                        $incomesCount = count($item['incomes']);
                                        $expensesCount = count($item['expenses']);

                                        if ($incomesCount > $expensesCount)
                                            $maxCount = $incomesCount;
                                        else
                                            $maxCount = $expensesCount;

                                        $maxCount += 2;
                                        ?>

                                        @for($i=count($item['incomes']); $i<$maxCount; $i++)
                                            <tr>
                                                <td><br><br></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endfor

                                        <tr>
                                            <th class="text-center" colspan="4">Total</th>
                                            <th class="text-center">৳ {{ number_format($item['incomes']->sum('amount') * nbrCalculation(),2) }}</th>
                                            @php
                                                $totalIncome += $item['incomes']->sum('amount');
                                            @endphp
                                        </tr>
                                    </table>
                                    <div class="table-responsive">
                                    <table class="table table-bordered" style="width:50%; float:left">
                                        <tr>
                                            <th colspan="5" class="text-center">Expense(Debit)</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" width="30%">Particular</th>
                                            <th class="text-center" width="20%">Note</th>
                                            <th class="text-center" width="15%">Payment Type</th>
                                            <th class="text-center" width="20%">Bank Details</th>
                                            <th class="text-center" width="15%">Amount</th>
                                        </tr>

                                        @foreach($item['expenses'] as $expense)
                                            <tr>
                                                <td>{{ $expense->particular }}</td>
                                                <td class="text-center">{{ $expense->note }}</td>
                                                <td class="text-center">{{ $expense->transaction_method == 1 ? 'Cash' : 'Bank' }}</td>
                                                <td class="text-center">
                                                    @if ($expense->transaction_method == 2)
                                                        {{ $expense->bank->name??''.' - '.$expense->account->account_no??'' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">৳ {{ number_format($expense->amount,2) }}</td>
                                            </tr>
                                        @endforeach

                                        @for($i=count($item['expenses']); $i<$maxCount; $i++)
                                            <tr>
                                                <td><br><br></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endfor

                                        <tr>
                                            <th class="text-center" colspan="4">Total</th>
                                            <th class="text-center">৳ {{ number_format($item['expenses']->sum('amount'),2) }}</th>
                                        </tr>
                                       @php
                                           $totalExpense += $item['expenses']->sum('amount');
                                       @endphp
                                    </table>
                                    </div>
                                </div>
                            @endforeach
                            <div class="table-responsive">
                            <table class="table table-bordered" style="margin-bottom: 0px">
                                <tr>
                                    <th>Closing Balance</th>
                                    <th class="text-center">{{ number_format($openingBalance + (($totalIncome * nbrCalculation()) - $totalExpense),2) }}</th>
{{--                                    <th class="text-center">{{ number_format($totalExpense,2) }}</th>--}}
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </table>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
