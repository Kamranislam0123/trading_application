@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Proposal Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            {{-- <a target="_blank" href="{{ route('sale_receipt.chalan.print', ['order' => $proposal->id]) }}" class="btn btn-primary"> Challan </a> --}}
                            <a target="_blank" href="{{ route('proposal.print', ['proposal' => $proposal->id]) }}" class="btn btn-primary"> Print </a>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Proposal No.</th>
                                    <td>{{ $proposal->proposal_no }}</td>
                                </tr>
                                <tr>
                                    <th>Proposal Date</th>
                                    <td>{{ $proposal->date->format('j F, Y') }}</td>
                                </tr>
                                {{-- <tr>
                                    <th> P.O No: </th>
                                    <td>{{ $proposal->received_by }}</td>
                                </tr> --}}
                                {{-- <tr>
                                    <th>Next Payment Date</th>
                                    <td>{{ $proposal->next_payment ? $proposal->next_payment->format('j F, Y') : '' }}</td>
                                </tr> --}}
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Customer Info</th>
                                </tr>

                                @if ($proposal->sub_customer_id)
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $proposal->subCustomer->name??'' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile No.</th>
                                        <td>{{ $proposal->subCustomer->mobile_no??'' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $proposal->subCustomer->address??'' }}</td>
                                    </tr>
                                @else 
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $proposal->customer->name??'' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile No.</th>
                                        <td>{{ $proposal->customer->mobile_no??'' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $proposal->customer->address??'' }}</td>
                                    </tr>    
                                @endif
                                
                            </table>
                        </div>
                    </div>

                    @if(count($proposal->products) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Product Model</th>
                                        {{-- <th>Product Serial</th> --}}
                                        <th>Description </th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($proposal->product_items as $key => $product)
                                            <tr>
                                                <td class="text-center">{{ ++$key }}</td>
                                                <td>{{ $product->productItem->name??'' }}</td>
                                                <td>{{ $product->productItem->description??'' }}</td>
                                                <td class="text-center">
                                                    {{ $product->item_products($product->proposal_id, $product->product_item_id)->sum('quantity') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $product->productItem->unit->name??'' }}
                                                </td>
                                                <td>
                                                    {{ $product->unit_price }}
                                                </td>
                                                <td width="100">
                                                    {{ $product->item_products($product->proposal_id, $product->product_item_id)->sum('total') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-offset-8 col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Product Sub Total</th>
                                    <td>৳{{ number_format($proposal->sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Installation Charge </th>
                                    <td>৳{{ number_format($proposal->installation_charge, 2) }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Product Vat ({{ $proposal->vat_percentage }}%)</th>
                                    <td>৳{{ number_format($proposal->vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Service Vat ({{ $proposal->service_vat_percentage }}%)</th>
                                    <td>৳{{ number_format($proposal->service_vat, 2) }}</td>
                                </tr> --}}
                                <tr>
                                    <th> TAX  </th>
                                    <td>৳{{ number_format($proposal->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> VAT </th>
                                    <td>৳{{ number_format($proposal->vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Discount</th>
                                    <td>৳{{ number_format($proposal->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>৳{{ number_format($proposal->total, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });
        });
    </script>
@endsection
