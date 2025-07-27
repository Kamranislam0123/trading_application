<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <style>
        /* @page {
            @top-center {
                content: element(pageHeader);
            }
        } */
        #pageHeader{
            position: running(pageHeader);
        }

        table{
            border:1px solid black !important;
            margin-top:20px;
        }
        table th{
            border:1px solid black !important;
        }
        table td{
            border:1px solid black !important;
        }

        th{
            padding: 2px !important;
            text-align: center !important;
            font-weight: 300;
        }

        td{
            padding: 5px !important;
            text-align: center !important;
            font-size: 14px;
        }

        
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row first_page">
        <div class="col-xs-12">
            <header id="pageHeader" style="margin-bottom: 10px">
                <div class="row">
                    <div class="col-xs-3 col-xs-offset-1">
                        {{-- <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px"> --}}
                    </div>
            
                    <div class="col-xs-8 pull-right">
                        <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px; float:right">
                        <br>
            
                    </div>
                </div>
            </header>
        </div>
        <div class="col-xs-12">
            <div class="title text-center" style="margin-top: 10%;">
                <b> Proposal for {{ $proposal->title }} </b>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="text-center" style="margin-top: 15%;">
                <span style="font-weight: bold;"> Submitted to:</span> <br> <br>
                @if ($proposal->sub_customer_id)
                    <span style="font-weight: bold;">  {{ $proposal->subCustomer->name??'' }} </span> <br>
                    <b> Address: {{ $proposal->subCustomer->address??'' }}</b>
                @else 
                    <span style="font-weight: bold;">  {{ $proposal->customer->name??'' }} </span> <br>
                    <b> Address: {{ $proposal->customer->address??'' }}</b>
                @endif
                
            </div>
        </div>
        <div class="col-xs-12">
            <div class="submitted_by text-center" style="margin-top: 20%;">
                <span style="font-weight: bold;"> Submitted By:</span> <br><br>
                <span style="font-weight: bold;">  {{ $proposal->user->name??'' }} </span> <br> <br>
                <b>  
                    @if ($proposal->user->employee)
                        {{ $proposal->user->employee->designation->name??'' }},
                        {{ $proposal->user->employee->department->name??'' }}
                    @else 
                        {{-- Admin     --}}
                    @endif
                </b> <br>
                <span style="font-weight: bold;"> Bio-Access Tech Co,</span> <br>
                <b> Date: {{ $proposal->date->format('d/m/Y') }}</b>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="footer_content text-center" style="margin-top: 45%;">
                <hr>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span style="font-size: 12px;">
                            House# 9, road# 2/2-1b, banani, dhaka-1213, bangladesh. <br>
                            tel: 02-55040826, fax: +88-02-9564042, e-mail: kmh@bioaccessbd.com, web: www.bioaccessbd.com 
                        </span>
                        <br>
                        <span style="font-style: italic;font-size: 13px;">Software developed by 2A IT. Mobile: 01884697775</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <header id="pageHeader" style="margin-bottom: 10px">
                <div class="row">
                    <div class="col-xs-3 col-xs-offset-1">
                        {{-- <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px"> --}}
                    </div>
            
                    <div class="col-xs-8 pull-right">
                        <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px; float:right">
                        
                    </div>
                </div>
            </header>
        </div>
        <div class="col-xs-12">
            <div class="proposal_letter">
                <p>
                    <b>Ref: {{ $proposal->proposal_no }}</b> <br>
                    <b>Date: {{ $proposal->date->format('d F Y') }}</b> <br>
                    <b>To,</b> <br>
                    @if ($proposal->sub_customer_id)
                        <b>The {{ $proposal->subCustomer->name??'' }}</b> <br>
                        <b>{{ $proposal->subCustomer->address??'' }}</b> <br>
                    @else 
                        <b>The {{ $proposal->customer->name??'' }}</b> <br>
                        <b>{{ $proposal->customer->address??'' }}</b> <br>    
                    @endif
                    
                    Subject: Price Quotation for  {{ $proposal->title }}  <br> <br>
                    Dear Sir, <br>
                    <span style="font-size: 14px;"> 
                        Greeting From Bio-Access Tech Co. ! Let us introduce ourselves and then lead you to the proposed solution
                    and pricing. <br> <br>
                    We incepted in year 2005 as a pioneer biometric solution provider in Bangladesh. Over the period of times, the
                    company diversified in vertical and horizontal line such as Payroll Management Solution, Customized
                    Attendance Management Solution, Video Surveillance, Intrusion, Home Automation etc. We are serving these
                    solutions over 300+ corporate customers which includes 13+ banks, leading manufacturers in apparel industries,
                    pharmaceuticals, medical colleges, schools, hospitality industries and in real estates.
                    We are representing world renowned brand like Suprema, ZKTeco, Nitgen, Hisharp, Axxonsoft, IRIS ID,
                    Futronic, etc. The resources of our CS Centre (Customer Service Centre) not only include experienced hardware
                    team but also software development and API/SDK integration personnel. <br><br>
                    By the grace of Allah we performed work that value more than 4 core in Dutch Bangla Bank; more than 800+
                    terminals in in Ha-Meem group, 500+ terminals in Standard group, 1000+ cameras in Bengal group, IP PA
                    system for more 300,000 sft areas and axxon’s mind-blowing video analytics in a pharmaceutical factory. We
                    are way to put our step InshaAllah in Home automation with LOXONE putting Alexa and Google assistance far
                    behind in terms of scalability, design and programming. <br><br>
                    Our core value, “we deal with honesty and treat everyone with respect” will touch your heart and give you more
                    credibility on us. Please provide us the scope to fulfil your heart with desired value for money that we create
                    through our product, workmanship and service. <br> <br>
                    
                    </span>
                    Sincerely yours <br>
                    Thanking you with best regards <br> 
                    <b> 
                        {{ $proposal->user->name??'' }}
                    </b> <br>
                    <b>
                        @if ($proposal->user->employee)
                            {{-- {{ $proposal->user->employee->name??'' }} <br> --}}
                            ({{ $proposal->user->employee->designation->name??'' }}) <br>
                            Cell: {{ $proposal->user->employee->emergency_contact??'' }}
                        @else 
                            {{ $proposal->user->employee->name??'' }}
                            (Admin) <br>
                            Cell: 
                        @endif 
                    </b> 
                        
                </p>


            </div>
        </div>
        <div class="col-xs-12">
            <div class="footer_content text-center" style="margin-top: 15%;">
                <hr>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span style="font-size: 12px;">
                            House# 9, road# 2/2-1b, banani, dhaka-1213, bangladesh. <br>
                            tel: 02-55040826, fax: +88-02-9564042, e-mail: kmh@bioaccessbd.com, web: www.bioaccessbd.com 
                        </span>
                        <br>
                        <span style="font-style: italic;font-size: 13px;">Software developed by 2A IT. Mobile: 01884697775</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px">
        <span style="border: 1px solid #000;">
            <div class="col-xs-12">
                <header id="pageHeader">
                    <div class="row">
                        <div class="col-xs-3 col-xs-offset-1">
                            
                        </div>
                
                        <div class="col-xs-8 pull-right">
                            <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px; float:right">
                            
                
                        </div>
                    </div>
                </header>
            </div>
            <div class="col-xs-12">
                <div class="" style="border: 1px">
                    <div class="row col-xs-12">
                        <div class="" style="width: 50%;float:left">
                            <b>
                                Quotation for <br>
                                @if ($proposal->sub_customer_id)
                                    {{ $proposal->subCustomer->name??'' }} <br>
                                    Address: {{ $proposal->subCustomer->address??'' }} <br>
                                    Mobile: {{ $proposal->subCustomer->mobile??'' }} <br>
                                @else 
                                    {{ $proposal->customer->name??'' }} <br>
                                    Address: {{ $proposal->customer->address??'' }} <br>
                                    Mobile: {{ $proposal->customer->mobile??'' }} <br>    
                                @endif
                                
                            </b>
                        </div>
                        <div class="" style="width: 45%;">
                            <b>
                                Quotation No: {{ $proposal->proposal_no }} <br>
                                Date: {{ $proposal->date->format('d/m/Y') }} <br>
                            </b>
                        </div>
                    </div>
                    <div class="quotation_img">
                        <img src="{{ asset('img/quotation.jpg') }}" width="100" alt="Quotation" style="float: right;">
                    </div>
                    @if(count($proposal->products) > 0)
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Model</th>
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
                        <div class="col-xs-offset-6 col-xs-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Product Sub Total</th>
                                    <td>{{ number_format($proposal->sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Installation Charge </th>
                                    <td>{{ number_format($proposal->installation_charge, 2) }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Product Vat ({{ $proposal->vat_percentage }}%)</th>
                                    <td>{{ number_format($proposal->vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Service Vat ({{ $proposal->service_vat_percentage }}%)</th>
                                    <td>{{ number_format($proposal->service_vat, 2) }}</td>
                                </tr> --}}
                                <tr>
                                    <th> TAX </th>
                                    <td>{{ number_format($proposal->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> VAT </th>
                                    <td>{{ number_format($proposal->vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Discount</th>
                                    <td>{{ number_format($proposal->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>{{ number_format($proposal->total, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            Total: {{ $proposal->amount_in_word }} <br>
                        </div>
                    </div>
                    <div class="row terms_condition_box">
                        <div class="col-xs-8">
                            <div style="border: 1px solid #000; padding:10px;page-break-inside: avoid;">
                                <p style="font-weight: bold;">
                                    Terms and Conditions:
                                </p>
                                <b>Payment :</b> {{ $terms_condition->payment_text }} <br>
                                <b>Delivery Lead Time :</b> {{ $terms_condition->delevery_duration }} <br>
                                <b>Quotation Validity :</b> {{ $terms_condition->quotation_validity }} <br>
                                <b>Tax & Vat :</b> {{ $terms_condition->tax }} <br>
                                <b>Warranty :</b> {{ $terms_condition->warranty }} <br>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            
                        </div>
                    </div>
                    <div class="row" style="page-break-after: always">
                        <div class="col-xs-6 col-6" style="width: 40%;float:left"> <br>
                            Prepared By : {{ $proposal->user->name??'' }} <br>
                            @if ($proposal->user->employee)
                                Contact: {{ $proposal->user->employee->emergency_contact??'' }}
                            @else 
                                Contact: 
                            @endif
                        </div>
                        <div class="col-xs-6 col-6 pull-right" style="width: 40%;"> 
                            <img class="" style="float: right" src="{{ asset('img/proposal_logo.png') }}" width="150" alt="Proposal Logo">
                        </div>
                    </div> 
                </div>
            </div>
        </span>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <header id="pageHeader" style="margin-bottom: 10px">
                <div class="row">
                    <div class="col-xs-3 col-xs-offset-1">
                        
                    </div>
            
                    <div class="col-xs-8 pull-right">
                        <img src="{{ asset('img/logo.png') }}" width="200px" style="margin-top: 10px; float:right">
                        
                    </div>
                </div>
            </header>
        </div>
        <div class="col-xs-12">
            <div class="">
                 
                <div class="row">
        
                    <div class="col-xs-12">
                        <div class="content text-center">
                            <h3>
                                Our Client List
                            </h3> 
                        </div>
                    </div>
                    @foreach (App\Model\Customer::all() as $customer)
                        <div class="col-xs-3">
                            <div class="content">
                                {{ $customer->name }}
                            </div>
                        </div>
                    @endforeach
                    @foreach (App\Model\SubCustomer::all() as $subCustomer)
                        <div class="col-xs-3">
                            <div class="content">
                                {{ $subCustomer->name }}
                            </div>
                        </div>
                    @endforeach
                    
                </div> 
            </div>
        </div>
        <div class="col-xs-12">
            <div class="footer_content text-center" style="margin-top: 5%;">
                <hr>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <span style="font-size: 12px;">
                            House# 9, road# 2/2-1b, banani, dhaka-1213, bangladesh. <br>
                            tel: 02-55040826, fax: +88-02-9564042, e-mail: kmh@bioaccessbd.com, web: www.bioaccessbd.com 
                        </span>
                        <br>
                        <span style="font-style: italic;font-size: 13px;">Software developed by 2A IT. Mobile: 01884697775</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>



</body>
</html>
