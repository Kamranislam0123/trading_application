@extends('layouts.app')

@section('title')
    Customer Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Customer Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('customer.edit', ['customer' => $customer->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $customer->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mobile No. *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile No."
                                       name="mobile_no" value="{{ empty(old('mobile_no')) ? ($errors->has('mobile_no') ? '' : $customer->mobile_no) : old('mobile_no') }}">

                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Address</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Address"
                                       name="address" value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $customer->address) : old('address') }}">

                                @error('address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('employee_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="employee_id" id="employee_id">
                                    <option value="">Select Employee (Optional)</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ (empty(old('employee_id')) ? ($errors->has('employee_id') ? '' : ($customer->employee_id == $employee->id ? 'selected' : '')) : (old('employee_id') == $employee->id ? 'selected' : '')) }}>{{ $employee->name }}</option>
                                    @endforeach
                                </select>

                                @error('employee_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('opening_due') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label"> Opening Due</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter opening due"
                                       name="opening_due" value="{{ old('opening_due', $customer->opening_due) }}">

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
                                        <input type="radio" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($customer->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($customer->status == '0' ? 'checked' : '')) :
                                            (old('status') == '0' ? 'checked' : '') }}>
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
