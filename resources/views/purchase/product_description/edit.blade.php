@extends('layouts.app')

@section('title')
    Description Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Description Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal"  method="POST" action="{{ route('description.update', ['description' => $description->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('product_item') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Product Item *</label>

                            <div class="col-sm-10">
                                <select class="form-control item" name="product_item">
                                    <option value="">Select Product Item</option>

                                    @foreach($productItems as $productItem)
                                        <option value="{{ $productItem->id }}" {{ empty(old('product_item')) ? ($errors->has('product_item') ? '' : ($description->product_item_id == $productItem->id ? 'selected' : '')) :
                                            (old('product_item') == $productItem->id ? 'selected' : '') }}>{{ $productItem->name }}</option>
                                    @endforeach
                                </select>

                                @error('product_item')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Select Product *</label>

                            <div class="col-sm-10">
                                <select class="form-control product" name="product" id="product">
                                    <option value="">Select Product</option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('description') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Description *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Description"
                                       name="description" value="{{ empty(old('description')) ? ($errors->has('description') ? '' : $description->description) : old('description') }}">

                                @error('description')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Status *</label>

                            <div class="col-sm-10">

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($description->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($description->status == '0' ? 'checked' : '')) :
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
@section('script')
    <script>
        $('.item').change(function(){
            var item = $('.item').val();
            var index = $(".item").index(this);
            $.ajax({
                method: "GET",
                url: "{{ route('get_products') }}",
                data: {productItemId: item}
            }).done(function (response) {
                var selected = $('.product:eq('+index+')').data('selected');

                $.each(response, function( index, item ) {
                    if (selected == item.id)
                        $('#product').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                    else
                        $('#product').append('<option value="'+item.id+'">'+item.name+'</option>');
                });
            });

        })
    </script>
@endsection
