<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\MobileBanking;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Model\ProductColor;
use App\Model\ProductItem;
use App\Model\ProductSize;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\PurchaseOrder;
use App\Model\PurchaseOrderProduct;
use App\Model\PurchaseOrderPurchaseProduct;
use App\Model\PurchasePayment;
use App\Model\PurchaseProduct;
use App\Model\SalesOrderProduct;
use App\Model\StockTransferOrder;
use App\Model\Supplier;
use App\Model\TransactionLog;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;

class PurchaseController extends Controller
{
    public function purchaseOrder() {
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name','desc')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $product_colors = ProductColor::where('status', 1)->get();
        $product_sizes = ProductSize::where('status', 1)->get();
        $product_categories = ProductCategory::where('status', 1)->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('purchase.purchase_order.create', compact('suppliers',
            'warehouses', 'productItems','product_colors','product_sizes','product_categories','banks'));
    }

    public function purchaseOrderPost(Request $request) {

        $rules = [
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
            'product_type' => 'required',
            'date' => 'required|date',
            'transport_cost' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'quantity.*' => 'required|numeric|min:0',
            'unit_price.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',
        ];

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $validator = $request->validate($rules);

//        $available = true;
//        $message = '';
//        $counter = 0;
//
//
//            foreach ($request->product_item as $product_id) {
//                $product_category = ProductCategory::where('name', $request->product_category[$counter])->first();
//                if (!$product_category) {
//                    $available = false;
//                    $message = 'Category Invalid ' . $request->product_category[$counter];
//                    break;
//                }
//                $counter++;
//            }
//        if (!$available) {
//            return redirect()->back()->withInput()->with('message', $message);
//        }
//
//        $counter = 0;
//
//        foreach ($request->product_item as $product_id) {
//            if ($product_id != '') {
//                $product_category = ProductCategory::where('name', $request->product_category[$counter])->first();
//
//                if (!$product_category){
//
//                }
//            }
//
//                $counter++;
//            }

                $order = new PurchaseOrder();
                // $order->order_no = rand(10000000, 99999999);
                $order->supplier_id = $request->supplier_id;
                $order->warehouse_id = $request->warehouse_id;
                $order->product_type = $request->product_type;
                $order->transport_cost = $request->transport_cost;
                $order->discount_percentage = $request->discount_percentage;
                $order->discount = $request->discount;
                $order->date = $request->date;
                $order->total = 0;
                $order->paid = 0;
                $order->due = 0;
                $order->user_id = Auth::id();
                $order->save();
                $order->order_no = str_pad($order->id, 5, 0, STR_PAD_LEFT);
                $order->save();

                $sub_total = 0;
                $counter = 0;


                foreach ($request->product_item as $product_id) {
                    if ($product_id != '') {
                        $productItem = ProductItem::where('name', $product_id)->where('type',$request->product_type)->first();
                        $product_category = ProductCategory::where('name', $request->product_category[$counter])
                            ->where('type',$request->product_type)
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

                        $purchase_order_product = PurchaseOrderProduct::where('purchase_order_id', $order->id)
                            ->where('product_item_id', $productItem->id)
                            ->where('product_category_id', $product_category->id)
                            ->where('warehouse_id', $request->warehouse_id)
                            ->first();

                        if (empty($purchase_order_product)) {
                            $purchase_order_product = PurchaseOrderProduct::create([
                                'purchase_order_id' => $order->id,
                                'product_item_id' => $productItem->id,
                                'product_category_id' => $product_category->id,
                                'warehouse_id' => $request->warehouse_id,
                                'product_type' => $request->product_type,
                                'date' => $request->date,
                                'quantity' => $request->quantity[$counter],
                                'unit_price' => $request->unit_price[$counter],
                                'selling_price' => $request->selling_price[$counter],
                                'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                            ]);
                            $sub_total += $request->quantity[$counter] * $request->unit_price[$counter];

                            // Inventory Log
                            $log = PurchaseInventoryLog::create([
                                'purchase_order_id' => $order->id,
                                'product_item_id' => $productItem->id,
                                'product_category_id' => $product_category->id,
                                'warehouse_id' => $request->warehouse_id,
                                'supplier_id' => $request->supplier_id,
                                'type' => 1,
                                'date' => $request->date,
                                'quantity' => $request->quantity[$counter],
                                'unit_price' => $request->unit_price[$counter],
                                'selling_price' => $request->selling_price[$counter],
                                'sale_total' => $request->quantity[$counter] * $request->selling_price[$counter],
                                'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                                'note' => 'Purchase Product',
                                'user_id' =>  Auth::id(),
                            ]);
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
                                    'selling_price' => $request->selling_price[$counter],
                                    'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                                ]);
                            } else {
                                $inventory = PurchaseInventory::create([
                                    'product_item_id' => $productItem->id,
                                    'product_category_id' => $product_category->id,
                                    'warehouse_id' => $request->warehouse_id,
                                    'quantity' => $request->quantity[$counter],
                                    'unit_price' => $request->unit_price[$counter],
                                    'selling_price' => $request->selling_price[$counter],
                                    'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                                ]);
                            }

                            $log->update([
                                'purchase_inventory_id' => $inventory->id,
                                'serial' => str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
                            ]);
                            $purchase_order_product->update(['purchase_inventory_id' => $inventory->id]);
                            $totalAmount = PurchaseOrderProduct::where('purchase_inventory_id', $inventory->id)->sum('unit_price');
                            $totalQuantity = PurchaseOrderProduct::where('purchase_inventory_id', $inventory->id)->count();
                            $inventory->update([
                                'quantity' => $inventory->in_product - $inventory->out_product,
                                'serial' => str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
                                'avg_unit_price' => $totalAmount / $totalQuantity,
                            ]);
                            $purchase_order_product->update(['serial' => $inventory->serial]);
                        }

                        $counter++;
                    }
                }

                $total = $sub_total+$request->transport_cost-$request->discount;
                $order->total = $total;
                $order->due = $total;
                $order->save();


        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment = new PurchasePayment();
                $payment->purchase_order_id = $order->id;
                $payment->supplier_id = $request->supplier_id;
                $payment->transaction_method = $request->payment_type;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->note = $request->note;
                $payment->save();

                if ($request->payment_type == 1)
                    Cash::first()->decrement('amount', $request->paid);
                else
                    MobileBanking::first()->decrement('amount', $request->paid);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Paid to ' . $order->supplier->name . ' for ' . $order->order_no;
                $log->transaction_type = 3;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->amount = $request->paid;
                $log->note = $request->note;
                $log->purchase_payment_id = $payment->id;
                $log->save();
            } else {
                $image = 'img/no_image.png';

                if ($request->cheque_image) {
                    // Upload Image
                    $file = $request->file('cheque_image');
                    $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'public/uploads/purchase_payment_cheque';
                    $file->move($destinationPath, $filename);

                    $image = 'uploads/purchase_payment_cheque/' . $filename;
                }

                $payment = new PurchasePayment();
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->bank_id = $request->bank;
                $payment->branch_id = $request->branch;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->note = $request->note;
                $payment->save();

                BankAccount::find($request->account)->decrement('balance', $request->paid);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Paid to ' . $order->supplier->name . ' for ' . $order->order_no;
                $log->transaction_type = 3;
                $log->transaction_method = 2;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->bank_id = $request->bank;
                $log->branch_id = $request->branch;
                $log->bank_account_id = $request->account;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->paid;
                $log->note = $request->note;
                $log->purchase_payment_id = $payment->id;
                $log->save();
            }

            $order->increment('paid', $request->paid);
            $order->decrement('due', $request->paid);
        }

        return redirect()->route('purchase_receipt.details', ['order' => $order->id]);
    }

    public function purchaseOrderEdit(Request $request, PurchaseOrder $order) {
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $product_colors = ProductColor::where('status', 1)->get();
        $product_sizes = ProductSize::where('status', 1)->get();
        $product_categories = ProductCategory::where('status', 1)->get();

        return view('purchase.purchase_order.edit', compact('suppliers',
            'warehouses', 'productItems','product_colors','product_sizes','product_categories','order'));
    }

//     public function purchaseOrderEditPost(Request $request, PurchaseOrder $order) {

//       // dd($request->all());

//         $rules = [
//             'supplier_id' => 'required',
//             'warehouse_id' => 'required',
//             'product_type' => 'required',
//             'date' => 'required|date',
//             'transport_cost' => 'required|numeric|min:0',
//             'discount' => 'required|numeric|min:0',
//             'product_item.*' => 'required',
//             'product_category.*' => 'required',
//             'quantity.*' => 'required|numeric|min:0',
//             'unit_price.*' => 'required|numeric|min:0',
//             'selling_price.*' => 'required|numeric|min:0',
//         ];

//         $validator = $request->validate($rules);

//         $order->supplier_id = $request->supplier_id;
//         $order->product_type = $request->product_type;
//         $order->warehouse_id = $request->warehouse_id;
//         $order->transport_cost = $request->transport_cost;
//         $order->discount_percentage = $request->discount_percentage;
//         $order->discount = $request->discount;
//         $order->date = $request->date;
//         $order->save();

//         foreach ($request->product_item as $key => $product_item_id) {
//             $previousSerials[] = $request->serial;
//         }

//         // Remove previous
//         PurchaseInventoryLog::where('purchase_order_id', $order->id)->delete();
//         $prev_order_products = PurchaseOrderProduct::where('purchase_order_id', $order->id)
//             ->whereNotIn('serial', $request->serial)
//             ->get();

//         //dd($prev_order_products);

//         foreach ($prev_order_products as $prev_order_product){
//             $saleOrderProduct = SalesOrderProduct::where('serial', $prev_order_product->serial)->first();
// //            if ($saleOrderProduct) {
// //                $message ='You Cannot Remove This Product' . $saleOrderProduct->serial;
// //                return redirect()->back()->withInput()->with('message', $message);
// //            }
//             $purchaseInventory = PurchaseInventory::where('id',$prev_order_product->purchase_inventory_id)->first();
//             $purchaseInventory->decrement('quantity',$prev_order_product->quantity);
//             $prev_order_product->delete();
//         }

//         PurchaseOrderProduct::where('purchase_order_id', $order->id)->delete();


//         $sub_total = 0;
//         foreach ($request->product_item as $key => $product_item_id) {

//             $productItem = ProductItem::where('name', $product_item_id)->where('type', $request->product_type)->first();
//             $product_category = ProductCategory::where('name', $request->product_category[$key])
//                 ->where('type',$request->product_type)
//                 ->first();

//             if (!$productItem) {

//                 $productItem = new ProductItem();
//                 $productItem->name = $request->product_item[$key];
//                 $productItem->type = $request->product_type;
//                 $productItem->unit_id = 1;
//                 $productItem->status = 1;
//                 $productItem->save();
//             }
//             if (!$product_category) {

//                 $product_category = new ProductCategory();
//                 $product_category->name = $request->product_category[$key];
//                 $product_category->type = $request->product_type;
//                 $product_category->status = 1;
//                 $product_category->save();
//             }
//             $purchase_order_product = PurchaseOrderProduct::where('purchase_order_id', $order->id)
//                 ->where('product_item_id', $productItem->id)
//                 ->where('product_category_id', $product_category->id)
//                 ->where('warehouse_id', $request->warehouse_id)
//                 ->first();

//             if ($purchase_order_product) {
//                 $purchase_order_product->update([
//                     'purchase_order_id' => $order->id,
//                     'product_type' => $request->product_type,
//                     'product_item_id' => $productItem->id,
//                     'product_category_id' => $product_category->id,
//                     'warehouse_id' => $request->warehouse_id,
//                     'date' => $request->date,
//                     'quantity' => $request->quantity[$key],
//                     'unit_price' => $request->unit_price[$key],
//                     'total' => $request->quantity[$key] * $request->unit_price[$key],
//                 ]);
//             }else {
//                 $purchase_order_product = PurchaseOrderProduct::create([
//                     'purchase_order_id' => $order->id,
//                     'product_type' => $request->product_type,
//                     'product_item_id' => $productItem->id,
//                     'product_category_id' => $product_category->id,
//                     'warehouse_id' => $request->warehouse_id,
//                     'date' => $request->date,
//                     'quantity' => $request->quantity[$key],
//                     'unit_price' => $request->unit_price[$key],
//                     'selling_price' => $request->selling_price[$key],
//                     'total' => $request->quantity[$key] * $request->unit_price[$key],
//                 ]);
//             }

//             $sub_total += $request->quantity[$key] * $request->unit_price[$key];

//             // Inventory Log
//             $log = PurchaseInventoryLog::create([
//                 'purchase_order_id' => $order->id,
//                 'product_item_id' => $productItem->id,
//                 'product_category_id' => $product_category->id,
//                 'warehouse_id' => $request->warehouse_id,
//                 'supplier_id' => $request->supplier_id,
//                 'type' => 1,
//                 'date' => $request->date,
//                 'quantity' => $request->quantity[$key],
//                 'unit_price' => $request->unit_price[$key],
//                 'selling_price' => $request->selling_price[$key],
//                 'sale_total' => $request->quantity[$key] * $request->selling_price[$key],
//                 'total' => $request->quantity[$key] * $request->unit_price[$key],
//                 'note' => 'Purchase Product',
//             ]);

//             $inventory = PurchaseInventory::where('product_item_id', $productItem->id)
//                 ->where('product_category_id', $product_category->id)
//                 ->where('warehouse_id', $request->warehouse_id)
//                 ->first();


//             if ($inventory) {
//                 $inventory->update([
//                     'product_item_id' => $productItem->id,
//                     'product_category_id' => $product_category->id,
//                     'warehouse_id' => $request->warehouse_id,
//                     'quantity' => $inventory->quantity+$request->quantity[$key],
//                     'unit_price' => $request->unit_price[$key],
//                     'selling_price' => $request->selling_price[$key],
//                     'total' => $request->quantity[$key] * $request->unit_price[$key],
//                 ]);
//             }else {
//                 $inventory = PurchaseInventory::create([
//                     'product_item_id' => $productItem->id,
//                     'product_category_id' => $product_category->id,
//                     'warehouse_id' => $request->warehouse_id,
//                     'quantity' => $request->quantity[$key],
//                     'unit_price' => $request->unit_price[$key],
//                     'selling_price' => $request->selling_price[$key],
//                     'total' => $request->quantity[$key] * $request->unit_price[$key],
//                 ]);
//             }
//             $log->update([
//                 'purchase_inventory_id'=>$inventory->id,
//                 'serial'=> str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
//             ]);
//             // dd($purchase_order_product);

//             $purchase_order_product->update(['purchase_inventory_id'=>$inventory->id]);
//             $totalAmount = PurchaseOrderProduct::where('purchase_inventory_id', $inventory->id)->sum('unit_price');
//             $totalQuantity = PurchaseOrderProduct::where('purchase_inventory_id', $inventory->id)->count();
//             $inventory->update([
//                 'quantity' => $inventory->in_product - $inventory->out_product,
//                 'serial' => str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
//                 'avg_unit_price' => $totalAmount / $totalQuantity,
//             ]);
//             $purchase_order_product->update(['serial' => $inventory->serial]);

//         }

//         if ($order->paid < $request->paid) {
//             $paidIncrement = $request->paid - $order->paid;
//             $purchasePaymentCheck = PurchasePayment::where('purchase_order_id',$order->id)->where('transaction_method',2)->first();
//             if ($purchasePaymentCheck) {
//                 BankAccount::find($purchasePaymentCheck->bank_account_id)->decrement('balance', $paidIncrement);
//             }else{
//                 Cash::first()->decrement('amount', $paidIncrement);
//             }
//         }else{
//             $paidDecrement = $order->paid - $request->paid;
//             $purchasePaymentCheck = PurchasePayment::where('purchase_order_id',$order->id)->where('transaction_method',2)->first();
//             if ($purchasePaymentCheck) {
//                 BankAccount::find($purchasePaymentCheck->bank_account_id)->increment('balance', $paidDecrement);
//             }else{
//                 Cash::first()->increment('amount', $paidDecrement);
//             }
//         }

//         $total = $sub_total + $request->transport_cost - $request->discount;
//         $order->total = $total;
//         $order->paid = $request->paid;
//         $order->due = $total - $request->paid;
//         $order->save();

//         $purchasePayment = PurchasePayment::where('purchase_order_id',$order->id)->first();
//         if ($purchasePayment) {
//             $purchasePayment->amount = $request->paid;
//             $purchasePayment->supplier_id = $request->supplier_id;
//             $purchasePayment->save();
//         }else{
//             $payment = new PurchasePayment();
//             $payment->purchase_order_id = $order->id;
//             $payment->supplier_id = $request->supplier_id;
//             $payment->transaction_method = 1;
//             $payment->amount = $request->paid;
//             $payment->date = $request->date;
//             $payment->save();
//         }

//         if ($purchasePayment) {
//             $log = TransactionLog::where('purchase_payment_id',$purchasePayment->id)->first();
//             if ($log) {
//                 $log->amount = $request->paid??'';
//                 $log->supplier_id = $request->supplier_id;
//                 $log->save();
//             }
//         }else{
//             $log = new TransactionLog();
//             $log->date = $request->date;
//             $log->particular = 'Paid to ' . $order->supplier->name . ' for ' . $order->order_no;
//             $log->transaction_type = 3;
//             $log->transaction_method = 1;
//             $log->account_head_type_id = 1;
//             $log->account_head_sub_type_id = 1;
//             $log->amount = $request->paid;
//             $log->purchase_payment_id = $payment->id;
//             $log->save();
//         }

//         return redirect()->route('purchase_receipt.details', ['order' => $order->id]);
//     }
    public function purchaseOrderEditPost(Request $request, PurchaseOrder $order) {

       // dd($request->all());

        $rules = [
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
            'product_type' => 'required',
            'date' => 'required|date',
            'transport_cost' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'product_item.*' => 'required',
            'product_category.*' => 'required',
            'quantity.*' => 'required|numeric|min:0',
            'unit_price.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',
        ];

        $validator = $request->validate($rules);

        $order->supplier_id = $request->supplier_id;
        $order->product_type = $request->product_type;
        $order->warehouse_id = $request->warehouse_id;
        $order->transport_cost = $request->transport_cost;
        $order->discount_percentage = $request->discount_percentage;
        $order->discount = $request->discount;
        $order->date = $request->date;
        $order->save();

        foreach ($request->product_item as $key => $product_item_id) {
            $previousSerials[] = $request->serial;
        }

        // Remove previous
        $prev_order_products = PurchaseOrderProduct::where('purchase_order_id', $order->id)
            ->whereNotIn('serial', $request->serial)
            ->get();

        //dd($prev_order_products);

        foreach ($prev_order_products as $prev_order_product){
            $saleOrderProduct = SalesOrderProduct::where('serial', $prev_order_product->serial)->first();
            if ($saleOrderProduct) {
                $message ='You Cannot Remove This Product' .'-'. $saleOrderProduct->serial;
                return redirect()->back()->withInput()->with('message', $message);
            }
            $purchaseInventory = PurchaseInventory::where('id',$prev_order_product->purchase_inventory_id)->first();
            $purchaseInventory->decrement('quantity',$prev_order_product->quantity);
            $prev_order_product->delete();
        }

        PurchaseInventoryLog::where('purchase_order_id', $order->id)->delete();

        PurchaseOrderProduct::where('purchase_order_id', $order->id)->delete();


        $sub_total = 0;
        foreach ($request->product_item as $key => $product_item_id) {

            $productItem = ProductItem::where('name', $product_item_id)->where('type', $request->product_type)->first();
            $product_category = ProductCategory::where('name', $request->product_category[$key])
                ->where('type',$request->product_type)
                ->first();

            if (!$productItem) {

                $productItem = new ProductItem();
                $productItem->name = $request->product_item[$key];
                $productItem->type = $request->product_type;
                $productItem->unit_id = 1;
                $productItem->status = 1;
                $productItem->save();
            }
            if (!$product_category) {

                $product_category = new ProductCategory();
                $product_category->name = $request->product_category[$key];
                $product_category->type = $request->product_type;
                $product_category->status = 1;
                $product_category->save();
            }
            $purchase_order_product = PurchaseOrderProduct::where('purchase_order_id', $order->id)
                ->where('product_item_id', $productItem->id)
                ->where('product_category_id', $product_category->id)
                ->where('warehouse_id', $request->warehouse_id)
                ->first();

            if ($purchase_order_product) {
                $purchase_order_product->update([
                    'purchase_order_id' => $order->id,
                    'product_type' => $request->product_type,
                    'product_item_id' => $productItem->id,
                    'product_category_id' => $product_category->id,
                    'warehouse_id' => $request->warehouse_id,
                    'date' => $request->date,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'total' => $request->quantity[$key] * $request->unit_price[$key],
                ]);
            }else {
                $purchase_order_product = PurchaseOrderProduct::create([
                    'purchase_order_id' => $order->id,
                    'product_type' => $request->product_type,
                    'product_item_id' => $productItem->id,
                    'product_category_id' => $product_category->id,
                    'warehouse_id' => $request->warehouse_id,
                    'date' => $request->date,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'selling_price' => $request->selling_price[$key],
                    'total' => $request->quantity[$key] * $request->unit_price[$key],
                ]);
            }

            $sub_total += $request->quantity[$key] * $request->unit_price[$key];

            // Inventory Log
            $log = PurchaseInventoryLog::create([
                'purchase_order_id' => $order->id,
                'product_item_id' => $productItem->id,
                'product_category_id' => $product_category->id,
                'warehouse_id' => $request->warehouse_id,
                'supplier_id' => $request->supplier_id,
                'type' => 1,
                'date' => $request->date,
                'quantity' => $request->quantity[$key],
                'unit_price' => $request->unit_price[$key],
                'selling_price' => $request->selling_price[$key],
                'sale_total' => $request->quantity[$key] * $request->selling_price[$key],
                'total' => $request->quantity[$key] * $request->unit_price[$key],
                'note' => 'Purchase Product',
            ]);

            $inventory = PurchaseInventory::where('product_item_id', $productItem->id)
                ->where('product_category_id', $product_category->id)
                ->where('warehouse_id', $request->warehouse_id)
                ->first();


            if ($inventory) {
                $inventory->update([
                    'product_item_id' => $productItem->id,
                    'product_category_id' => $product_category->id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => $inventory->quantity+$request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'selling_price' => $request->selling_price[$key],
                    'total' => $request->quantity[$key] * $request->unit_price[$key],
                ]);
            }else {
                $inventory = PurchaseInventory::create([
                    'product_item_id' => $productItem->id,
                    'product_category_id' => $product_category->id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'selling_price' => $request->selling_price[$key],
                    'total' => $request->quantity[$key] * $request->unit_price[$key],
                ]);
            }
            $log->update([
                'purchase_inventory_id'=>$inventory->id,
                'serial'=> str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
            ]);
            // dd($purchase_order_product);

            $purchase_order_product->update(['purchase_inventory_id'=>$inventory->id]);
            $totalAmount = PurchaseOrderProduct::where('purchase_inventory_id', $inventory->id)->sum('unit_price');
            $totalQuantity = PurchaseOrderProduct::where('purchase_inventory_id', $inventory->id)->count();
            $inventory->update([
                'quantity' => $inventory->in_product - $inventory->out_product,
                'serial' => str_pad($inventory->id, 7, 0, STR_PAD_LEFT),
                'avg_unit_price' => $totalAmount / $totalQuantity,
            ]);
            $purchase_order_product->update(['serial' => $inventory->serial]);

        }

        if ($order->paid < $request->paid) {
            $paidIncrement = $request->paid - $order->paid;
            $purchasePaymentCheck = PurchasePayment::where('purchase_order_id',$order->id)->where('transaction_method',2)->first();
            if ($purchasePaymentCheck) {
                BankAccount::find($purchasePaymentCheck->bank_account_id)->decrement('balance', $paidIncrement);
            }else{
                Cash::first()->decrement('amount', $paidIncrement);
            }
        }else{
            $paidDecrement = $order->paid - $request->paid;
            $purchasePaymentCheck = PurchasePayment::where('purchase_order_id',$order->id)->where('transaction_method',2)->first();
            if ($purchasePaymentCheck) {
                BankAccount::find($purchasePaymentCheck->bank_account_id)->increment('balance', $paidDecrement);
            }else{
                Cash::first()->increment('amount', $paidDecrement);
            }
        }

        $total = $sub_total + $request->transport_cost - $request->discount;
        $order->total = $total;
        $order->paid = $request->paid;
        $order->due = $total - $request->paid;
        $order->save();

        $purchasePayment = PurchasePayment::where('purchase_order_id',$order->id)->first();
        if ($purchasePayment) {
            $purchasePayment->amount = $request->paid;
            $purchasePayment->supplier_id = $request->supplier_id;
            $purchasePayment->save();
        }else{
            $payment = new PurchasePayment();
            $payment->purchase_order_id = $order->id;
            $payment->supplier_id = $request->supplier_id;
            $payment->transaction_method = 1;
            $payment->amount = $request->paid;
            $payment->date = $request->date;
            $payment->save();
        }

        if ($purchasePayment) {
            $log = TransactionLog::where('purchase_payment_id',$purchasePayment->id)->first();
            if ($log) {
                $log->amount = $request->paid??'';
                $log->supplier_id = $request->supplier_id;
                $log->save();
            }
        }else{
            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to ' . $order->supplier->name . ' for ' . $order->order_no;
            $log->transaction_type = 3;
            $log->transaction_method = 1;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->amount = $request->paid;
            $log->purchase_payment_id = $payment->id;
            $log->save();
        }

        return redirect()->route('purchase_receipt.details', ['order' => $order->id]);
    }

    public function purchaseDelete(Request $request){


        $purchaseOrderProducts = PurchaseOrderProduct::where('purchase_order_id', $request->id)->get();

        foreach ($purchaseOrderProducts as $purchaseOrderProduct){
            $inventories = PurchaseInventory::where('product_item_id', $purchaseOrderProduct->product_item_id)
                ->where('product_category_id', $purchaseOrderProduct->product_category_id)
                ->where('warehouse_id', $purchaseOrderProduct->warehouse_id)
                ->where('quantity','>=', $purchaseOrderProduct->quantity)
                ->get();

            if ($inventories->count() > 0) {
                foreach ($inventories as $inventory){
                    $inventory->update([
                        'quantity' => $inventory->quantity - $purchaseOrderProduct->quantity,
                    ]);
                    //$inventory->delete();
                }
                PurchaseOrder::where('id',$request->id)->delete();
                $purchaseOrderProduct->delete();
                PurchaseInventoryLog::where('purchase_order_id',$request->id)->delete();
                $purchasePayment = PurchasePayment::where('purchase_order_id',$request->id)->first();
                if ($purchasePayment) {
                    if ($purchasePayment->transaction_method == 1) {
                        Cash::first()->increment('amount', $purchasePayment->amount);
                    }else{
                        BankAccount::find($purchasePayment->bank_account_id)->increment('balance', $purchasePayment->amount);
                    }
                    TransactionLog::where('purchase_payment_id',$purchasePayment->id)->delete();
                    $purchasePayment->delete();
                }
            }else{

                return redirect(route('purchase_receipt.all'))->with('message','This Order Delete Impossible');
            }
        }
        return redirect(route('purchase_receipt.all'))->with('message','Purchase Order Delete Successfully');
    }

    public function purchaseReceipt() {
        return view('purchase.receipt.all');
    }


    public function purchaseReceiptDetails(PurchaseOrder $order) {
        return view('purchase.receipt.details', compact('order'));
    }

    public function purchaseReceiptPrint(PurchaseOrder $order) {
        return view('purchase.receipt.print', compact('order'));
    }

    public function qrCode(PurchaseOrder $order) {
        return view('purchase.receipt.qr_code', compact('order'));
    }

    public function qrCodePrint(PurchaseOrder $order) {

        return view('purchase.receipt.qr_code_print', compact('order'));
    }
    public function qrSingleCodePrint($order) {
        $product = PurchaseOrderProduct::where('id',$order)->first();

        return view('purchase.receipt.qr_code_print', compact('product'));
    }

    public function purchasePaymentDetails(PurchasePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('purchase.receipt.payment_details', compact('payment'));
    }

    public function purchasePaymentPrint(PurchasePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('purchase.receipt.payment_print', compact('payment'));
    }

    public function supplierPayment() {
        $suppliers = Supplier::all();
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('purchase.supplier_payment.all', compact('suppliers', 'banks'));
    }

    public function supplierPaymentGetOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('due', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function supplierPaymentOrderDetails(Request $request) {
        $order = PurchaseOrder::where('id', $request->orderId)
            ->first()->toArray();

        return response()->json($order);
    }

    public function makePayment(Request $request) {
        $rules = [
            // 'order' => 'required',
            'supplier_id' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        // if ($request->order != '') {
        //     $order = PurchaseOrder::find($request->order);
        //     $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        // }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->payment_type == 1) {
                $cash = Cash::first();
                if ($request->amount > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                if ($request->account != '') {
                    $account = BankAccount::find($request->account);

                    if ($request->amount > $account->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // $order = PurchaseOrder::find($request->order);
        $supplier = Supplier::find($request->supplier_id);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new PurchasePayment();
            $payment->purchase_order_id = null;
            $payment->supplier_id = $request->supplier_id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->decrement('amount', $request->amount);
            else
                MobileBanking::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$supplier->name??'';
            $log->transaction_type = 3;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->supplier_id = $request->supplier_id;
            $log->purchase_payment_id = $payment->id;
            $log->save();

        }elseif ($request->payment_type == 4){

            $payment = new PurchasePayment();
            $payment->purchase_order_id = null;
            $payment->supplier_id = $request->supplier_id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->user_id = Auth::user()->id;
            $payment->save();

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Account Adjustment Discount to '.$supplier->name;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 209;
            $log->account_head_sub_type_id = 18;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
            $log->save();

        }else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/purchase_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/purchase_payment_cheque/'.$filename;
            }

            $payment = new PurchasePayment();
            $payment->purchase_order_id = null;
            $payment->supplier_id = $request->supplier_id;
            $payment->transaction_method = 2;
            $payment->bank_id = $request->bank;
            $payment->branch_id = $request->branch;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            BankAccount::find($request->account)->decrement('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$supplier->name??'';
            $log->transaction_type = 3;
            $log->transaction_method = 2;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->supplier_id = $request->supplier_id;
            $log->purchase_payment_id = $payment->id;
            $log->save();
        }

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function purchaseStockTransfer()
    {
        $warehouses = Warehouse::where('status',1)->get();
        $source_warehouses = Warehouse::where('status',1)->orderBy('id','desc')->get();
        $products = PurchaseInventory::where('quantity', '>', 0)->get();

        return view('purchase.inventory.stock_transfer', compact('warehouses','products','source_warehouses'));
    }

    public function purchaseStockTransferPost(Request $request)
    {
        $request->validate([
            'source_warehouse' => 'required',
            'target_warehouse' => 'required',
            'product.*' => 'required',
            'quantity.*' => 'required|min:1|numeric',
        ]);

        $source_warehouse = Warehouse::find($request->source_warehouse);
        $target_warehouse = Warehouse::find($request->target_warehouse);

        if ($source_warehouse->id == $target_warehouse->id) {
            $message = 'Source Warehouse And Target Warehouse Not to be Same';
            return redirect()->back()->withInput()->with('error', $message);
        }

        $available = true;
        $message = '';
        $counter = 0;

        if ($request->product) {
            foreach ($request->product as $productId) {
                $inventory = PurchaseInventory::where('id',$request->product[$counter])
                    ->where('warehouse_id', $source_warehouse->id)
                    ->first();

                if ($inventory) {
                    if ($request->quantity[$counter] > $inventory->quantity) {
                        $available = false;
                        $message = 'Insufficient ' . $inventory->productItem->name;
                        break;
                    }
                    $counter++;
                }else{
                    $message_one = 'Target Warehouse And Product Warehouse Not to be same';
                    return redirect()->back()->withInput()->with('error', $message_one);
                }

            }
        }

        if (!$available) {
            return redirect()->back()->withInput()->with('error', $message);
        }

        $order = new StockTransferOrder();
        $order->sourch_warehouse_id = $source_warehouse->id;
        $order->target_warehouse_id = $target_warehouse->id;
        $order->date = date('Y-m-d');
        $order->user_id = Auth::id();
        $order->save();
        $order->order_no = str_pad($order->id, 5, 0, STR_PAD_LEFT);
        $order->save();

        $counter = 0;

        if ($request->product) {

            foreach ($request->product as $reqProduct) {

                $sourceInventory = PurchaseInventory::where('id',$request->product[$counter])
                    ->where('warehouse_id', $source_warehouse->id)
                    ->first();

                if ($sourceInventory) {
                    $sourceInventory->decrement('quantity', $request->quantity[$counter]);

                    $purchaseInventoryLog = new PurchaseInventoryLog();
                    $purchaseInventoryLog->stock_transfer_order_id = $order->id;
                    $purchaseInventoryLog->serial = $sourceInventory->serial;
                    $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                    $purchaseInventoryLog->product_item_id = $sourceInventory->product_item_id;
                    $purchaseInventoryLog->product_category_id = $sourceInventory->product_category_id;
                    $purchaseInventoryLog->type = 10;
                    $purchaseInventoryLog->date = date('Y-m-d');
                    $purchaseInventoryLog->warehouse_id = $sourceInventory->warehouse_id;
                    $purchaseInventoryLog->quantity = $request->quantity[$counter];
                    $purchaseInventoryLog->unit_price = $sourceInventory->unit_price;
                    $purchaseInventoryLog->selling_price = $sourceInventory->selling_price;
                    $purchaseInventoryLog->purchase_inventory_id = $sourceInventory->id;
                    $purchaseInventoryLog->total = $sourceInventory->unit_price * $request->quantity[$counter];
                    $purchaseInventoryLog->user_id = Auth::id();
                    $purchaseInventoryLog->stock_transfer_status = 1;
                    $purchaseInventoryLog->save();
                }

                $targetInventory = PurchaseInventory::where('serial',$sourceInventory->serial)
                    ->where('product_category_id', $sourceInventory->product_category_id)
                    ->where('warehouse_id', $target_warehouse->id)
                    ->first();

                if ($targetInventory) {
                    $targetInventory->increment('quantity', $request->quantity[$counter]);
                    $purchaseInventoryLog = new PurchaseInventoryLog();
                    $purchaseInventoryLog->stock_transfer_order_id = $order->id;
                    $purchaseInventoryLog->serial = $targetInventory->serial;
                    $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                    $purchaseInventoryLog->product_item_id = $targetInventory->product_item_id;
                    $purchaseInventoryLog->product_category_id = $targetInventory->product_category_id;
                    $purchaseInventoryLog->type = 11;
                    $purchaseInventoryLog->date = date('Y-m-d');
                    $purchaseInventoryLog->warehouse_id = $targetInventory->warehouse_id;
                    $purchaseInventoryLog->quantity = $request->quantity[$counter];
                    $purchaseInventoryLog->unit_price = $targetInventory->unit_price;
                    $purchaseInventoryLog->selling_price = $targetInventory->selling_price;
                    $purchaseInventoryLog->purchase_inventory_id = $targetInventory->id;
                    $purchaseInventoryLog->total = $targetInventory->unit_price * $request->quantity[$counter];
                    $purchaseInventoryLog->user_id = Auth::id();
                    $purchaseInventoryLog->stock_transfer_status = 1;
                    $purchaseInventoryLog->save();
                }else{

                    $inventory = PurchaseInventory::create([
                        'product_item_id' => $sourceInventory->product_item_id,
                        'product_category_id' => $sourceInventory->product_category_id,
                        'warehouse_id' => $target_warehouse->id,
                        'serial' => $sourceInventory->serial,
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $sourceInventory->unit_price,
                        'avg_unit_price' => $sourceInventory->unit_price,
                        'selling_price' => $sourceInventory->selling_price,
                        'total' => $request->quantity[$counter] * $sourceInventory->unit_price,
                    ]);

                    $purchaseInventoryLog = new PurchaseInventoryLog();
                    $purchaseInventoryLog->stock_transfer_order_id = $order->id;
                    $purchaseInventoryLog->serial = $inventory->serial;
                    $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                    $purchaseInventoryLog->product_item_id = $inventory->product_item_id;
                    $purchaseInventoryLog->product_category_id = $inventory->product_category_id;
                    $purchaseInventoryLog->type = 11;
                    $purchaseInventoryLog->date = date('Y-m-d');
                    $purchaseInventoryLog->warehouse_id = $inventory->warehouse_id;
                    $purchaseInventoryLog->quantity = $request->quantity[$counter];
                    $purchaseInventoryLog->unit_price = $inventory->unit_price;
                    $purchaseInventoryLog->selling_price = $inventory->selling_price;
                    $purchaseInventoryLog->total = $request->quantity[$counter] * $inventory->unit_price;
                    $purchaseInventoryLog->purchase_inventory_id = $inventory->id;
                    $purchaseInventoryLog->user_id = Auth::id();
                    $purchaseInventoryLog->stock_transfer_status = 1;
                    $purchaseInventoryLog->save();
                }
                $counter++;
            }
        }
        return redirect()->route('stock_transfer_details', ['order' => $order->id]);
    }

    public function stockTransferInvoice(){
        return view('purchase.inventory.transfer_invoice');
    }
    public function stockTransferChallan(StockTransferOrder $order){
        return view('purchase.inventory.transfer_challan',compact('order'));
    }
    public function stockTransferDetails(StockTransferOrder $order){
        return view('purchase.inventory.transfer_details',compact('order'));
    }
     public function stockTransferEdit(StockTransferOrder $order){
         $warehouses = Warehouse::where('status',1)->get();
         $source_warehouses = Warehouse::where('status',1)->orderBy('id','desc')->get();
         $products = PurchaseInventory::get();
         return view('purchase.inventory.transfer_edit', compact('warehouses','products','source_warehouses','order'));

     }
     public function stockTransferEditPost(StockTransferOrder $order , Request $request){

         $request->validate([
             'source_warehouse' => 'required',
             'target_warehouse' => 'required',
             'product.*' => 'required',
             'quantity.*' => 'required|min:1|numeric',
         ]);

         $source_warehouse = Warehouse::find($request->source_warehouse);
         $target_warehouse = Warehouse::find($request->target_warehouse);

         if ($source_warehouse->id == $target_warehouse->id) {
             $message = 'Source Warehouse And Target Warehouse Not to be Same';
             return redirect()->back()->withInput()->with('error', $message);
         }

         $available = true;
         $message = '';
         $counter = 0;

         if ($request->product) {
             foreach ($request->product as $productId) {
                 //$inventory = PurchaseInventory::where('id',$request->product[$counter])
                 $inventory = PurchaseInventory::where('id',1303)
                     ->where('warehouse_id', $source_warehouse->id)
                     ->first();

                 $purchaseInventoryLog = PurchaseInventoryLog::where('purchase_inventory_id',1303)
                     ->where('type', 10)
                     ->first();

                 if ($inventory && $purchaseInventoryLog){
                     $totalQuantity = $purchaseInventoryLog->quantity + $inventory->quantity;
                 }else{
                     $totalQuantity = $inventory->quantity;
                 }

                 if ($inventory) {
                     if ($request->quantity[$counter] > $totalQuantity) {
                         $available = false;
                         $message = 'Insufficient ' . $inventory->productItem->name;
                         break;
                     }
                     $counter++;
                 }else{
                     $message_one = 'Target Warehouse And Product Warehouse Not to be same';
                     return redirect()->back()->withInput()->with('error', $message_one);
                 }

             }
         }

         if (!$available) {
             return redirect()->back()->withInput()->with('error', $message);
         }

         foreach ($order->products as $orderProduct){
             $sourceInventory = PurchaseInventory::where('serial',$orderProduct->serial)
                 ->where('warehouse_id',$source_warehouse->id)
                 ->first();
             $sourceInventory->increment('quantity',$orderProduct->quantity);

             $sourceInventory = PurchaseInventory::where('serial',$orderProduct->serial)
                 ->where('warehouse_id',$target_warehouse->id)
                 ->first();
             $sourceInventory->decrement('quantity',$orderProduct->quantity);

             $orderProduct->delete();
         }

         //dd($request->all());

         $order->sourch_warehouse_id = $source_warehouse->id;
         $order->target_warehouse_id = $target_warehouse->id;
         $order->date = date('Y-m-d');
         $order->user_id = Auth::id();
         $order->save();

         $counter = 0;

         if ($request->product) {

             foreach ($request->product as $reqProduct) {

                 $sourceInventory = PurchaseInventory::where('id',$request->product[$counter])
                     ->where('warehouse_id', $source_warehouse->id)
                     ->first();

                 if ($sourceInventory) {
                     $sourceInventory->decrement('quantity', $request->quantity[$counter]);

                     $purchaseInventoryLog = new PurchaseInventoryLog();
                     $purchaseInventoryLog->stock_transfer_order_id = $order->id;
                     $purchaseInventoryLog->serial = $sourceInventory->serial;
                     $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                     $purchaseInventoryLog->product_item_id = $sourceInventory->product_item_id;
                     $purchaseInventoryLog->product_category_id = $sourceInventory->product_category_id;
                     $purchaseInventoryLog->type = 10;
                     $purchaseInventoryLog->date = date('Y-m-d');
                     $purchaseInventoryLog->warehouse_id = $sourceInventory->warehouse_id;
                     $purchaseInventoryLog->quantity = $request->quantity[$counter];
                     $purchaseInventoryLog->unit_price = $sourceInventory->unit_price;
                     $purchaseInventoryLog->selling_price = $sourceInventory->selling_price;
                     $purchaseInventoryLog->purchase_inventory_id = $sourceInventory->id;
                     $purchaseInventoryLog->total = $sourceInventory->unit_price * $request->quantity[$counter];
                     $purchaseInventoryLog->user_id = Auth::id();
                     $purchaseInventoryLog->stock_transfer_status = 1;
                     $purchaseInventoryLog->save();
                 }

                 $targetInventory = PurchaseInventory::where('serial',$sourceInventory->serial)
                     ->where('product_category_id', $sourceInventory->product_category_id)
                     ->where('warehouse_id', $target_warehouse->id)
                     ->first();

                 if ($targetInventory) {
                     $targetInventory->increment('quantity', $request->quantity[$counter]);

                     $purchaseInventoryLog = new PurchaseInventoryLog();
                     $purchaseInventoryLog->stock_transfer_order_id = $order->id;
                     $purchaseInventoryLog->serial = $targetInventory->serial;
                     $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                     $purchaseInventoryLog->product_item_id = $targetInventory->product_item_id;
                     $purchaseInventoryLog->product_category_id = $targetInventory->product_category_id;
                     $purchaseInventoryLog->type = 11;
                     $purchaseInventoryLog->date = date('Y-m-d');
                     $purchaseInventoryLog->warehouse_id = $targetInventory->warehouse_id;
                     $purchaseInventoryLog->quantity = $request->quantity[$counter];
                     $purchaseInventoryLog->unit_price = $targetInventory->unit_price;
                     $purchaseInventoryLog->selling_price = $targetInventory->selling_price;
                     $purchaseInventoryLog->purchase_inventory_id = $targetInventory->id;
                     $purchaseInventoryLog->total = $targetInventory->unit_price * $request->quantity[$counter];
                     $purchaseInventoryLog->user_id = Auth::id();
                     $purchaseInventoryLog->stock_transfer_status = 1;
                     $purchaseInventoryLog->save();
                 }else{

                     $inventory = PurchaseInventory::create([
                         'product_item_id' => $sourceInventory->product_item_id,
                         'product_category_id' => $sourceInventory->product_category_id,
                         'warehouse_id' => $target_warehouse->id,
                         'serial' => $sourceInventory->serial,
                         'quantity' => $request->quantity[$counter],
                         'unit_price' => $sourceInventory->unit_price,
                         'avg_unit_price' => $sourceInventory->unit_price,
                         'selling_price' => $sourceInventory->selling_price,
                         'total' => $request->quantity[$counter] * $sourceInventory->unit_price,
                     ]);

                     $purchaseInventoryLog = new PurchaseInventoryLog();
                     $purchaseInventoryLog->stock_transfer_order_id = $order->id;
                     $purchaseInventoryLog->serial = $inventory->serial;
                     $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                     $purchaseInventoryLog->product_item_id = $inventory->product_item_id;
                     $purchaseInventoryLog->product_category_id = $inventory->product_category_id;
                     $purchaseInventoryLog->type = 11;
                     $purchaseInventoryLog->date = date('Y-m-d');
                     $purchaseInventoryLog->warehouse_id = $inventory->warehouse_id;
                     $purchaseInventoryLog->quantity = $request->quantity[$counter];
                     $purchaseInventoryLog->unit_price = $inventory->unit_price;
                     $purchaseInventoryLog->selling_price = $inventory->selling_price;
                     $purchaseInventoryLog->total = $request->quantity[$counter] * $inventory->unit_price;
                     $purchaseInventoryLog->purchase_inventory_id = $inventory->id;
                     $purchaseInventoryLog->user_id = Auth::id();
                     $purchaseInventoryLog->stock_transfer_status = 1;
                     $purchaseInventoryLog->save();
                 }
                 $counter++;
             }
         }
         return redirect()->route('stock_transfer_details', ['order' => $order->id]);
     }
    public function stockTransferDatatable() {

        $query = StockTransferOrder::with('sourchWarehouse','targetWarehouse')->orderBy('created_at', 'desc')->latest();

        return DataTables::eloquent($query)
            ->addColumn('source_warehouse', function(StockTransferOrder $order) {
                return $order->sourchWarehouse->name??'';
            })
            ->addColumn('target_warehouse', function(StockTransferOrder $order) {
                return $order->targetWarehouse->name??'';
            })
            ->addColumn('quantity', function (StockTransferOrder $order) {
                return $order->quantity() ?? '';
            })
            ->addColumn('action', function(StockTransferOrder $order) {
                $btn = '<a href="'.route('stock_transfer_challan', ['order' => $order->id]).'" class="btn btn-primary btn-sm" target="_blank">Challan</a> ';
                $btn .= '<a href="'.route('stock_transfer_details', ['order' => $order->id]).'" class="btn btn-info btn-sm">Details</a> ';
                $btn .= '<a href="'.route('stock_transfer_edit', ['order' => $order->id]).'" class="btn btn-warning btn-sm"> Edit </a> ';
                //$btn .= '<a role="button" class="btn btn-danger btn-sm btnDelete" data-id="'.$order->id.'"> Delete </a>';
                return $btn;
            })

            ->editColumn('date', function(StockTransferOrder $order) {
                return $order->date;
            })

            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function transferChallanPrint(StockTransferOrder $order){
        return view('purchase.inventory.transfer_challan_print',compact('order'));
    }

    public function purchaseInventory() {
        return view('purchase.inventory.all');
    }

    public function purchaseInventoryDetails(PurchaseInventory $purchase_inventory) {
        // dd($warehouse);
        return view('purchase.inventory.details', compact('purchase_inventory'));
    }
    public function purchaseInventoryEdit(PurchaseInventory $purchase_inventory) {
        // dd($warehouse);
        return view('purchase.inventory.edit', compact('purchase_inventory'));
    }
    public function purchaseInventoryEditPost(Request $request){
        $request->validate([
            'product_item' => 'required',
            'product_category' => 'required',
            'quantity' => 'required|min:0|numeric',
            'unit_price' => 'required|min:1|numeric',
            'selling_price' => 'required|min:1|numeric',
        ]);

        $supplier = PurchaseInventoryLog::where('purchase_inventory_id',$request->purchase_inventory_id)->first();
        $purchaseInventory = PurchaseInventory::where('id',$request->purchase_inventory_id)->first();
        if ($purchaseInventory->quantity == $request->quantity) {
            $purchaseInventory->update([
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'avg_unit_price' => $request->unit_price,
                'selling_price' => $request->selling_price,
                //'total' => $request->quantity * $request->selling_price,
            ]);
            return redirect()->route('purchase_inventory.all')->with('message','Inventory Edit Successfully');
        }else{
            if ($purchaseInventory->quantity <  $request->quantity) {
                $quantityIncrement = $request->quantity - $purchaseInventory->quantity;

                $purchaseInventoryLog = new PurchaseInventoryLog();
                $purchaseInventoryLog->serial = $purchaseInventory->serial;
                $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                $purchaseInventoryLog->product_item_id = $purchaseInventory->product_item_id;
                $purchaseInventoryLog->product_category_id = $purchaseInventory->product_category_id;
                $purchaseInventoryLog->type = 1;
                $purchaseInventoryLog->date = date('Y-m-d');
                $purchaseInventoryLog->warehouse_id = $purchaseInventory->warehouse_id;
                $purchaseInventoryLog->quantity = $quantityIncrement;
                $purchaseInventoryLog->unit_price = $request->unit_price;
                $purchaseInventoryLog->selling_price = $request->selling_price;
                $purchaseInventoryLog->sale_total = $request->selling_price * $quantityIncrement;
                $purchaseInventoryLog->total = $quantityIncrement * $request->unit_price;
                $purchaseInventoryLog->supplier_id = $supplier->supplier_id??'';
                $purchaseInventoryLog->purchase_order_id = $supplier->purchase_order_id;
                $purchaseInventoryLog->purchase_inventory_id = $purchaseInventory->id;
                $purchaseInventoryLog->note = 'Inventory Edit';
                $purchaseInventoryLog->stock_type = 3;
                $purchaseInventoryLog->user_id = Auth::id();
                $purchaseInventoryLog->save();
            }else{
                $quantityIncrement = $purchaseInventory->quantity - $request->quantity;

                $purchaseInventoryLog = new PurchaseInventoryLog();
                $purchaseInventoryLog->serial = $purchaseInventory->serial;
                $purchaseInventoryLog->company_branch_id = Auth::user()->company_branch_id;
                $purchaseInventoryLog->product_item_id = $purchaseInventory->product_item_id;
                $purchaseInventoryLog->product_category_id = $purchaseInventory->product_category_id;
                $purchaseInventoryLog->type = 2;
                $purchaseInventoryLog->date = date('Y-m-d');
                $purchaseInventoryLog->warehouse_id = $purchaseInventory->warehouse_id;
                $purchaseInventoryLog->quantity = $quantityIncrement;
                $purchaseInventoryLog->unit_price = $request->unit_price;
                $purchaseInventoryLog->selling_price = $request->selling_price;
                $purchaseInventoryLog->sale_total = $request->selling_price * $quantityIncrement;
                $purchaseInventoryLog->total = $quantityIncrement * $request->unit_price;
                $purchaseInventoryLog->supplier_id = $supplier->supplier_id;
                $purchaseInventoryLog->purchase_order_id = $supplier->purchase_order_id;
                $purchaseInventoryLog->purchase_inventory_id = $purchaseInventory->id;
                $purchaseInventoryLog->note = 'Inventory Edit';
                $purchaseInventoryLog->stock_type = 3;
                $purchaseInventoryLog->user_id = Auth::id();
                $purchaseInventoryLog->save();
            }
            $purchaseInventory->update([
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'avg_unit_price' => $request->unit_price,
                'selling_price' => $request->selling_price,
            ]);

            return redirect()->route('purchase_inventory.all')->with('message','Inventory Edit Successfully');
        }

    }

    public function purchaseInventoryBarcode(Request $request){
        $inventory = PurchaseInventory::find($request->purchase_inventory_id);
        $quantity = $request->quantity;
        return view('purchase.inventory.barcode', compact('inventory','quantity'));
    }

    public function purchaseReceiptDatatable() {
        $query = PurchaseOrder::with('supplier');

        return DataTables::eloquent($query)
            ->addColumn('supplier', function(PurchaseOrder $order) {
                return $order->supplier->name;
            })
            ->addColumn('quantity', function (PurchaseOrder $order) {
                return $order->quantity() ?? '';
            })
            ->addColumn('action', function(PurchaseOrder $order) {
                $btn = '<a href="'.route('purchase_receipt.details', ['order' => $order->id]).'" class="btn btn-primary btn-sm">View</a> <a href="'.route('purchase_receipt.qr_code', ['order' => $order->id]).'" class="btn btn-success btn-sm">BarCode</a>  ';
                $btn .= '<a href="'.route('purchase_order.edit', ['order' => $order->id]).'" class="btn btn-success btn-sm"> Edit </a> ';
                $btn .= '<a role="button" class="btn btn-danger btn-sm btnDelete" data-id="'.$order->id.'"> Delete </a>';
                return $btn;
            })
            ->addColumn('product_items', function(PurchaseOrder $order) {
                $products = '';
                foreach ($order->order_products as $key => $product) {
                    $products .= $product->productItem->name??'';
                    if(!empty($order->order_products[$key+1])){
                        $products .= ', ';
                    }
                }
                return $products;
            })
            ->filterColumn('product_items', function ($query, $keyword) {
                $order_products = ProductItem::where('name','like', '%'.$keyword.'%')->pluck('id');
                $order_ids = PurchaseOrderProduct::whereIn('product_item_id', $order_products)->distinct('purchase_order_id')->pluck('purchase_order_id');
                return $query->whereIn('id', $order_ids);
            })
            ->editColumn('date', function(PurchaseOrder $order) {
                return $order->date->format('j F, Y');
            })
            ->editColumn('total', function(PurchaseOrder $order) {
                return '৳'.number_format($order->total, 2);
            })
            ->editColumn('paid', function(PurchaseOrder $order) {
                return '৳'.number_format($order->paid, 2);
            })
            ->editColumn('due', function(PurchaseOrder $order) {
                return '৳'.number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseInventoryDatatable() {
        $query = PurchaseInventory::with('warehouse', 'productItem', 'productCategory','productColor','productSize');

        return DataTables::eloquent($query)
            ->addColumn('product_item', function(PurchaseInventory $inventory) {
                return $inventory->productItem->name??'';
            })
            // ->addColumn('type', function(PurchaseInventory $inventory) {
            //     if ($inventory->productItem->type == 1)
            //         return 'China';
            //     else
            //         return 'Bangla';
            // })
            ->addColumn('product_category', function(PurchaseInventory $inventory) {
                return $inventory->productCategory->name??'';
            })
            ->addColumn('warehouse', function(PurchaseInventory $inventory) {
                return $inventory->warehouse->name??'';
            })
            ->addColumn('action', function(PurchaseInventory $inventory) {
                //return '<a href="'.route('purchase_inventory.details', ['product' => $inventory->purchase_product_id, 'warehouse' => $inventory->warehouse->id]).'" class="btn btn-primary btn-sm">Details</a> <a href="'.route('purchase_inventory.qr_code', ['product' => $inventory->purchase_product_id, 'warehouse' => $inventory->warehouse->id]).'" class="btn btn-success btn-sm">QR Code</a>';
                $btn = '<a href="'.route('purchase_inventory.details', ['purchase_inventory' => $inventory->id]).'" class="btn btn-primary btn-sm">Details</a> ';
                $btn .= '<a role="button" class="btn btn-success btn-sm barcode_modal" data-id="'.$inventory->id.'" data-name="'.$inventory->productItem->name. '" data-code="' . $inventory->serial . '"> Barcode </a>';
                $btn .= '<a href="'.route('purchase_inventory.edit', ['purchase_inventory' => $inventory->id]).'" class="btn btn-primary btn-sm">Edit</a> ';
                return $btn;
            })
            ->editColumn('quantity', function(PurchaseInventory $inventory) {
                return number_format($inventory->quantity, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseInventoryDetailsDatatable(Request $request) {
        $query = PurchaseInventoryLog::where('purchase_inventory_id', request('purchase_inventory_id'))
            ->with('customer','saleOrder','supplier','productItem', 'productCategory','productColor',
                'productSize', 'purchaseInventory','saleReturnOrder','transferOrder');

        // $query = PurchaseInventoryLog::query();
        return DataTables::eloquent($query)
            ->addColumn('serial', function(PurchaseInventoryLog $log) {
                return $log->purchaseInventory->serial??'';
            })
            ->editColumn('date', function(PurchaseInventoryLog $log) {
                return $log->date->format('j F, Y');
            })
            ->editColumn('type', function(PurchaseInventoryLog $log) {
                if ($log->type == 1 && $log->return_status == 0 && $log->stock_type == 1)
                    return '<span class="label label-success">In</span>';
                elseif($log->type == 2 && $log->stock_type == 3 )
                    return '<span class="label label-danger">Inventory Edit Out</span>';
                elseif($log->type == 1 && $log->stock_type == 3 )
                    return '<span class="label label-success">Inventory Edit In</span>';
                elseif ($log->type == 2 && $log->return_status == 0)
                    return '<span class="label label-danger">Out</span>';
                elseif ($log->type == 3 && $log->return_status == 0)
                    return '<span class="label label-success">Add</span>';
                elseif($log->type == 1 && $log->return_status == 1 )
                    return '<span class="label label-success">Return</span>';
                elseif($log->type == 11 && $log->stock_transfer_status == 1 )
                    return '<span class="label label-success">Transfer In</span>';
                elseif($log->type == 10 && $log->stock_transfer_status == 1 )
                    return '<span class="label label-danger">Transfer Out</span>';
            })
            ->editColumn('quantity', function(PurchaseInventoryLog $log) {
                return number_format($log->quantity, 2);
            })
            ->editColumn('unit_price', function(PurchaseInventoryLog $log) {
                if ($log->unit_price)
                    return '৳'.number_format($log->unit_price, 2);
                else
                    return '';
            })
            ->editColumn('selling_price', function(PurchaseInventoryLog $log) {
                if ($log->selling_price)
                    return '৳'.number_format($log->selling_price, 2);
                else
                    return '';
            })
            ->editColumn('supplier', function(PurchaseInventoryLog $log) {
                if ($log->supplier)
                    return $log->supplier->name;
                else
                    return '';
            })
            ->addColumn('action', function(PurchaseInventoryLog $log) {
                if ($log->saleOrder) {
                    return '<a href="'.route('sale_receipt.details', ['order' => $log->saleOrder->id]).'" target="_blank">view Invoice</a>';
                }elseif ($log->purchaseOrder) {
                    return '<a href="'.route('purchase_receipt.details', ['order' => $log->purchaseOrder->id]).'" target="_blank">view Invoice</a>';
                }elseif($log->saleReturnOrder){
                    return '<a href="'.route('return_invoice.details', ['order' => $log->saleReturnOrder->id]).'" target="_blank">view Invoice</a>';
                }elseif($log->transferOrder){
                    return '<a href="'.route('stock_transfer_details', ['order' => $log->transferOrder->id]).'" target="_blank">view Invoice</a>';
                }else{
                    return '';
                }
            })
            ->editColumn('customer', function(PurchaseInventoryLog $log) {
                if ($log->saleOrder)
                    return $log->saleOrder->customer->name;
                elseif ($log->customer) {
                    return $log->customer->name;
                }else{
                    return '';
                }
            })
//            ->editColumn('order_no', function(PurchaseInventoryLog $log) {
//                if ($log->saleOrder)
//                    return $log->saleOrder->order_no;
//                else
//                    return '';
//            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['type', 'order'])
            ->filter(function ($query) {
                if (request()->has('date') && request('date') != '') {
                    $dates = explode(' - ', request('date'));
                    if (count($dates) == 2) {
                        $query->where('date', '>=', $dates[0]);
                        $query->where('date', '<=', $dates[1]);
                    }
                }

                if (request()->has('type') && request('type') != '') {
                    $query->where('type', request('type'));
                }
            })
            ->rawColumns(['action','type'])
            ->toJson();
    }


}
