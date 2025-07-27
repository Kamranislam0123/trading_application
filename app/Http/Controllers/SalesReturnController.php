<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use App\Model\ProductCategory;
use App\Model\ProductColor;
use App\Model\ProductItem;
use App\Model\ProductReturnOrder;
use App\Model\ProductSize;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\PurchaseOrderProduct;
use App\Model\Supplier;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class SalesReturnController extends Controller
{
    public function index()
    {
        return view('sale.sales_return.all');
    }

    public function add()
    {
        return view('sale.sales_return.create');
    }

    public function addPost(Request $request)
    {
        if (empty($request->product_serial)) {
            $message = 'No Product Item Found';
            return redirect()->back()->withInput()->with('message', $message);
        }

        $rules = [
            'date' => 'required|date',
            'purchase_inventory.*' => 'required',
            'product_item.*' => 'required',
            'product_category.*' => 'required',
            'warehouse.*' => 'required',
            'quantity.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',

        ];

        $validator = $request->validate($rules);

//        $available = true;
//        $message = '';
//
//        if ($request->purchase_inventory) {
//            foreach ($request->purchase_inventory as $key => $purchase_inventory_id) {
//                $inventory = PurchaseInventory::find($purchase_inventory_id);
//                if ($inventory) {
//                    $purchaseInventory = PurchaseInventoryLog::where('serial',$inventory->serial)
//                    ->where('return_status',1)->first();
//                    if ($purchaseInventory) {
//                        $available = false;
//                        $message = 'Already Return This Product ' . $inventory->serial;
//                        break;
//                    }
//                }
//            }
//        }
//
//        if (!$available) {
//            return redirect()->back()->withInput()->with('message', $message);
//        }

        $order = new ProductReturnOrder();
        $order->company_branch_id = Auth::user()->company_branch_id;
        $order->customer_id = $request->customer;
        $order->date = $request->date;
        $order->total = 0;
        $order->user_id = Auth::user()->id;
        $order->save();
        $order->order_no = str_pad($order->id, 6, 0, STR_PAD_LEFT);
        $order->save();

        $subTotal = 0;

        foreach ($request->purchase_inventory as $key => $purchase_inventory_id) {
            $inventory = PurchaseInventory::find($purchase_inventory_id);
            // Inventory Log
            $inventoryLog = new PurchaseInventoryLog();
            $inventoryLog->product_return_order_id = $order->id;
            $inventoryLog->customer_id = $request->customer;
            $inventoryLog->product_item_id = $inventory->product_item_id;
            $inventoryLog->product_category_id = $inventory->product_category_id;
            $inventoryLog->warehouse_id = $inventory->warehouse_id;
            $inventoryLog->serial = $inventory->serial;
            $inventoryLog->type = 1;
            $inventoryLog->date = $request->date;
            $inventoryLog->quantity = $request->quantity[$key];
            $inventoryLog->unit_price = $request->unit_price[$key];
            $inventoryLog->selling_price = $request->selling_price[$key];
            $inventoryLog->sale_total = $inventoryLog->quantity * $inventoryLog->selling_price;
            $inventoryLog->total = $inventoryLog->quantity * $inventoryLog->selling_price;
            $inventoryLog->sales_order_id = null;
            $inventoryLog->sales_order_no = $request->sales_order_no;
            $inventoryLog->purchase_inventory_id = $inventory->id;
            $inventoryLog->note = 'Sale Return Product';
            $inventoryLog->return_status = 1;
            $inventoryLog->user_id = Auth::id();
            $inventoryLog->company_branch_id = Auth::user()->company_branch_id;
            $inventoryLog->save();

            $inventory->increment('quantity', $request->quantity[$key]);

            $inventory->update([
                'unit_price' => $request->unit_price[$key],
                'selling_price' => $request->selling_price[$key],
            ]);

            $subTotal += $request->quantity[$key] * $inventoryLog->selling_price;


//            $inventory->update([
//                'quantity' => $inventory->in_product - $inventory->out_product
//            ]);
        }
        $order->total = $subTotal;
        $order->save();

        return redirect()->route('return_invoice.details', ['order' => $order->id]);
        //return view('sale.sales_return.invoice_details',compact('$order->id'));
    }
    public function saleReturnProductDetails(Request $request) {

        $firstWarehouseProduct = PurchaseInventory::where('serial', $request->serial)
            ->where('warehouse_id', 1)
            ->where('quantity', '>=', 0)
            ->with('productItem','productCategory','warehouse')
            ->first();

        $secondWarehouseProduct = PurchaseInventory::where('serial', $request->serial)
            ->where('warehouse_id', 2)
            ->where('quantity', '>=', 0)
            ->with('productItem','productCategory','warehouse')
            ->first();

        if (!empty($firstWarehouseProduct && $secondWarehouseProduct)) {

            if ($firstWarehouseProduct->quantity >= $secondWarehouseProduct->quantity) {
                $product = $firstWarehouseProduct->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            }elseif($firstWarehouseProduct->quantity <= $secondWarehouseProduct->quantity){
                $product = $secondWarehouseProduct->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            }else{
                return response()->json(['success' => false, 'message' => 'Not found.']);
            }

        }else{

            $product = PurchaseInventory::where('serial', $request->serial)
                ->where('quantity', '>=', 0)
                ->with('productItem','productCategory','warehouse')
                ->first();

            if ($product) {
                $product = $product->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            } else {
                return response()->json(['success' => false, 'message' => 'Not found.']);
            }
        }


        if ($product) {
            $product = $product->toArray();
            return response()->json(['success' => true, 'data' => $product]);
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function productReturnInvoiceAll(){
        $productReturnOrders = ProductReturnOrder::orderBy('id', 'desc')->with('logs','customer')->get();
        return view('sale.sales_return.invoice_all',compact('productReturnOrders'));
    }
    public function returnInvoiceDetails(ProductReturnOrder $order)
    {
        return view('sale.sales_return.invoice_details', compact('order'));
    }

    public function returnInvoicePrint(ProductReturnOrder $order) {
        return view('sale.sales_return.invoice_print', compact('order'));
    }

    public function returnInvoiceBarcode( ProductReturnOrder $order) {

        return view('sale.sales_return.barcode_all', compact('order'));
    }

    public function returnInvoiceBarcodePrint($order) {
        $product = PurchaseInventoryLog::where('id',$order)->first();

        return view('sale.sales_return.barcode_print', compact('product'));
    }

    public function edit(PurchaseInventoryLog $purchase_inventory_log)
    {
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $product_categories = ProductCategory::where('status', 1)->get();
        return view('sale.sales_return.edit', compact(
            'customers',
            'warehouses',
            'productItems',
            'product_categories',
            'purchase_inventory_log'
        ));
    }

    public function editPost(Request $request, PurchaseInventoryLog $purchase_inventory_log)
    {
        $rules = [
            'warehouse_id' => 'required',
            'date' => 'required|date',
            'product_item' => 'required',
            'product_category' => 'required',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ];

        $validator = $request->validate($rules);

        $purchase_inventory_log->customer_id = $request->customer;
        $purchase_inventory_log->date = $request->date;
        $purchase_inventory_log->quantity = $request->quantity;
        $purchase_inventory_log->selling_price = $request->unit_price;
        $purchase_inventory_log->sale_total = $request->quantity * $purchase_inventory_log->selling_price;
        $purchase_inventory_log->total = $request->quantity * $purchase_inventory_log->unit_price;
        $purchase_inventory_log->sales_order_no = $request->sales_order_no;
        $purchase_inventory_log->save();

        $inventory = $purchase_inventory_log->purchaseInventory;
        $inventory->update([
            'quantity' => $inventory->in_product - $inventory->out_product,
        ]);

        return redirect()->route('sales_return')->with('message', 'Return product updated successfully.');
    }

    public function details(PurchaseInventoryLog $purchase_inventory_log){
        return view('sale.sales_return.details',compact('purchase_inventory_log'));
    }
    public function receiptPrint(PurchaseInventoryLog $purchase_inventory_log){
        return view('sale.sales_return.print',compact('purchase_inventory_log'));
    }

    public function datatable()
    {
        if (Auth::user()->company_branch_id == 0) {
            $query = PurchaseInventoryLog::with(['customer','purchaseInventory', 'productItem', 'productCategory', 'warehouse'])
                ->where('return_status', 1);

        }else{
            $query = PurchaseInventoryLog::with(['customer','purchaseInventory', 'productItem', 'productCategory', 'warehouse'])
                ->where('return_status', 1)
                ->where('company_branch_id', Auth::user()->company_branch_id);
        }

        return DataTables::eloquent($query)
            ->addColumn('customer_name', function (PurchaseInventoryLog $purchase_inventory_log) {
                return $purchase_inventory_log->customer->name??'';
            })
            ->addColumn('date', function (PurchaseInventoryLog $purchase_inventory_log) {
                return $purchase_inventory_log->date->format('d-m-Y');
            })
            ->editColumn('selling_price', function (PurchaseInventoryLog $purchase_inventory_log) {
               if (auth()->user()->role == 2)
                    return $purchase_inventory_log->unit_price + nbrSellCalculation($purchase_inventory_log->unit_price);
               else
                   return $purchase_inventory_log->selling_price;
            })
            ->addColumn('action', function (PurchaseInventoryLog $purchase_inventory_log) {
                if (auth()->user()->role != 2){
                    return '<a class="btn btn-info btn-sm" href="' . route('sales_return.edit', ['purchase_inventory_log' => $purchase_inventory_log->id]) . '"> Edit </a>
                        <a class="btn btn-info btn-sm" href="' . route('sales_return.details', ['purchase_inventory_log' => $purchase_inventory_log->id]) . '"> Details </a>
                        <a role="button" class="btn btn-success btn-sm barcode_modal" data-id="' . $purchase_inventory_log->purchaseInventory->id . '" data-name="' . $purchase_inventory_log->productItem->name . '" data-code="' . $purchase_inventory_log->serial . '"> Barcode </a>';
            }

            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
