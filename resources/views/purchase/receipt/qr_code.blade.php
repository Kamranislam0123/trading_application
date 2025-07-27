@extends('layouts.app')

@section('title')
    Bar Code
@endsection
@section('style')
    <style>
        ul.bar-code {
            list-style: none;
        }

        ul.bar-code li {
            display: inline-block;
            margin-right: 0px;
        }
        .bar-code-area {
            text-align: left;
        }
        li.bar-code-size {
            font-size: 8px;
            height: 25mm;
            width: 38mm;
            border: 1px solid #000;
            padding: 0;
            margin: 0;
            margin-bottom: 9px;
            text-align: center;
            overflow: hidden;
        }

        li.bar-code-size > p {
            margin: 0;
        }
        ul.bar-code li {
            display: inherit;
            margin-right: 0px;
        }
        .btn-primary.focus, .btn-primary:focus {
            color: #fff;
            background-color: #b411aa;
            border-color: #281240;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
{{--                    <div class="row">--}}
{{--                        <div class="col-md-12 text-right">--}}
{{--                            <a target="_blank" href="{{ route('purchase_receipt.qr_code_print', ['order' => $order->id]) }}" class="btn btn-primary">Print</a>--}}

{{--                            <hr>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bar-code-area">


                                <ul class="bar-code">
                                    @foreach($order->products  as $product)
                                            <div>
                                                <a  href="{{ route('purchase_receipt.qr_code_single_print', ['order' => $product->id]) }}" class="btn btn-primary" target="_blank">Print</a>
                                                <li class="bar-code-size">

                                                    <p>{{ $product->productItem->name }} - {{ $product->productCategory->name }}</p>
                                                    {!! DNS1D::getBarcodeSVG($product->serial_no, 'C93',1.2,50); !!}
                                                    <p>Price: {{  number_format($product->selling_price,2) }}</p>
                                                </li>
                                            </div>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

@endsection
