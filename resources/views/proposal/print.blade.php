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
        @page {
            @top-center {
                content: element(pageHeader);
            }
        }
        #pageHeader{
            position: running(pageHeader);
        }

        table.table-bordered{
            border:1px solid black !important;
            margin-top:20px;
        }
        table.table-bordered th{
            border:1px solid black !important;
        }
        table.table-bordered td{
            border:1px solid black !important;
        }

        .product-table th, .table-summary th {
            padding: 2px !important;
            text-align: center !important;
        }

        .product-table td, .table-summary td {
            padding: 2px !important;
            text-align: center !important;
        }

        @media screen {
            div.divFooter {
                display: none;
            }
        }
        @media print {
            div.divFooter {
                position: fixed;
                bottom: 0;
            }
        }
        @media print {
            body,p{
                font-size: 16px;
            }
            div.first_page, div.second_page, div.product_page, .terms_condition_box{
                page-break-inside: avoid;
            }
            div.first_page .title {
                margin-top: 10%;
            }
            div.first_page .submitted_to {
                margin-top: 15%;
            }
            div.first_page .submitted_by {
                margin-top: 30%;
            }
            div.first_page .footer_content {
                margin-top: 25%;
            }
            div.second_page {
                font-size: 12px;;
            }
            div.second_page .footer_content {
                margin-top: 5%;
            }
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
            <div class="title text-center">
                <b> &#147 Proposal for {{ $proposal->title }} &#148</b>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="submitted_to text-center">
                <b> Submitted to:</b> <br><br>
                <b> &#147 {{ $proposal->customer->name??'' }} &#148</b> <br>
                <b> Address: {{ $proposal->customer->address??'' }}</b>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="submitted_by text-center">
                <b> Submitted By:</b> <br><br>
                <b> &#147 {{ $proposal->user->name??'' }} &#148</b> <br>
                <b>  
                    @if ($proposal->user->employee)
                        {{ $proposal->user->employee->designation->name??'' }},
                        {{ $proposal->user->employee->department->name??'' }}
                    @else 
                        Admin    
                    @endif
                </b> <br>
                <b> AT International,</b> <br>
                <b> Date: {{ $proposal->date->format('d/m/Y') }}</b>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="footer_content text-center">
                <hr>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        Software developed by 2A IT. Mobile: 01884697775
                        <br>
                        House# 9, road# 2/2-1b, banani, dhaka-1213, bangladesh. <br>
                        tel: 02-55040826, fax: +88-02-9564042, e-mail: kmh@bioaccessbd.com, web: www.bioaccessbd.com 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row second_page">
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
            <div class="content">
                <p>
                    <b>Ref: {{ $proposal->proposal_no }}</b> <br>
                    <b>Date: {{ $proposal->date->format('d F Y') }}</b> <br>
                    <b>To,</b> <br>
                    <b>The {{ $proposal->customer->name??'' }}</b> <br>
                    <b>{{ $proposal->customer->address??'' }}</b> <br>
                    Subject: Price Quotation for &#147 Facial Recognition &#148 <br> <br>
                    Dear Sir, <br>
                    Greeting From AT International ! Let us introduce ourselves and then lead you to the proposed solution
                    and pricing. <br>
                    We incepted in year 2005 as a pioneer biometric solution provider in Bangladesh. Over the period of times, the
                    company diversified in vertical and horizontal line such as Payroll Management Solution, Customized
                    Attendance Management Solution, Video Surveillance, Intrusion, Home Automation etc. We are serving these
                    solutions over 300+ corporate customers which includes 13+ banks, leading manufacturers in apparel industries,
                    pharmaceuticals, medical colleges, schools, hospitality industries and in real estates.
                    We are representing world renowned brand like Suprema, ZKTeco, Nitgen, Hisharp, Axxonsoft, IRIS ID,
                    Futronic, etc. The resources of our CS Centre (Customer Service Centre) not only include experienced hardware
                    team but also software development and API/SDK integration personnel.
                    By the grace of Allah we performed work that value more than 4 core in Dutch Bangla Bank; more than 800+
                    terminals in in Ha-Meem group, 500+ terminals in Standard group, 1000+ cameras in Bengal group, IP PA
                    system for more 300,000 sft areas and axxon’s mind-blowing video analytics in a pharmaceutical factory. We
                    are way to put our step InshaAllah in Home automation with LOXONE putting Alexa and Google assistance far
                    behind in terms of scalability, design and programming. <br>
                    Our core value, “we deal with honesty and treat everyone with respect” will touch your heart and give you more
                    credibility on us. Please provide us the scope to fulfil your heart with desired value for money that we create
                    through our product, workmanship and service. <br> <br>
                    Sincerely yours <br>
                    Thanking you with best regards <br> <br>

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
                    </b> <br>
                        
                </p>


            </div>
        </div>
        <div class="col-xs-12">
            <div class="footer_content text-center">
                <hr>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        Software developed by 2A IT. Mobile: 01884697775
                        <br>
                        House# 9, road# 2/2-1b, banani, dhaka-1213, bangladesh. <br>
                        tel: 02-55040826, fax: +88-02-9564042, e-mail: kmh@bioaccessbd.com, web: www.bioaccessbd.com 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row product_page">
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
            <div class="content">
                <div class="row">
                    <div class="col-xs-6">
                        <p>
                            <b>
                                Quotation for <br>
                                {{ $proposal->customer->name??'' }} <br>
                                Address: {{ $proposal->customer->address??'' }} <br>
                                Mobile: {{ $proposal->customer->mobile??'' }} <br>
                            </b>
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p>
                            <b>
                                Quotation No: {{ $proposal->proposal_no }} <br>
                                Date: {{ $proposal->date->format('d/m/Y') }} <br>
                            </b>
                        </p>
                    </div>
                </div>
                <br><br>
                @if(count($proposal->products) > 0)
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Description </th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($proposal->products as $key => $product)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $product->productItem->description??'' }}</td>
                                            <td>
                                                {{ $product->quantity }} 
                                                {{-- @isset($product->product->unit->name)
                                                    ({{ $product->product->unit->name }})
                                                @endisset --}}
                                                
                                            </td>
                                            <td>
                                                {{ $product->productItem->unit->name??'' }}
                                            </td>
                                            <td>
                                                {{ $product->unit_price }}
                                            </td>
                                            <td width="100">
                                                ৳ {{ $product->total }}
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
                                <th> TAX </th>
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
                <div class="row">
                    <div class="col-xs-12">
                        Total: {{ $proposal->amount_in_word }} <br>
                    </div>
                </div>
                <div class="row terms_condition_box">
                    <div class="col-xs-8" style="border: 1px solid #000; padding:10px;">
                        <b>Terms and Conditions:</b> <br>
                        <b>Payment :</b> 75% Advance Along with the work order
                        25% after delivery and Installation. <br>
                        <b>Delivery Lead Time :</b> 3 Weeks <br>
                        <b>Quotation Validity :</b> 30 days <br>
                        <b>Vat & Tax :</b> Included <br>
                        <b>Warranty :</b> One Year of Service Parts and Service <br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-6"> <br>
                        Prepared By : {{ $proposal->user->name??'' }} <br>
                        @if ($proposal->user->employee)
                            Contact: {{ $proposal->user->employee->emergency_contact??'' }}
                        @else 
                            Contact: 
                        @endif
                    </div>
                    <div class="col-xs-6 col-6"> 
                        
                        <img class="pull-right" src="{{ asset('img/proposal_logo.png') }}" width="150" alt="Proposal Logo">
                    </div>
                </div>    
            </div>
        </div>
        <div class="col-xs-12">
            <div class="footer_content text-center">
                <hr>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        Software developed by 2A IT. Mobile: 01884697775
                        <br>
                        House# 9, road# 2/2-1b, banani, dhaka-1213, bangladesh. <br>
                        tel: 02-55040826, fax: +88-02-9564042, e-mail: kmh@bioaccessbd.com, web: www.bioaccessbd.com 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row product_page">
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
            <div class="content">
                  @foreach ($proposal->product_items as $item)
                    <div>
                        @isset($item->productItem->catalog)
                        <iframe name="printf" src="{{ asset($item->productItem->catalog) }}" width="100%" height="500" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="width: 100%;" allowfullscreen> </iframe>
                            {{-- <iframe src="{{ asset($item->productItem->catalog) }}" style="width:100%;height:700px;"></iframe> --}}
                        @endisset
                        
                    </div>
                      
                  @endforeach
            </div>
        </div>
    </div>

    <div class="row product_page">
        
        <div class="col-xs-12">
            <div class="content text-center">
                <h3>
                    Our Client List
                </h3> 
            </div>
        </div>
        @foreach (App\Model\Customer::all() as $customer)
            <div class="col-xs-4">
                <div class="content">
                    {{ $customer->name }}
                </div>
            </div>
        @endforeach
        
    </div>
</div>



<script>
    window.print();
    // window.frames["printf"].focus();
    // window.frames["printf"].print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
