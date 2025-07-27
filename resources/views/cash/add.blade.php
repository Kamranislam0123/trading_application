@extends('layouts.app')

@section('title')
    Cash
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"> Cash Description </h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('cash') }}">
                    @csrf

                    <div class="box-body">
                        @if (empty($cash))
                            <div class="form-group {{ $errors->has('opening_balance') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label"> Opening Balance *</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Opening Balance"
                                        name="opening_balance" value="{{ old('opening_balance') }}">
                                    @error('opening_balance')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="form-group {{ $errors->has('amount') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label"> Balance *</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Balance"
                                        name="amount" value="{{ old('amount', $cash->amount) }}" disabled>
                                    @error('amount')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('opening_balance') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label"> Opening Balance *</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Opening Balance"
                                        name="opening_balance" value="{{ old('opening_balance', $cash->opening_balance) }}">
                                    @error('opening_balance')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary"> Save </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
