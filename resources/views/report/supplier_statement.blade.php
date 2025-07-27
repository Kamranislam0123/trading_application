@extends('layouts.app')



@section('title')
    Supplier Report
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
                    <form action="{{ route('report.supplier_statement') }}" method="GET">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> Supplier </label>

                                    <select class="form-control select2" name="supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach($allSuppliers as $allSupplier)
                                            <option value="{{ $allSupplier->id }}" {{ old('supplier') == $allSupplier->id ? 'selected' : '' }}>{{ $allSupplier->name }} - {{$allSupplier->address}} - {{$allSupplier->mobile}}</option>
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

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><br>

                    <div id="prinarea">
                        <div class="row">
                            <div class="col-xs-12">
                                @if (Auth::user()->company_branch_id == 2)
                                    <img src="{{ asset('img/your_choice_plus.png') }}"style="margin-top: 10px; float:inherit">
                                @else
                                    <img src="{{ asset('img/your_choice.png') }}"style="margin-top: 10px; float:inherit">
                                @endif
                            </div>
                        </div>
                        <div class="logo-pad">
                            <img src="{{ asset('img/logo.png') }}" style="position: absolute;opacity: 0.1;height: 553px;width: 650px;margin-top: 130px;margin-left: 65px;">
                        </div>
                        <div class="table-responsive">
                         <table id="table" class="table table-bordered table-striped">
                             <thead>
                             <tr>
                                 <th class="text-center">Sl</th>
                                 <th class="text-center">Name</th>
                                 <th class="text-center">Total</th>
                                 <th class="text-center">Opening Due</th>
                                 <th class="text-center">Paid</th>
                                 <th class="text-center">Due</th>
                             </tr>
                             </thead>
                             <tbody>

                             @foreach($suppliers as $supplier)
                                 <tr>
                                     <td class="text-center">{{$loop->iteration}}</td>
                                     <td >{{$supplier->name}}</td>
                                     <td class="text-center">৳ {{number_format($supplier->total,2)}}</td>
                                     <td class="text-center">৳ {{number_format($supplier->opening_due,2)}}</td>
                                     <td class="text-center">৳ {{number_format($supplier->paid,2)}}</td>
                                     <td class="text-center">৳ {{number_format($supplier->due,2)}}</td>
                                 </tr>
                             @endforeach

                             </tbody>

                             <tfoot>
                             <tr>
                                 <th class="text-center" colspan="2">Total</th>
                                 <th class="text-center">৳ {{number_format($suppliers->sum('total'),2)}}</th>
                                 <th class="text-center">৳ {{number_format($suppliers->sum('opening_due'),2)}}</th>
                                 <th class="text-center">৳ {{number_format($suppliers->sum('paid'),2)}}</th>
                                 <th class="text-center">৳ {{number_format($suppliers->sum('due'),2)}}</th>
                             </tr>
                             </tfoot>

                         </table>
                        </div>

                     </div>

                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>


        var APP_URL = '{!! url()->full()  !!}';
        function getprint(prinarea) {

            $('body').html($('#'+prinarea).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
