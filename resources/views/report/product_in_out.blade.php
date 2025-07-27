@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Product In Out Report
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
                    <form action="{{ route('report.product_in_out') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Product Model *</label>

                                    <select class="form-control select2" name="product_item_id" required>
                                        <option value=""> Select Product Model </option>
                                        @foreach ($productItems as $productItem)
                                            <option @if (request()->get('product_item_id')==$productItem->id) selected @endif value="{{ $productItem->id }}"> {{ $productItem->name }} </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Report type </label>

                                    <select class="form-control select2" name="type" required>
                                        <option @if (request()->get('type')==1) selected @endif value="1"> In </option>
                                        <option @if (request()->get('type')==2) selected @endif value="2"> Out </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start') }}" autocomplete="off" >
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end') }}" autocomplete="off" >
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> &nbsp;</label>

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
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>

                    <div id="prinarea">
                        <div class="col-xs-12">
                            @if (Auth::user()->company_branch_id == 2)
                                <img src="{{ asset('img/your_choice_plus.png') }}" style="margin-top: 10px; float:inherit">
                            @else
                                <img src="{{ asset('img/your_choice.png') }}" style="margin-top: 10px; float:inherit">
                            @endif
                        </div>
                        <div class="table-responsive">
                         <table id="table" class="table table-bordered table-striped">
                             <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center"> Code </th>
                                    <th class="text-center"> Type </th>
                                    <th class="text-center"> Quantity </th>
                                    <th class="text-center"> Unit Price </th>
                                    <th class="text-center"> Supplier </th>
                                    <th class="text-center">Purchase Order </th>
                                    <th class="text-center"> Customer </th>
                                    <th class="text-center">Sale Order </th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($results as $result)
                                   <tr>
                                        <td class="text-center">
                                            {{ $result->date->format('Y-m-d') }}
                                        </td>
                                        <td class="text-center">
                                            {{ $result->serial }}
                                        </td>
                                        <td class="text-center">
                                            {{ $result->type==1?'In':'Out' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $result->quantity }}
                                        </td>
                                        <td class="text-center">
                                            {{ $result->unit_price }}
                                        </td>

                                       <td class="text-center">
                                           {{ $result->purchaseOrder->supplier->name??'' }}
                                       </td>
                                       <td class="text-center">
                                           {{$result->purchaseOrder->order_no??''}}
                                       </td>
                                       <td class="text-center">
                                           {{ $result->saleOrder->customer->name??'' }}
                                       </td>
                                       <td class="text-center">
                                           {{$result->saleOrder->order_no??''}}
                                       </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th class="text-right" colspan="3">Total</th>
                                    <th class="text-center"> {{number_format($results->sum('quantity'),2)}}</th>
                                    <th class="text-center">৳ {{number_format($results->sum('unit_price'),2)}}</th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                </tr>
                             </tbody>
                         </table>
                                {{-- {{ $results->links() }} --}}
                        </div>

                     </div>

                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>

        $('.select2').select2();
        //Date picker
        $('#start, #end').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {

            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
