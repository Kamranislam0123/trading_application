<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use App\Model\ManualStockOrder;
use App\Model\ProductCategory;
use App\Model\ProductColor;
use App\Model\ProductItem;
use App\Model\ProductSize;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\PurchaseOrder;
use App\Model\PurchaseOrderProduct;
use App\Model\Supplier;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class ManuallyStockController extends Controller
{
    public function index()
    {
        return view('purchase.product_stock.all');
    }

    public function add()
    {
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();

        return view('purchase.product_stock.create', compact(
            'customers',
            'warehouses'
        ));
    }

    public function addPost(Request $request)
    {
        $rules = [
            'warehouse_id' => 'required',
            'date' => 'required|date',
            'quantity.*' => 'required|numeric|min:0',
            'unit_price.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',
        ];

        if ($request->customer_type == 1) {
            $rules['customer_name'] = 'required | unique:customers,name';
            $rules['mobile_no'] = 'required |unique:customers,mobile_no';
            $rules['address'] = 'required';
        }
        if ($request->customer_type == 2) {
            $rules['customer_id'] = 'required';
        }

        $validator = $request->validate($rules);

        if ($request->customer_type == 1){
            $customer = new Customer();
            $customer->name = $request->customer_name;
            if (Auth::user()->company_branch_id == 0) {
                $customer->company_branch_id = $request->companyBranch;
            }else{
                $customer->company_branch_id = Auth::user()->company_branch_id;
            }
            $customer->mobile_no = $request->mobile_no;
            $customer->address = $request->address;
            $customer->save();
        }else{
            $customer = Customer::find($request->customer_id);
        }


        $order = new ManualStockOrder();
        //$order->order_no = rand(10000000, 99999999);
        $order->customer_id = $customer->id;
        $order->warehouse_id = $request->warehouse_id;
        $order->date = $request->date;
        $order->total = 0;
        $order->user_id = Auth::id();
        $order->save();
        $order->order_no = str_pad($order->id, 5, 0, STR_PAD_LEFT);
        $order->save();
        $sub_total = 0;

        $counter = 0;

        foreach ($request->product_item as $product_id) {
            if ($product_id != '') {
                $productItem = ProductItem::where('name', $product_id)->first();
                $product_category = ProductCategory::where('name', $request->product_category[$counter])
                    ->first();

                if (!$productItem) {
                    $productItem = new ProductItem();
                    $productItem->name = $request->product_item[$counter];
                    $productItem->unit_id = 1;
                    $productItem->type = $request->product_type;
                    $productItem->status = 1;
                    $productItem->save();
                }
                if (!$product_category) {
                    $product_category = new ProductCategory();
                    $product_category->name = $request->product_category[$counter];
                    $product_category->type = $request->product_type;
                    $product_category->status = 1;
                    $product_category->save();
                }
                    // Inventory Log
                $log = PurchaseInventoryLog::create([
                    'manual_stock_order_id' => $order->id,
                    'product_item_id' => $productItem->id,
                    'product_category_id' => $product_category->id,
                    'warehouse_id' => $request->warehouse_id,
                    'customer_id' => $customer->id,
                    'type' => 1,
                    'stock_type' => 2,
                    'date' => $request->date,
                    'quantity' => $request->quantity[$counter],
                    'unit_price' => $request->unit_price[$counter],
                    'selling_price' => $request->selling_price[$counter],
                    'sale_total' => $request->quantity[$counter] * $request->selling_price[$counter],
                    'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    'note' => 'Stock Product',
                    'user_id' =>  Auth::id(),
                ]);
                $sub_total += $request->quantity[$counter] * $request->unit_price[$counter];

                $inventory = PurchaseInventory::where('product_item_id', $productItem->id)
                    ->where('product_category_id', $product_category->id)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if ($inventory) {
                    $inventory->update([
                        'product_item_id' => $productItem->id,
                        'product_category_id' => $product_category->id,
                        'warehouse_id' => $request->warehouse_id,
                        'quantity' => $inventory->quantity + $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'avg_unit_price' => $request->unit_price[$counter],
                        'selling_price' => $request->selling_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],

                    ]);
                }else{
                    $inventory = PurchaseInventory::create([
                        'product_item_id' => $productItem->id,
                        'product_category_id' => $product_category->id,
                        'warehouse_id' => $request->warehouse_id,
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'avg_unit_price' => $request->unit_price[$counter],
                        'selling_price' => $request->selling_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    ]);
                }

                $log->update([
                    'purchase_inventory_id' => $inventory->id,
                    'serial' => str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
                ]);
                $inventory->update([
                    'quantity' => $inventory->in_product - $inventory->out_product,
                    'serial' => str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
                ]);

                $counter++;
            }
        }
        $order->total = $sub_total;
        $order->save();

        return redirect()->route('stock_product.invoice', ['order' => $order->id]);
        //return redirect()->route('product_stock')->with('message', 'Manually stock add successfully.');
    }

    public function edit(PurchaseInventoryLog $purchase_inventory_log)
    {
        $suppliers = Customer::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $product_colors = ProductColor::where('status', 1)->get();
        $product_sizes = ProductSize::where('status', 1)->get();
        $product_categories = ProductCategory::where('status', 1)->get();

        return view('purchase.product_stock.edit', compact(
            'suppliers',
            'warehouses',
            'productItems',
            'product_colors',
            'product_sizes',
            'product_categories',
            'purchase_inventory_log'
        ));
    }

    public function editPost(Request $request, PurchaseInventoryLog $purchase_inventory_log)
    {
        $rules = [
            'warehouse_id' => 'required',
            'customer_id' => 'required',
            'date' => 'required',
            'product_item' => 'required',
            'product_category' => 'required',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ];

        $validator = $request->validate($rules);

        $productItem = ProductItem::where('name', $request->product_item)->first();
        $product_category = ProductCategory::where('name', $request->product_category)->first();
//
        if (!$productItem) {
            $productItem = new ProductItem();
            $productItem->name = $request->product_item;
            $productItem->unit_id = 1;
            $productItem->status = 1;
            $productItem->save();
        }
        if (!$product_category) {
            $product_category = new ProductCategory();
            $product_category->name = $request->product_category;
            $product_category->status = 1;
            $product_category->save();
        }
        $purchase_inventory_log->date = $request->date;
        $purchase_inventory_log->customer_id = $request->customer_id;
        $purchase_inventory_log->product_item_id = $productItem->id;
        $purchase_inventory_log->product_category_id = $product_category->id;
        $purchase_inventory_log->quantity = $request->quantity;
        $purchase_inventory_log->unit_price = $request->unit_price;
        $purchase_inventory_log->selling_price = $request->selling_price;
        $purchase_inventory_log->sale_total = $request->quantity*$request->selling_price;
        $purchase_inventory_log->total = $request->quantity*$request->unit_price;
        $purchase_inventory_log->save();

        $inventory = $purchase_inventory_log->purchaseInventory;
        $inventory->update([
            'quantity' => $inventory->in_product - $inventory->out_product,
            'product_item_id' => $productItem->id,
            'product_category_id' => $product_category->id,
        ]);

        return redirect()->route('product_stock')->with('message', 'Manually stock updated successfully.');
    }

    public function stockProductDetails(Request $request)
    {
        $product = PurchaseInventory::where('serial', $request->serial)
            ->with('productItem', 'productCategory', 'productColor', 'productSize', 'warehouse')
            ->first();

        if ($product) {
            $product = $product->toArray();
            return response()->json(['success' => true, 'data' => $product]);
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function stockProductInvoice(ManualStockOrder $order)
    {
        return view('purchase.product_stock.invoice', compact('order'));
    }
    public function stockProductInvoicePrint(ManualStockOrder $order) {
        return view('purchase.product_stock.invoice_print', compact('order'));
    }
    public function manualStockDetails(PurchaseInventoryLog $purchase_inventory_log) {
        return view('purchase.product_stock.details', compact('purchase_inventory_log'));
    }
    public function stockReceiptPrint(PurchaseInventoryLog $purchase_inventory_log) {
        return view('purchase.product_stock.print', compact('purchase_inventory_log'));
    }

    public function stockProductInvoiceAll(){
        $manualStockOrders = ManualStockOrder::with('logs','customer')->get();
        return view('purchase.product_stock.invoice_all',compact('manualStockOrders'));
    }

    public function stockProductBarcode( ManualStockOrder $order) {

        return view('purchase.product_stock.barcode_all', compact('order'));
    }

    public function stockProductBarcodePrint($order) {
        $product = PurchaseInventoryLog::where('id',$order)->first();

        return view('purchase.product_stock.barcode_print', compact('product'));
    }

    public function datatable()
    {
        $query = PurchaseInventoryLog::with(['purchaseInventory','productItem', 'productCategory','warehouse'])->where('stock_type',2);

        return DataTables::eloquent($query)
            ->editColumn('date', function (PurchaseInventoryLog $purchase_inventory_log) {
                return $purchase_inventory_log->date->format('d-m-Y');
            })
            ->addColumn('action', function (PurchaseInventoryLog $purchase_inventory_log) {
                return '<a href="'.route('manual_stock.details', ['purchase_inventory_log' => $purchase_inventory_log->id]).'" class="btn btn-primary btn-sm">View</a>
                         <a class="btn btn-info btn-sm" href="' . route('product_stock.edit', ['purchase_inventory_log' => $purchase_inventory_log->id]) . '"> Edit </a>
                         <a role="button" class="btn btn-success btn-sm barcode_modal" data-id="'.$purchase_inventory_log->purchaseInventory->id.'" data-name="'.$purchase_inventory_log->productItem->name. '" data-code="' . $purchase_inventory_log->serial . '"> Barcode </a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
