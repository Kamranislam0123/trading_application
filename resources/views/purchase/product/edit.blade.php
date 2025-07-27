@extends('layouts.app')

@section('title')
    Product Serial Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Product Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ route('product.edit', ['product' => $product->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('product_item') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Product Model *</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="product_item">
                                    <option value="{{ $product->product_item_id }}"> {{ $product->productItem->name??'' }} </option>

                                    {{-- @foreach($productItems as $productItem)
                                        <option value="{{ $productItem->id }}" {{ old('product_item') == $productItem->id ? 'selected' : '' }}>{{ $productItem->name }}</option>
                                    @endforeach --}}
                                </select>

                                @error('product_item')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Product Serial *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Serial"
                                       name="name" value="{{ old('name', $product->name) }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('unit') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Product Unit</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="unit">
                                    <option value=""> Select Unit </option>

                                    @foreach(App\Model\Unit::where('status',1)->get() as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>

                                @error('unit')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group {{ $errors->has('catalog') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Product Catalog</label>

                            <div class="col-sm-10">
                                <textarea name="catalog" id="catalog" rows="3" class="form-control">{!! old('catalog',$product->catalog) !!}</textarea>

                                @error('catalog')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Status *</label>

                            <div class="col-sm-10">

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($product->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($product->status == '0' ? 'checked' : '')) :
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
