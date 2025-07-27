@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('title')
    Bank Statement
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
                    <form action="{{ route('report.bank_statement') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Bank</label>

                                    <select class="form-control" id="bank" name="bank" required>
                                        <option value="">Select Bank</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ request()->get('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Branch</label>

                                    <select class="form-control" id="branch" name="branch" required>
                                        <option value="">Select Branch</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account</label>

                                    <select class="form-control" id="account" name="account" required>
                                        <option value="">Select Account</option>
                                    </select>
                                </div>
                            </div>
                        </div>

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
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center" width="15%">Date</th>
                                    <th class="text-center" width="35%">Particular</th>
                                    <th class="text-center" width="15%">Debit</th>
                                    <th class="text-center" width="15%">Credit</th>
                                    <th class="text-center" width="20">Balance</th>
                                </tr>

                                @foreach($result as $item)
                                    <tr>
                                        <td class="text-center">
                                            @if (!empty($item['date']))
                                                {{ date('d-m-Y',strtotime($item['date'])) }}
                                            @endif
                                        </td>
                                        <td >{{ $item['particular'] }}</td>
                                        <td class="text-center">৳ {{ number_format((double)$item['credit'],2) }}</td>
                                        <td class="text-center">৳ {{ number_format((double)$item['debit'],2) }}</td>

                                        <td class="text-center">৳ {{ number_format((double)$item['balance'],2) }}</td>
                                    </tr>
                                @endforeach

                              <tfoot>
                              <tr>

                                  <th colspan="1"></th>
                                  <th class="text-center">Total</th>
                                  <th class="text-center">৳ {{ number_format((double)$metaData['total_credit'],2) }}</th>
                                  <th class="text-center">৳ {{ number_format((double)$metaData['total_debit'],2) }}</th>

                                  <th class="text-center">৳ {{ number_format((double)$item['balance'] ,2)}}</th>
                              </tr>
                              </tfoot>
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
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

            var branchSelected = '{{ request()->get('branch') }}';
            var accountSelected = '{{ request()->get('account') }}';

            $('#bank').change(function () {
                var bankId = $(this).val();
                $('#branch').html('<option value="">Select Branch</option>');
                $('#account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (branchSelected == item.id)
                                $('#branch').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('#branch').trigger('change');
                    });
                }

                $('#branch').trigger('change');
            });

            $('#branch').change(function () {
                var branchId = $(this).val();
                $('#account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (accountSelected == item.id)
                                $('#account').append('<option value="'+item.id+'" selected>'+item.account_no+'</option>');
                            else
                                $('#account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });

            $('#bank').trigger('change');
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
