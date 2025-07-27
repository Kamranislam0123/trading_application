@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Ledger
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
                    <form action="{{ route('report.ledger') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('account_head_type') ? 'has-error' :'' }}">
                                    <label>Account Head </label>


                                    <select name="account_head_type" class="form-control" id="account_head_type">
                                        <option value="">Select Head</option>
                                        @foreach($accountHead as $head)
                                            <option value="{{$head->id}}" {{ $head->id == request()->get('account_head_type') ? 'selected' : '' }}>{{$head->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('account_head_type')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror


                                    <!-- /.input group -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('account_head_sub_type') ? 'has-error' :'' }}">
                                    <label>Account Sub-head</label>

                                    <select name="account_head_sub_type" class="form-control" id="account_head_sub_type">
                                        <option value="">Select Sub-Head</option>
                                    </select>
                                    @error('account_head_sub_type')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                                    <!-- /.input group -->
                                </div>
                            </div>
                            <div class="col-md-3">
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

                            <div class="col-md-3">
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
            @if($incomes)
                <section class="panel">

                    <div class="panel-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                        <div class="adv-table" id="prinarea">
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
                            </div>
                            <table class="table table-bordered" style="margin-bottom: 0px">
                                <tr>
                                    <th class="text-center">{{ date("F d, Y", strtotime(request()->get('start'))).' - '.date("F d, Y", strtotime(request()->get('end'))) }}</th>
                                </tr>
                            </table>

                            <div style="clear: both">
                                <table class="table table-bordered" style="width:50%; float:left">
                                    <tr>
                                        <th colspan="6" class="text-center">Debit</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="10%">Date</th>
                                        <th class="text-center" width="25%">Particular</th>
                                        <th class="text-center" width="20%">Note</th>
                                        <th class="text-center" width="15%">Payment Type</th>
                                        <th class="text-center" width="20%">Bank Details</th>
                                        <th class="text-center" width="10%">Amount</th>
                                    </tr>

                                    @foreach($incomes as $income)
                                        <tr>
                                            <td class="text-center">{{ $income->date->format('j F, Y') }}</td>
                                            <td>{{ $income->particular }}</td>
                                            <td class="text-center">{{ $income->note }}</td>
                                            <td class="text-center">
                                                @if($income->transaction_method == 1)
                                                    Cash
                                                @elseif($income->transaction_method == 2)
                                                    Bank
                                                @elseif($income->transaction_method == 3)
                                                    Mobile Banking
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($income->transaction_method == 2)
                                                    {{ $income->bank->name.' - '.$income->account->account_no }}
                                                @endif
                                            </td>
                                            <td class="text-center">৳ {{ number_format($income->amount,2) }}</td>
                                        </tr>
                                    @endforeach

                                    <?php
                                    $incomesCount = count($incomes);
                                    $expensesCount = count($expenses);

                                    if ($incomesCount > $expensesCount)
                                        $maxCount = $incomesCount;
                                    else
                                        $maxCount = $expensesCount;

                                    $maxCount += 2;
                                    ?>

                                    @for($i=count($incomes); $i<$maxCount; $i++)
                                        <tr>
                                            <td ><br><br></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <th class="text-center" colspan="5">Total</th>
                                        <th class="text-center">৳ {{ number_format($incomes->sum('amount'),2) }}</th>
                                    </tr>
                                </table>
                                <table class="table table-bordered" style="width:50%; float:left">
                                    <tr>
                                        <th colspan="6" class="text-center">Credit</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="10%">Date</th>
                                        <th class="text-center" width="25%">Particular</th>
                                        <th class="text-center" width="20%">Note</th>
                                        <th class="text-center" width="15%">Payment Type</th>
                                        <th class="text-center" width="20%">Bank Details</th>
                                        <th class="text-center" width="10%">Amount</th>
                                    </tr>

                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td class="text-center">{{ $expense->date->format('j F, Y') }}</td>
                                            <td>{{ $expense->particular }}</td>
                                            <td class="text-center">{{ $expense->note }}</td>
                                            <td class="text-center">
                                                @if($expense->transaction_method == 1)
                                                    Cash
                                                @elseif($expense->transaction_method == 2)
                                                    Bank
                                                @elseif($expense->transaction_method == 3)
                                                    Mobile Banking
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($expense->transaction_method == 2)
                                                    {{ $expense->bank->name.' - '.$expense->account->account_no }}
                                                @endif
                                            </td>
                                            <td class="text-center">৳ {{ number_format($expense->amount,2) }}</td>
                                        </tr>
                                    @endforeach

                                    @for($i=count($expenses); $i<$maxCount; $i++)
                                        <tr>
                                            <td><br><br></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <th class="text-center" colspan="5">Total</th>
                                        <th class="text-center">৳ {{ number_format($expenses->sum('amount'),2) }}</th>
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
    <script>
        $(function () {

            var accountHeadSubTypeSelected = '{{ request()->get('account_head_sub_type') }}';

            $('#account_head_type').change(function () {
                var typeId = $(this).val();

                $('#account_head_sub_type').html('<option value="">Select Account Head Sub Type</option>');

                if (typeId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_account_head_sub_type') }}",
                        data: { typeId: typeId }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (accountHeadSubTypeSelected == item.id)
                                $('#account_head_sub_type').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#account_head_sub_type').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            })
            $('#account_head_type').trigger('change');
            })
    </script>
@endsection
