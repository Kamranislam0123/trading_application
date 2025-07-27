<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\Customer;
use App\Model\MobileBanking;
use App\Model\ProductItem;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\SalePayment;
use App\Model\SalesOrder;
use App\Model\Service;
use App\Model\Supplier;
use App\Model\TransactionLog;
use App\Model\Warehouse;
use App\Model\Product;
use App\Model\Proposal;
use App\Model\ProposalProduct;
use App\Model\TermsCondition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use DataTables;
use PDF;
use DB;
use Illuminate\Support\Facades\Gate;

class ProposalController extends Controller
{
    public function proposalCreate() {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();

        return view('proposal.create', compact('warehouses', 'banks',
            'productItems'));
    }

    public function proposalStore(Request $request) {

        $total = $request->total;
        $due = $request->due_total;
        $rules = [
            'customer' => 'required',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'tax' => 'required|numeric',
            'vat' => 'required|numeric',
            'installation_charge' => 'required|numeric',
        ];

        if ($request->product_item) {
            $rules['product_item.*'] = 'required';
            $rules['product.*'] = 'required';
            // $rules['description.*'] = 'required';
            $rules['warehouse.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:.01';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }

        if ($request->service_name) {
            $rules['service_name.*'] = 'required';
            $rules['service_quantity.*'] = 'required|numeric|min:.01';
            $rules['service_unit_price.*'] = 'required|numeric|min:0';
        }

        $request->validate($rules);
        // dd($request->all());

        $proposal = new Proposal();
        $proposal->title = $request->title;
        $proposal->customer_id = $request->customer;
        $proposal->sub_customer_id = $request->sub_customer;
        $proposal->date = $request->date;
        $proposal->sub_total = 0;
        $proposal->vat_percentage = 0;
        $proposal->vat = $request->vat;
        $proposal->tax = $request->tax;
        $proposal->discount = $request->discount;
        $proposal->total = 0;
        $proposal->service_sub_total = 0;
        $proposal->service_vat_percentage = 0;
        $proposal->service_vat = 0;
        $proposal->service_discount = 0;
        $proposal->created_by = Auth::user()->id;
        $proposal->save();
        $proposal->proposal_no = date('dmY').$proposal->id;
        $proposal->save();

        $counter = 0;
        $subTotal = 0;

        if  ($request->product_item) {
            foreach ($request->product_item as $productItemId) {
                // $product = Product::find($request->product[$counter]);
                $product = Product::where('product_item_id',$productItemId)->orWhereNotNull('id')->first();
                $productItem = ProductItem::find($productItemId);
                $product_exist = ProposalProduct::where(['proposal_id'=> $proposal->id,'product_item_id'=>$productItemId])->first();
                if(empty($product_exist)){
                    ProposalProduct::create([
                        'proposal_id' => $proposal->id,
                        'customer_id' => $request->customer,
                        'sub_customer_id' => $request->sub_customer,
                        'product_id' => $product->id,
                        'product_item_id' => $productItem->id??'',
                        'product_item_name' => $productItem->name??'',
                        'product_name' => $product->name,
                        'warehouse_id' =>  $request->warehouse[$counter],
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    ]);
                    
                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];              
                    $counter++;
                }
                
            }
        }

        $proposal->sub_total = $subTotal;
        $proposal->installation_charge = $request->installation_charge;
        $proposal->service_sub_total = 0;
        $total = $subTotal + $request-> vat + $request->tax + $request->installation_charge - $request->discount;
        $proposal->total = $total;
        $proposal->save();

        return redirect()->route('proposal.details', ['proposal' => $proposal->id]);
    }

    public function proposalEdit(Proposal $proposal, Request $request) {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();

        return view('proposal.edit', compact('warehouses', 'banks',
            'productItems','proposal'));
    }

    public function proposalUpdate(Proposal $proposal, Request $request) {

        $total = $request->total;
        $due = $request->due_total;
        $rules = [
            'customer' => 'required',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'tax' => 'required|numeric',
            'vat' => 'required|numeric',
            'installation_charge' => 'required|numeric',
        ];

        if ($request->product_item) {
            $rules['product_item.*'] = 'required';
            $rules['product.*'] = 'required';
            // $rules['description.*'] = 'required';
            $rules['warehouse.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:.01';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }

        if ($request->service_name) {
            $rules['service_name.*'] = 'required';
            $rules['service_quantity.*'] = 'required|numeric|min:.01';
            $rules['service_unit_price.*'] = 'required|numeric|min:0';
        }

        $request->validate($rules);
        // dd($request->all());

        // $proposal = new Proposal();
        
        
        $counter = 0;
        $subTotal = 0;

        if  ($request->product_item) {
            foreach ($request->product_item as $productItemId) {
                // $product = Product::find($request->product[$counter]);
                $product = Product::where('product_item_id',$productItemId)->orWhereNotNull('id')->first();
                $productItem = ProductItem::find($productItemId);
                // dd($productItem);
                $product_exist = ProposalProduct::where(['proposal_id'=> $proposal->id,'product_item_id'=>$productItemId])->first();
                if(empty($product_exist)){
                    ProposalProduct::create([
                        'proposal_id' => $proposal->id,
                        'customer_id' => $request->customer,
                        'sub_customer_id' => $request->sub_customer,
                        'product_id' => $product->id,
                        'product_item_id' => $productItem->id??'',
                        'product_item_name' => $productItem->name??'',
                        'product_name' => $product->name,
                        'warehouse_id' =>  $request->warehouse[$counter],
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    ]);
                    
                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];              
                    $counter++;
                }else{
                    $product_exist->update([
                        'customer_id' => $request->customer,
                        'sub_customer_id' => $request->sub_customer,
                        'product_id' => $product->id,
                        'product_item_id' => $productItem->id??'',
                        'product_item_name' => $productItem->name??'',
                        'product_name' => $product->name,
                        'warehouse_id' =>  $request->warehouse[$counter],
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    ]);
                    
                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];              
                    $counter++;
                }
                
            }
        }
        // Remove Old Product
        ProposalProduct::where('proposal_id', $proposal->id)->whereNotIn('product_item_id',$request->product_item)->delete();

