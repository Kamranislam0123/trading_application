@extends('layouts.app')

@section('title')
  Price Without Stock Report
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
                    <form action="{{ route('report.price.without.stock') }}">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Product</label>

                                    <select class="form-control" name="product" required>
                                        <option value="all">All Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ request()->get('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
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
    @isset($inventories)
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>

                    <div id="prinarea">
                        <div style="padding:10px; width:100%; text-align:center;">
                            <p style="font-family: 'Times New Roman'; font-size: 33px; font-style: italic; margin: 0px; font-weight: bold; text-align:center">
                                {{ config('app.name', 'Your Choice') }}<br>

                            <h5 style="text-align:center"><strong>IMPORTER &amp; WHOLESALER OF FOREIGN SHOES</strong></h5>

                            </p>
                            <p style="margin: 0px; font-size: 16px; text-align:center">
                                Shop# 20-21, Fubaria Super Market-1 (1st Floor)Dhaka-1000<br>
                                Phone : +8802223381027,, Mobile : 01591-148251(MANAGER)<br>
                                EMAIL:YOURCHOICE940@YAHOO.COM<br>
                                HELPLINE: IT DEPARTMENT,,,,MD.PORAN BHUYAIN<br>
                                MOBILE:01985-511918
                            </p>
                            <h4>Price Without Stock Report</h4>
                            <h5>Stock Report upto {{date('d-m-Y')}}</h5>
                        </div>
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Product Item</th>
                                    <th class="text-center">Warehouse</th>
                                    <th class="text-center">Stock (PCS)</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($inventories as $inventory)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}</td>
                                    <td >{{$inventory->productItem->name}}</td>
                                    <td >{{$inventory->warehouse->name}}</td>
                                    <td class="text-right">{{$inventory->quantity}}</td>
                                </tr>
                            @endforeach

                            </tbody>

                            <tfoot>
                            <tr>
                                <th class="text-right" colspan="3">Total= </th>
                                <th class="text-right" >{{number_format($inventories->sum('quantity',2))}}</th>
                            </tr>
                            </tfoot>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
    @endisset
@endsection

@section('script')
    <script>
        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
