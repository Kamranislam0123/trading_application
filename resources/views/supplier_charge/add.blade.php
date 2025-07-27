@extends('layouts.app')

@section('title')
    Supplier Charge Add
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Supplier Charge Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('supplier_charge.add') }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('supplier') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Supplier *</label>

                            <div class="col-sm-10">
                                <select name="supplier" class="form-control">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}" {{old("supplier"==$supplier->id?"selected":'')}}>{{$supplier->name}}</option>
                                    @endforeach
                                </select>

                                @error('supplier')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('charge') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Charge *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Charge Amount"
                                       name="charge" value="{{ old('charge') }}">

                                @error('charge')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Description *</label>

                            <div class="col-sm-10">
                                <textarea name="description" class="form-control" role="3">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Date *</label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="date" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
                                </div>
                                @error('date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section("script")
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $('#date, #next-payment-date').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