        $proposal->title = $request->title;
        $proposal->customer_id = $request->customer;
        $proposal->date = $request->date;
        $proposal->vat = $request->vat;
        $proposal->tax = $request->tax;
        $proposal->discount = $request->discount;
        $proposal->installation_charge = $request->installation_charge;
        $proposal->service_sub_total = 0;
        
        $proposal->sub_total = $subTotal;
        $total = $subTotal + $request->vat + $request->tax + $request->installation_charge - $request->discount;
        $proposal->total = $total;
        $proposal->save();

        return redirect()->route('proposal.details', ['proposal' => $proposal->id]);
    }

    public function proposals(Request $request) {
        //dd($order->product_items);
        return view('proposal.proposals');
    }

    public function proposalsDatatable() {
        $query = Proposal::with(['customer', 'subCustomer'])->where('created_by', Auth::id());

        return DataTables::eloquent($query)
            ->addColumn('name', function(Proposal $proposal) {
                return $proposal->customer->name??'';
            })
            ->addColumn('subCustomer', function(Proposal $proposal) {
                return $proposal->subCustomer->name??'';
            })
            ->addColumn('mobile', function(Proposal $proposal) {
                return $proposal->customer->mobile_no;
            })
            ->addColumn('action', function(Proposal $proposal) {
                $action = '<a href="'.route('proposal.details', ['proposal' => $proposal->id]).'" class="btn btn-info btn-sm">View</a> ';
                $action .= '<a href="'.route('proposal.edit', ['proposal' => $proposal->id]).'" class="btn btn-primary btn-sm">Edit</a>';

                return $action;
            })
            ->editColumn('date', function(Proposal $proposal) {
                return $proposal->date->format('j F, Y');
            })
            ->editColumn('total', function(Proposal $proposal) {
                return '৳'.number_format($proposal->total, 2);
            })
            ->orderColumn('date', function ($query, $proposal) {
                $query->orderBy('date', $proposal)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function allProposals(Request $request) {
        //dd($order->product_items);
        return view('proposal.all_proposals');
    }

    public function allProposalsDatatable() {
        $query = Proposal::with(['customer', 'subCustomer']);

        return DataTables::eloquent($query)
            ->addColumn('name', function(Proposal $proposal) {
                return $proposal->customer->name??'';
            })
            ->addColumn('subCustomer', function(Proposal $proposal) {
                return $proposal->subCustomer->name??'';
            })
            ->addColumn('mobile', function(Proposal $proposal) {
                return $proposal->customer->mobile_no;
            })
            ->addColumn('action', function(Proposal $proposal) {
                $action = '<a href="'.route('proposal.details', ['proposal' => $proposal->id]).'" class="btn btn-info btn-sm">View</a> ';
                $action .= '<a href="'.route('proposal.edit', ['proposal' => $proposal->id]).'" class="btn btn-primary btn-sm">Edit</a>';

                return $action;
            })
            ->editColumn('date', function(Proposal $proposal) {
                return $proposal->date->format('j F, Y');
            })
            ->editColumn('total', function(Proposal $proposal) {
                return '৳'.number_format($proposal->total, 2);
            })
            ->orderColumn('date', function ($query, $proposal) {
                $query->orderBy('date', $proposal)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function proposalDetails(Proposal $proposal) {
        //dd($order->product_items);
        return view('proposal.details', compact('proposal'));
    }

    public function proposalPrint(Proposal $proposal) {
        $terms_condition = TermsCondition::first();
        $proposal->amount_in_word = DecimalToWords::convert($proposal->total,'Taka',
            'Poisa');
        
        // dd($data);
        $data = [
            // 'title' => 'Welcome to ItSolutionStuff.com',
                'proposal' => $proposal,
                'terms_condition' => $terms_condition,
            ];

        $pdf = PDF::loadView('proposal.print_pdf', $data);
        $pdf->save('public/uploads/web_content.pdf');
        // $pdf->save('uploads/'.$filename);
        // return $pdf->stream('proposal.pdf');

        $pdfMerger = PDFMerger::init();
        $pdfMerger->addPDF(public_path('uploads/web_content.pdf'), 'all');
        if(count($proposal->product_items??[]) > 0){
            foreach ($proposal->product_items as $key => $item) {
                if ($item->productItem->catalog) {
                    $pdfMerger->addPDF(public_path($item->productItem->catalog), 'all');
                }
                
            }
        }
        $pdfMerger->merge();
        $pdfMerger->save(public_path('uploads/proposal.pdf'), "file");

        return redirect(url('public/uploads/proposal.pdf'));
        // return public_path('uploads/catalog.pdf');
        // return $pdf->stream('proposal.pdf');

        // return view('proposal.print', compact('proposal'));
    }

}
