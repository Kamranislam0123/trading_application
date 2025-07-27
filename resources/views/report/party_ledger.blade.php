@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Party Ledger
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.party_ledger') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Client</label>
                                    <select class="form-control select2" name="client">
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ request()->get('client') == $client->id ? 'selected' : '' }}>{{ $client->name }} - {{ $client->address }} - {{ $client->mobile_no }} - {{ $client->branch->name??'' }}</option>
                                        @endforeach
                                    </select>
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
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off">
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
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>	&nbsp;</label>
                                    <input class="btn btn-primary form-control" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-heading">
                    <a onclick="getprint('prinarea')" role="button" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</a>
                </div>
                <div class="panel-body" id="prinarea">
                    <div class="row">
                        <div class="col-xs-12">
                            @if (Auth::user()->company_branch_id == 2)
                                <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                            @else
                                <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                            @endif
                        </div>
                    </div>
                    @if($clientName)
                        <h2 class="text-center" style="margin: 0">{{ $clientName->name }}</h2>
                        <h3 class="text-center" style="margin-top: 0">Mobile: {{ $clientName->mobile }}</h3>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th class="text-right">Invoice</th>
                                <th class="text-right">Return</th>
                                <th class="text-right">Payment</th>
                                <th class="text-right">Due Balance</th>
                            </tr>
                            <?php $dueBalance = 0;
                            $totalPaid = 0;
                            $totalReturn = 0;
                            $totalInvoice = 0;
                            ?>
                            @if(count($clientHistories) > 0)

                                @foreach($clientHistories as $key => $clientHistory)
                                    <?php
                                    $dueBalance += $clientHistory['due_balance'];
                                    $dueBalance -= $clientHistory['payment'];
                                    $dueBalance -= $clientHistory['return'];
                                    $totalPaid += $clientHistory['payment'];
                                    $totalReturn += $clientHistory['return'];
                                    $totalInvoice += $clientHistory['invoice'];

                                    ?>

                                            <tr>
                                                <td>{{ $clientHistory['date'] }}</td>
                                                <td>{{ $clientHistory['particular'] }}</td>
                                                <td class="text-right">{{ $clientHistory['invoice'] > 0 ? number_format($clientHistory['invoice'] * nbrCalculation(),2) : '' }}</td>
                                                <td class="text-right">{{ $clientHistory['return'] > 0 ? number_format($clientHistory['return'] * nbrCalculation(),2) : '' }}</td>
                                                <td class="text-right">{{ $clientHistory['payment'] > 0 ? number_format($clientHistory['payment'] * nbrCalculation(),2) : ''  }}</td>
                                                <td class="text-right">{{ number_format($dueBalance * nbrCalculation(),2) }}</td>
                                            </tr>
                                @endforeach
                            @endif
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th class="text-right">{{ number_format($totalInvoice * nbrCalculation(),2) }}</th>
                                <th class="text-right">{{ number_format($totalReturn * nbrCalculation(),2) }}</th>
                                <th class="text-right">{{ number_format($totalPaid * nbrCalculation(),2) }}</th>
                                <th class="text-right">{{ number_format($dueBalance * nbrCalculation(),2) }}</th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

        });
        $('.select2').select2();

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
