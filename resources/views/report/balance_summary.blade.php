@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Balance Summary
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Bank Details</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Bank Name</th>
                                        <th>Branch Name</th>
                                        <th>Account No.</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($bankAccounts as $account)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->bank->name }}</td>
                                            <td>{{ $account->branch->name }}</td>
                                            <td>{{ $account->account_no }}</td>
                                            <td class="text-right">৳ {{ number_format($account->balance * nbrCalculation(), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="3"></th>
                                            <th class="text-right">Total</th>
                                            <th class="text-right">৳ {{ number_format($bankAccounts->sum('balance') * nbrCalculation(), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Cash</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Cash</td>
                                        <td class="text-right">৳ {{ number_format($cash->amount * nbrCalculation(), 2) }}</td>
                                    </tr>
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">৳ {{ number_format($cash->amount * nbrCalculation(), 2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Mobile Banking</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Mobile Banking</td>
                                        <td class="text-right">৳ {{ number_format($mobile_banking->amount * nbrCalculation(), 2) }}</td>
                                    </tr>
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">৳ {{ number_format($mobile_banking->amount * nbrCalculation(), 2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Customer Payments </h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered " id="customer-payment-table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Total</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
                                            <th class="text-right">৳ {{ number_format($customerTotal * nbrCalculation(), 2) }}</th>
                                            <th class="text-right">৳ {{ number_format($customerTotalPaid * nbrCalculation(), 2) }}</th>
                                            <th class="text-right">৳ {{ number_format($customerTotalDue * nbrCalculation(), 2) }}</th>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Supplier Payment</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="supplier-payment-table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $supplier->name }}</td>
                                            <td>{{ $supplier->mobile }}</td>
                                            <td>৳ {{ number_format($supplier->total, 2) }}</td>
                                            <td>৳ {{ number_format($supplier->paid, 2) }}</td>
                                            <td>৳ {{ number_format($supplier->due, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Total</th>
                                        <th class="text-right">৳ {{ number_format($suppliers->sum('total'), 2) }}</th>
                                        <th class="text-right">৳ {{ number_format($suppliers->sum('paid'), 2) }}</th>
                                        <th class="text-right">৳ {{ number_format($suppliers->sum('due'), 2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Stock Summary</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="inventory-table">
                                    <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Product Item</th>
                                        <th>Warehouse</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Total Price</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($inventories as $inventory)
                                        <tr>
                                            <td>{{ $inventory->serial }}</td>
                                            <td>{{ $inventory->productItem->name??'' }}</td>
                                            <td>{{ $inventory->warehouse->name }}</td>
                                            <td>৳ {{ number_format($inventory->unit_price, 2) }}</td>
                                            <td>{{ $inventory->quantity }}</td>
                                            <td class="text-right">৳ {{ number_format($inventory->unit_price * $inventory->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="4"></th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">৳ {{ number_format($totalInventory[0]->total , 2) }}</th>

                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Summary</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="inventory-table">
                                    <thead>
                                    <tr>
                                        <th>Total Capital: ৳ {{ number_format(($bankAccounts->sum('balance') * nbrCalculation()) + ($cash->amount * nbrCalculation())+($mobile_banking->amount * nbrCalculation()) + ($customerTotalDue * nbrCalculation()) + $supplierSaleDue + $totalInventory[0]->total - $suppliers->sum('due'), 2) }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
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
            $('#customer-payment-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('client_payment.customer.datatable') }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                ],
                columnDefs: [
                    {
                        targets: 2,
                        className: 'text-right'
                    },
                    {
                        targets: 3,
                        className: 'text-right'
                    },
                    {
                        targets: 4,
                        className: 'text-right'
                    }
                ]
            });

            $('#client-payment-supplier-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('client_payment.supplier.datatable') }}',
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                ],
                columnDefs: [
                    {
                        targets: 2,
                        className: 'text-right'
                    },
                    {
                        targets: 3,
                        className: 'text-right'
                    },
                    {
                        targets: 4,
                        className: 'text-right'
                    }
                ]
            });

            $('#supplier-payment-table').DataTable({
                columnDefs: [
                    {
                        targets: 2,
                        className: 'text-right'
                    },
                    {
                        targets: 3,
                        className: 'text-right'
                    },
                    {
                        targets: 4,
                        className: 'text-right'
                    }
                ]
            });

            $('#inventory-table').DataTable({
                columnDefs: [
                    {
                        targets: 2,
                        className: 'text-right'
                    },
                    {
                        targets: 4,
                        className: 'text-right'
                    }
                ]
            });
        });
    </script>
@endsection
