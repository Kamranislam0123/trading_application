@extends('layouts.app')

@section('title')
    Supplier Add
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Supplier Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('supplier.add') }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

{{--                        <div class="form-group {{ $errors->has('owner_name') ? 'has-error' :'' }}">--}}
{{--                            <label class="col-sm-2 control-label">Owner Name</label>--}}

{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" class="form-control" placeholder="Enter Owner Name"--}}
{{--                                       name="owner_name" value="{{ old('owner_name') }}">--}}

{{--                                @error('owner_name')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mobile No. *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile No."
                                       name="mobile_no" value="{{ old('mobile_no') }}">

                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

{{--                        <div class="form-group {{ $errors->has('alternative_mobile_no') ? 'has-error' :'' }}">--}}
{{--                            <label class="col-sm-2 control-label">Alternative Mobile No.</label>--}}

{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" class="form-control" placeholder="Enter Alternative Mobile No."--}}
{{--                                       name="alternative_mobile_no" value="{{ old('alternative_mobile_no') }}">--}}

{{--                                @error('alternative_mobile_no')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Email</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Email"
                                       name="email" value="{{ old('email') }}">

                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Address *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Address"
                                       name="address" value="{{ old('address') }}">

                                @error('address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('opening_due') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label"> Opening due *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter opening_due"
                                       name="opening_due" value="{{ old('opening_due', 0) }}">

                                @error('opening_due')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Status *</label>

                            <div class="col-sm-10">

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                        Inactive
                                    </label>
                                </div>

                                @error('status')
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
