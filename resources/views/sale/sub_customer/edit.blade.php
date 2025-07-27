@extends('layouts.app')

@section('title')
    Sub Customer Edit
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Sub Customer Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('sub_customer.edit', ['subCustomer' => $subCustomer->id]) }}">
                    @csrf

                    <div class="box-body">

                        <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label"> Customer *</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="customer" id="customer" required>
                                    <option value=""> Select Customer </option>
                                    @foreach($customers as $customer)
                                        <option value="{{$customer->id}}" {{ old('customer',$subCustomer->customer_id) == $customer->id  ? 'selected' : '' }}>{{$customer->name}}</option>
                                    @endforeach
                                </select>

                                @error('customer')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $subCustomer->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mobile No. *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile No."
                                       name="mobile_no" value="{{ empty(old('mobile_no')) ? ($errors->has('mobile_no') ? '' : $subCustomer->mobile_no) : old('mobile_no') }}">

                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Address</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Address"
                                       name="address" value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $subCustomer->address) : old('address') }}">

                                @error('address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('sub_customer_old_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Sub Customer Old id</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter sub_customer_old_id"
                                       name="sub_customer_old_id" value="{{ old('sub_customer_old_id', $subCustomer->sub_customer_old_id) }}">

                                @error('sub_customer_old_id')
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

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            // Initialize Select2 Elements
            $('.select2').select2();
        });
    </script>
@endsection