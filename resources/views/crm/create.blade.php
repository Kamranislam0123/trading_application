@extends('layouts.app')

@section('title')
     Add Client Order
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Client Order Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('marketing.add') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="box-body">
                        {{-- <div class="form-group {{ $errors->has('marketing') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Marketing Name *</label>

                            <div class="col-sm-10">
                                <select name="marketing" class="form-control">
                                    <option value="">Select Marketing Name</option>
                                    <option value="1" {{ old('marketing') == '1' ? 'selected' : '' }} >Zulfikker</option>
                                    <option value="2" {{ old('marketing') == '2' ? 'selected' : '' }}>Jyoti</option>
                                </select>

                                @error('marketing')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Client Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('company') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Company Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Company name"
                                       name="company" value="{{ old('company') }}">

                                @error('company')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('mobile') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mobile Number *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile Number"
                                       name="mobile" value="{{ old('mobile') }}">

                                @error('mobile')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('client_source_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Source  *</label>

                            <div class="col-sm-10">
                                <select name="client_source_id" class="form-control">
                                    <option value="">Select Source</option>
                                    @foreach ($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('client_source_id') == $source->id ? 'selected' : '' }}> {{ $source->name }} </option>
                                    @endforeach
                                    
                                </select>

                                @error('client_source_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('client_service_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Service Type  *</label>

                            <div class="col-sm-10">
                                <select name="client_service_id" class="form-control">
                                    <option value="">Select Service Type</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" {{ old('client_service_id') == $service->id ? 'selected' : '' }}> {{ $service->name }} </option>
                                    @endforeach
                                    
                                </select>

                                @error('client_service_id')
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
                        <div class="form-group {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Status  *</label>

                            <div class="col-sm-10">
                                <select name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Low</option>
                                    <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Medium </option>
                                    <option value="3" {{ old('status') == '3' ? 'selected' : '' }}>High</option>
                                    <option value="4" {{ old('status') == '4' ? 'selected' : '' }}>Work Order</option>
                                    <option value="5" {{ old('status') == '5' ? 'selected' : '' }}>Negative</option>
                                </select>

                                @error('status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('amount') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Propose Amount </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('amount') }}" name="amount" >

                                @error('amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('contact_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Contact Date </label>

                            <div class="col-sm-10">
                                <input type="date" class="form-control" value="{{ old('contact_date') }}" name="contact_date" >

                                @error('contact_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('next_contact_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Next Contact Date </label>

                            <div class="col-sm-10">
                                <input type="date" class="form-control" value="{{ old('next_contact_date') }}" name="next_contact_date" >

                                @error('next_contact_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('comments') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Comments </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('comments') }}" name="comments" >

                                @error('comments')
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
