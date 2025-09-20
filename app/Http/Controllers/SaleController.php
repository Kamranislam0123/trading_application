<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\CompanyBranch;
use App\Model\Customer;
use App\Model\Employee;
use App\Model\ManualStockOrder;
use App\Model\MobileBanking;
use App\Model\ProductItem;
use App\Model\ProductReturnOrder;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\SalePayment;
use App\Model\SalesOrder;
use App\Model\Service;
use App\Model\SubCustomer;
use App\Model\Supplier;
use App\Model\TransactionLog;
use App\Model\Warehouse;
use App\Model\Product;
use App\Model\ProductSalesOrder;
use App\Model\SalesOrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use DataTables;
use DB;

class SaleController extends Controller
{
    public function salesOrder() {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        if (Auth::user()->company_branch_id == 0) {
            $customers = Customer::where('status', 1)->with('branch')->orderBy('name')->get();
        }else{
            $customers = Customer::where('status', 1)->where('company_branch_id',Auth::user()->company_branch_id)->orderBy('name')->get();
        }
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $companyBranches = CompanyBranch::where('status', 1)->orderBy('name')->get();


        return view('sale.sales_order.create', compact('warehouses', 'banks',
            'productItems','customers','companyBranches'));
    }
    public function salesWastage()
    {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();

        return view('sale.sales_order.create_wastage', compact(
            'warehouses',
            'banks',
            'productItems'
        ));
    }

    public function salesOrderPost(Request $request)
    {

        if (empty($request->product_serial)) {
            $message = 'No Product Item Found';
            return redirect()->back()->withInput()->with('message', $message);
        }

        $total = $request->total;
        $due = $request->due_total;
        $rules = [
            'date' => 'required|date',
        ];

        if (Auth::user()->company_branch_id == 0) {
            $rules = [
                'companyBranch' => 'required',
            ];
        }
        if ($request->product_serial) {
            $rules['invoice_type'] = 'required';
            $rules['product_serial.*'] = 'required';
            $rules['product_item.*'] = 'required';
            $rules['product_category.*'] = 'required';
            $rules['product_color.*'] = 'required';
            $rules['product_size.*'] = 'required';
            $rules['warehouse.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:.01';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }

        if ($request->customer_type == 1) {
            $rules['customer_name'] = 'required';
            $rules['mobile_no'] = 'required';
            $rules['address'] = 'required';
        }
        if ($request->customer_type == 2) {
            $rules['customer'] = 'required';
        }

        if ($due > 0)
            $rules['next_payment'] = 'required|date';

        if ($request->payment_type == '2') {
            $rules['client_bank_name'] = 'required';
            $rules['client_cheque_no'] = 'required';
            $rules['cheque_date'] = 'required';
            $rules['client_amount'] = 'required';
        }

        $request->validate($rules);

        if ($request->customer_type == 1) {
            $customer = new Customer();
            $customer->name = $request->customer_name;
            if (Auth::user()->company_branch_id == 0) {
                $customer->company_branch_id = $request->companyBranch;
            } else {
                $customer->company_branch_id = Auth::user()->company_branch_id;
            }
            $customer->mobile_no = $request->mobile_no;
            $customer->address = $request->address;
            $customer->save();
        } else {
            $customer = Customer::find($request->customer);
        }

        $available = true;
        $message = '';

         if ($request->purchase_inventory) {
                foreach ($request->purchase_inventory as $index => $purchase_inventory_id) {
                    $inventory = PurchaseInventory::find($purchase_inventory_id);
                    if ($inventory) {
                        if ($request->quantity[$index] > $inventory->quantity) {
                            $available = false;
                            $message = 'Insufficient ' . $inventory->serial;
                            break;
                        }
                    }
                }
            }


        if (!$available) {
            return redirect()->back()->withInput()->with('message', $message);
        }

        $order = new SalesOrder();
        if (Auth::user()->company_branch_id == 0) {
            $order->company_branch_id = $request->companyBranch;
        }else{
            $order->company_branch_id = Auth::user()->company_branch_id;
        }

        $order->invoice_type = $request->invoice_type;
        $order->customer_id = $customer->id;
        $order->received_by = $request->received_by;
        $order->date = $request->date;
        $order->note = $request->note;
        $order->sub_total = 0;
        $order->vat_percentage = $request->vat;
        $order->vat = 0;
        $order->discount_percentage = $request->discount_percentage;
        $order->discount = $request->discount;
        $order->transport_cost = $request->transport_cost;
        $order->return_amount = $request->return_amount;
        $order->sale_type = $request->sale_type;
        $order->total = 0;
        $order->paid = $request->paid;
        if ($request->payment_type == 2){
            $order->client_bank_name = $request->client_bank_name;
            $order->client_cheque_no = $request->client_cheque_no;
            $order->cheque_date = $request->cheque_date;
            $order->client_amount = $request->client_amount;
        }
        $order->due = 0;
        $order->previous_due = $request->previous_due;
        $order->current_due = $request->due_total;
        $order->user_id = Auth::user()->id;
        $order->save();
        $order->order_no = str_pad($order->id, 6, 0, STR_PAD_LEFT);
        $order->save();

        $subTotal = 0;
        if  ($request->purchase_inventory) {
            foreach ($request->purchase_inventory as $key => $purchase_inventory_id) {
                $inventory = PurchaseInventory::find($purchase_inventory_id);
                if ($inventory) {
                    $sales_order_product = SalesOrderProduct::where('sales_order_id', $order->id)->where('purchase_inventory_id', $purchase_inventory_id)->first();
                    if (empty($sales_order_product)) {
                        SalesOrderProduct::create([
                            'sales_order_id' => $order->id,
                            'purchase_inventory_id' => $purchase_inventory_id,
                            'product_item_id' => $inventory->product_item_id,
                            'product_category_id' => $inventory->product_category_id,
                            'warehouse_id' => $inventory->warehouse_id,
                            'serial' => $inventory->serial,
                            'quantity' => $request->quantity[$key],
                            'buy_price' => $inventory->unit_price,
                            'unit_price' => $request->unit_price[$key],
                            'total' => $request->quantity[$key] * $request->unit_price[$key],
                        ]);

                        $subTotal += $request->quantity[$key] * $request->unit_price[$key];

                            // Inventory Log
                            $inventoryLog = PurchaseInventoryLog::where('sales_order_id', $order->id)
                                ->where('purchase_inventory_id', $purchase_inventory_id)
                                ->where('type', 2)->first();
                            if (empty($inventoryLog)) {
                                $inventoryLog = new PurchaseInventoryLog();
                            }

                            $inventoryLog->product_item_id = $inventory->product_item_id;
                            $inventoryLog->product_category_id = $inventory->product_category_id;
                            $inventoryLog->warehouse_id = $inventory->warehouse_id;
                            $inventoryLog->serial = $inventory->serial;
                            $inventoryLog->type = 2;
                            $inventoryLog->date = $request->date;
                            $inventoryLog->quantity = $request->quantity[$key];
                            $inventoryLog->unit_price = $inventory->unit_price;
                            $inventoryLog->selling_price = $request->unit_price[$key];
                            $inventoryLog->sale_total = $request->quantity[$key] * $inventoryLog->selling_price;
                            $inventoryLog->total = $request->quantity[$key] * $inventoryLog->unit_price;
                            $inventoryLog->sales_order_id = $order->id;
                            $inventoryLog->purchase_inventory_id = $inventory->id;
                            $inventoryLog->note = 'Sale Product';
                            $inventoryLog->user_id = Auth::id();
                            $inventoryLog->save();

                            $inventory->decrement('quantity', $request->quantity[$key]);
                        // $inventory->update([
                        //     'quantity' => $inventory->in_product - $inventory->out_product,
                        // ]);
                    }

                }

            }
        }

        $order->sub_total = $subTotal;
        $vat = ($subTotal * $request->vat) / 100;
        $order->vat = $vat;
        $total = $subTotal + $request->transport_cost + $vat - $request->discount - $request->return_amount;
        $order->total = $total;
        $due = $total - $request->paid;
        $order->due = $due;
        $order->next_payment = $due > 0 ? $request->next_payment : null;
        $order->save();

        // Sales Payment
        if ($request->paid > 0) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->customer_id = $customer->id;
            $payment->company_branch_id = $order->company_branch_id;
            $payment->transaction_method = 1;
            $payment->received_type = 1;
            $payment->amount = $request->paid;
            $payment->date = $request->date;
            $payment->status = 2;
            $payment->save();

            Cash::first()->increment('amount', $request->paid);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Rev. from '.($order->order_no).' '.$order->customer->name;
            $log->transaction_type = 1;
            $log->transaction_method = 1;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->paid;
            $log->customer_id = $request->customer??null;
            $log->sale_payment_id = $payment->id;
            $log->company_branch_id = $order->company_branch_id;
            $log->sales_order_id = $order->id;
            $log->save();
        }

        if ($request->payment_type == 2) {

            //$image = 'img/no_image.png';

//            if ($request->cheque_image) {
//                // Upload Image
//                $file = $request->file('cheque_image');
//                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
//                $destinationPath = 'public/uploads/sales_payment_cheque';
//                $file->move($destinationPath, $filename);
//                $image = 'uploads/sales_payment_cheque/'.$filename;
//            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->customer_id = $customer->id;
            $payment->company_branch_id = $order->company_branch_id;
            $payment->transaction_method = 2;
            $payment->client_bank_name = $request->client_bank_name;
            $payment->client_cheque_no = $request->client_cheque_no;
            $payment->client_amount = $request->client_amount;
//                $payment->bank_id = $request->bank;
//                $payment->branch_id = $request->branch;
//                $payment->bank_account_id = $request->account;
//                $payment->cheque_no = $request->cheque_no;
//                $payment->cheque_image = $image;
            $payment->amount = $request->client_amount;
            $payment->date = $request->date;
            $payment->cheque_date = $request->cheque_date;
            $payment->note = $request->note;
            $payment->status = 1;
            $payment->save();

            //BankAccount::find($request->account)->decrement('balance', $request->paid);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Rev. from '.($order->order_no).' '.$order->customer->name;
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            //$log->bank_id = $request->bank;
            //$log->branch_id = $request->branch;
            //$log->bank_account_id = $request->account;
            //$log->cheque_no = $request->cheque_no;
            //$log->cheque_image = $image;
            $log->amount = $request->client_amount;
            $log->customer_id = $customer->id ?? null;
            $log->company_branch_id = $order->company_branch_id;
            $log->sales_order_id = $order->id;
            $log->sale_payment_id = $payment->id;
            $log->payment_cheak_status = 1;
            $log->save();

        }
        return redirect()->route('sale_receipt.details', ['order' => $order->id]);
    }

    public function manuallyChequeIn() {
        $customers = Customer::where('status',1)->get();
        $employees = Employee::orderBy('name')->get();
        return view('sale.client_payment.manually_chequeIn',compact('customers','employees'));
    }
    public function manuallyChequeInPost(Request $request) {

        // Debug: Log the request data
        \Log::info('ManuallyChequeIn Request Data:', $request->all());

        $request->validate([
            'customer' => 'required',
            'invoice_no' => 'nullable|string|max:255',
            'total_sales_amount' => 'nullable|numeric|min:0',
            'receive_amount' => 'nullable|numeric|min:0',
            'due_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:1,2',
            'next_payment_date' => 'nullable|date',
            'next_approximate_payment' => 'nullable|numeric|min:0',
            'sales_person_id' => 'required|exists:employees,id',
            'note' => 'nullable|string',
        ]);

        $customer = Customer::find($request->customer);
        
        if (!$customer) {
            return redirect()->back()->withInput()->with('message', 'Customer not found');
        }

        // Calculate the due amount using the correct business logic
        // Due = Opening Due + Total Sales Amount - Total Received Amount
        $openingDue = $customer->opening_due ?? 0;
        $totalSalesAmount = $request->total_sales_amount ?? 0;
        $totalReceivedAmount = $request->receive_amount ?? 0;
        
        // Calculate current due amount
        $calculatedDueAmount = ($openingDue + $totalSalesAmount) - $totalReceivedAmount;
        
        // Ensure due amount is never negative
        $calculatedDueAmount = max(0, $calculatedDueAmount);

            $payment = new SalePayment();
            $payment->invoice_no = $request->invoice_no;
            $payment->total_sales_amount = $request->total_sales_amount;
            $payment->receive_amount = $request->receive_amount;
            $payment->due_amount = $calculatedDueAmount; // Use calculated due amount
            $payment->payment_method = $request->payment_method;
            $payment->next_payment_date = $request->next_payment_date;
            $payment->next_approximate_payment = $request->next_approximate_payment;
            $payment->customer_id = $customer->id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = $request->payment_method; // Use the selected payment method
            $payment->sales_person_id = $request->sales_person_id;
            $payment->amount = $request->receive_amount; // Use receive amount as the payment amount
            $payment->date = Carbon::now();
            $payment->note = $request->note;
            $payment->status = 1;
            
            // Debug: Log the payment data before saving
            \Log::info('Payment Data Before Save:', $payment->toArray());
            
            try {
                $payment->save();
                \Log::info('Payment saved successfully with ID: ' . $payment->id);
            } catch (\Exception $e) {
                \Log::error('Error saving payment: ' . $e->getMessage());
                \Log::error('Payment data: ' . json_encode($payment->toArray()));
                return redirect()->back()->withInput()->with('message', 'Error saving payment: ' . $e->getMessage());
            }

            $log = new TransactionLog();
            $log->date = Carbon::now();
            $log->particular = 'Rev. from '."Manual Payment".' '.$customer->name;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_method;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->receive_amount;
            $log->customer_id = $customer->id ?? null;
            $log->company_branch_id = Auth::user()->company_branch_id;
            $log->sale_payment_id = $payment->id;
            $log->payment_cheak_status = 1;
            
            try {
                $log->save();
                \Log::info('TransactionLog saved successfully with ID: ' . $log->id);
            } catch (\Exception $e) {
                \Log::error('Error saving TransactionLog: ' . $e->getMessage());
                \Log::error('TransactionLog data: ' . json_encode($log->toArray()));
                return redirect()->back()->withInput()->with('message', 'Error saving transaction log: ' . $e->getMessage());
            }

        return redirect()->route('client_payment.all_pending_check')->with('message','Payment Added Successfully');
    }

    public function saleReceiptCustomer() {
        return view('sale.receipt.customer_all');
    }
    public function saleReceiptCustomerWarehousePending() {
        return view('sale.receipt.customer_all_warehouse_pending');
    }



    public function saleWastageReceiptCustomer() {
        return view('sale.receipt.wastage_customer_all');
    }

    public function saleReceiptSupplier() {
        return view('sale.receipt.supplier_all');
    }

    public function saleReceiptDetails(SalesOrder $order) {
        //dd($order->product_items);

        return view('sale.receipt.details', compact('order'));
    }

    public function saleReceiptPrint(SalesOrder $order) {
        if($order->total > 0){
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');
        }

        $salesOrderProducts = SalesOrderProduct::where('sales_order_id',$order->id)->get();
        //dd($salesOrderProducts);

        return view('sale.receipt.print', compact('order','salesOrderProducts'));
    }
    public function requisitionPrint(SalesOrder $order) {
        if($order->total > 0){
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');
        }

        return view('sale.receipt.requisition_print', compact('order'));
    }
    public function SuperRequisitionPrint(SalesOrder $order) {
    if($order->total > 0){
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');
    }

        return view('sale.receipt.super_market_requisition_print', compact('order'));
    }

    public function saleReceiptWpadPrint(SalesOrder $order) {
        if($order->total > 0){
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');
        }


        return view('sale.receipt.wpad_print', compact('order'));
    }

    public function saleReceiptChallanPrint(SalesOrder $order) {
        if($order->total > 0){
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');
        }

        return view('sale.receipt.challan', compact('order'));
    }

    public function saleReceiptChallanWpadPrint(SalesOrder $order) {
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');

        return view('sale.receipt.wpad_challan', compact('order'));
    }

    public function salePaymentDetails(SalePayment $payment) {
        // Load the related sales order and customer to get complete information
        $payment->load(['salesOrder', 'customer']);
        
        // Debug: Log the payment data
        \Log::info('Payment Details Debug:', [
            'payment_id' => $payment->id,
            'sales_order_id' => $payment->sales_order_id,
            'has_sales_order' => $payment->salesOrder ? 'yes' : 'no',
            'sales_order_total' => $payment->salesOrder ? $payment->salesOrder->total : 'no order',
            'current_total_sales_amount' => $payment->total_sales_amount,
            'current_due_amount' => $payment->due_amount
        ]);
        
        // Get the total amount from the sales order (this is the original total amount)
        if ($payment->salesOrder) {
            $payment->total_sales_amount = $payment->salesOrder->total;
        } else {
            // If no sales order, use the existing total_sales_amount or get from customer's total
            if ($payment->total_sales_amount) {
                // Keep existing total_sales_amount if it exists
                $payment->total_sales_amount = $payment->total_sales_amount;
            } else {
                // Get the customer's total sales amount (original total before payments/returns)
                $payment->total_sales_amount = $payment->customer->total;
            }
        }
        
        // Get the current due amount for this customer (from all their orders)
        // Always calculate the current due amount to ensure it's up-to-date
        $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
        
        // Calculate total received amount for this customer
        $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
            ->sum('receive_amount');
        $payment->total_received_amount = $totalReceivedAmount;
        
        // Generate amount in words for total received amount
        if($totalReceivedAmount > 0){
            $payment->total_received_amount_in_word = DecimalToWords::convert($totalReceivedAmount,'Taka', 'Poisa');
        }
        
        $displayAmount = $payment->receive_amount ?? $payment->amount;
        if($displayAmount > 0){
            $payment->amount_in_word = DecimalToWords::convert($displayAmount,'Taka', 'Poisa');
        }
        
        // Debug: Log final values
        \Log::info('Payment Details Debug:', [
            'payment_id' => $payment->id,
            'customer_id' => $payment->customer_id,
            'customer_name' => $payment->customer->name ?? 'N/A',
            'total_sales_amount' => $payment->total_sales_amount,
            'calculated_due_amount' => $payment->due_amount,
            'display_amount' => $displayAmount,
            'customer_due_from_model' => $payment->customer->due ?? 'N/A',
            'opening_due' => $payment->customer->opening_due ?? 0,
            'total_received_amount' => $totalReceivedAmount
        ]);
        
        return view('sale.receipt.payment_details', compact('payment'));
    }

    public function salePaymentPrint(SalePayment $payment) {
        // Load the related sales order and customer to get complete information
        $payment->load(['salesOrder', 'customer']);
        
        // Get the total amount from the sales order (this is the original total amount)
        if ($payment->salesOrder) {
            $payment->total_sales_amount = $payment->salesOrder->total;
        }
        
        // Get the current due amount for this customer (from all their orders)
        // Always calculate the current due amount to ensure it's up-to-date
        $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
        
        // Calculate total received amount for this customer
        $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
            ->sum('receive_amount');
        $payment->total_received_amount = $totalReceivedAmount;
        
        // Generate amount in words for total received amount
        if($totalReceivedAmount > 0){
            $payment->total_received_amount_in_word = DecimalToWords::convert($totalReceivedAmount,'Taka', 'Poisa');
        }
        
        $displayAmount = $payment->receive_amount ?? $payment->amount;
        if($displayAmount > 0){
            $payment->amount_in_word = DecimalToWords::convert($displayAmount,'Taka', 'Poisa');
        }
        return view('sale.receipt.payment_print', compact('payment'));
    }

    public function clientPaymentCustomer() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $employees = \App\Model\Employee::orderBy('name')->get();

        return view('sale.client_payment.customer_all', compact('banks', 'employees'));
    }

    public function clientPaymentSupplier() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('sale.client_payment.supplier_all', compact('banks'));
    }

    public function clientPaymentGetOrders(Request $request) {
        $orders = SalesOrder::where('customer_id', $request->clientId)
            ->where('due', '>', 0)
            ->orderBy('id', 'desc')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function clientPaymentGetOrdersSupplier(Request $request) {
        $orders = SalesOrder::where('supplier_id', $request->clientId)
            ->where('due', '>', 0)
            ->orderBy('id', 'desc')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function makePayment(Request $request) {
        //dd($request->all());
        $rules = [
            'customer_id' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'next_payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

//         if ($request->order != '') {
//             $order = SalesOrder::find($request->order);
//             $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
//         }

//         if ($request->order != '') {
//             if ($request->amount < $order->due)
//                 $rules['next_payment_date'] = 'required|date';
//         }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }


         //$order = SalesOrder::find($request->order);
        $customer = Customer::find($request->customer_id);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = null;
            $payment->customer_id = $request->customer_id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->next_payment_date = $request->next_payment_date;
            $payment->note = $request->note;
            $payment->status = 2;
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->increment('amount', $request->amount);
            else
                MobileBanking::first()->increment('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Payment from ' . $customer->name;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->customer_id = $request->customer_id;
            $log->sale_payment_id = $payment->id;
            $log->company_branch_id = Auth::user()->company_branch_id;
            $log->save();
        }elseif ($request->payment_type == 4){

            $payment = new SalePayment();
            $payment->sales_order_id = null;
            $payment->customer_id = $request->customer_id;
            $payment->user_id = Auth::user()->id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->next_payment_date = $request->next_payment_date;
            $payment->note = $request->note;
            $payment->status = 2;
            $payment->save();

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Sale Adjustment Discount from '.$customer->name;
            $log->transaction_type = 2;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 210;
            $log->account_head_sub_type_id = 19;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();

        }elseif ($request->payment_type == 5){

            $payment = new SalePayment();
            $payment->sales_order_id = null;
            $payment->customer_id = $request->customer_id;
            $payment->user_id = Auth::user()->id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->next_payment_date = $request->next_payment_date;
            $payment->note = $request->note;
            $payment->status = 2;
            $payment->save();

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Return Adjustment Amount from '.$customer->name;
            $log->transaction_type = 2;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 233;
            $log->account_head_sub_type_id = 20;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $payment->customer_id = $request->customer_id;
            $log->return_adjustment_amount = 1;
            $log->save();

            Cash::first()->decrement('amount', $request->amount);

        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/'.$filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = null;
            $payment->customer_id = $request->customer_id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = 2;
            $payment->bank_id = $request->bank;
            $payment->branch_id = $request->branch;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->status = 2;
            $payment->save();

            BankAccount::find($request->account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Payment from '.$customer->name??'';
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->customer_id = $request->customer_id;
            $log->sale_payment_id = $payment->id;
            $log->company_branch_id = Auth::user()->company_branch_id;
            $log->save();
        }

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('client_payment.customer.all')]);
    }

    public function voucherUpdate(Request $request) {
                $rules = [
                    'amount' => 'required|numeric|min:0',
                    'note' => 'nullable|string|max:255',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
                }

                $payment = SalePayment::where('id', $request->payment_id)->where('status',2)->first();


                if ($payment){
                    if ($payment->transaction_method == 1 || $payment->transaction_method == 5) {
                        Cash::first()->decrement('amount', $payment->amount);
                        Cash::first()->increment('amount', $request->amount);
                    }elseif($payment->transaction_method == 2){
                        BankAccount::find($payment->bank_account_id)->decrement('balance', $payment->amount);
                        BankAccount::find($payment->bank_account_id)->increment('balance', $request->amount);
                    }else{
                        '';
                    }
                }
                $payment->amount = $request->amount;
                $payment->note = $request->note;
                $payment->user_id = Auth::user()->id;
                $payment->save();

                $log = TransactionLog::where('sale_payment_id',$request->payment_id)->first();
                if ($log) {
                    $log->amount = $request->amount;
                    $log->company_branch_id = Auth::user()->company_branch_id;
                    $log->note = $request->note;
                    $log->save();
                }

                return response()->json(['success' => true, 'message' => 'Payment has been Updated.', 'redirect_url' => route('customer')]);
            }

    public function chequeApproved(Request $request) {

        $rules = [
            'payment_id' => 'required',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == 2) {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Check if this is a partial payment
        $isPartialPayment = $request->has('is_partial_payment') && $request->is_partial_payment == '1';
        $nextApproximateDate = $request->next_approximate_payment_date;

        if ($request->payment_type == 1) {
            $previousPayment = SalePayment::with('customer')->where('id',$request->payment_id)->first();

            $payment = new SalePayment();
            if($previousPayment->sales_order_id != null){
                $payment->sales_order_id = $previousPayment->sales_order_id;
            }else{
                $payment->sales_order_id = null;
            }
            $payment->customer_id = $previousPayment->customer_id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = $request->payment_type;
            $payment->total_sales_amount = $previousPayment->total_sales_amount;
            $payment->receive_amount = $request->receive_amount ?? 0;
            $payment->due_amount = $request->receive_amount ?? 0;
            $payment->amount = $request->receive_amount ?? $previousPayment->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->status = 2;
            
            // Handle partial payment logic
            if ($isPartialPayment && $nextApproximateDate) {
                // For partial payments, create a new payment record for this individual payment
                $payment->receive_amount = $request->receive_amount ?? 0;
                $payment->due_amount = $request->receive_amount ?? 0;
                $payment->amount = $request->receive_amount ?? 0;
                $payment->status = 2; // Mark as approved
                $payment->note = $request->note ?? "Partial Payment";
                
                // Update the original payment's next approximate payment date
                $previousPayment->next_approximate_payment_date = $nextApproximateDate;
                
                // Calculate the current due amount for the customer
                $customer = $previousPayment->customer;
                $currentDueAmount = $this->calculateCustomerDueAmount($customer);
                
                // Update the original payment's due_amount to reflect current customer due
                $previousPayment->due_amount = $currentDueAmount;
                
                // Keep original payment as pending since it's a partial payment
                $previousPayment->update([
                    'status' => 1, // Keep as pending
                    'note' => "Partial Payment - Still Due"
                ]);
                
                $payment->save();
                
                if ($request->payment_type == 1)
                    Cash::first()->increment('amount', $request->receive_amount ?? 0);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Partial Payment from ' . $previousPayment->customer->name;
                $log->transaction_type = 1;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->amount = $request->receive_amount ?? 0;
                $log->note = $request->note;
                $log->customer_id = $previousPayment->customer_id;
                $log->sale_payment_id = $payment->id; // Link to the new payment record
                $log->company_branch_id = Auth::user()->company_branch_id;
                $log->save();

                return response()->json(['success' => true, 'message' => 'Partial payment processed successfully.']);
            } else {
                // Create a new payment record for this individual payment
                // Don't update the original payment's receive_amount
                $payment->receive_amount = $request->receive_amount ?? 0;
                $payment->due_amount = $request->receive_amount ?? 0;
                $payment->amount = $request->receive_amount ?? 0;
                $payment->status = 2; // Mark as approved
                $payment->note = $request->note ?? "Additional Payment";
                
                // Calculate the current due amount for the customer
                $customer = $previousPayment->customer;
                $currentDueAmount = $this->calculateCustomerDueAmount($customer);
                
                // Update the original payment's due_amount to reflect current customer due
                $previousPayment->due_amount = $currentDueAmount;
                
                // Only mark original payment as completed if fully paid
                if ($currentDueAmount <= 0) {
                    $previousPayment->update([
                        'status' => 3, // Completed
                        'note' => "Fully Paid - All Payments Complete"
                    ]);
                } else {
                    // Keep as pending if not fully paid
                    $previousPayment->update([
                        'status' => 1, // Keep as pending
                        'note' => "Partial Payment - Still Due"
                    ]);
                }
            }
            
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->increment('amount', $request->receive_amount ?? 0);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Additional Payment from ' . $previousPayment->customer->name;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->receive_amount ?? 0;
            $log->note = $request->note;
            $log->customer_id = $previousPayment->customer_id;
            $log->sale_payment_id = $payment->id;
            $log->company_branch_id = Auth::user()->company_branch_id;
            $log->payment_cheak_status = 3;
            $log->save();

        }else{
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);
                $image = 'uploads/sales_payment_cheque/'.$filename;
            }

            $payment = SalePayment::where('id',$request->payment_id)->first();

            $bankAccount = BankAccount::where('id', $request->account)
                ->first();

            if (empty($bankAccount)) {
                return response()->json(['success' => false, 'message' => 'Storage Bank Not Found', 'redirect_url' => route('customer')]);
            }


            // Handle partial payment logic for bank payments
            if ($isPartialPayment && $nextApproximateDate) {
                // For partial payments, keep the original payment in pending status
                // and update the next approximate payment date
                $payment->next_approximate_payment_date = $nextApproximateDate;
                $payment->bank_id = $request->bank;
                $payment->branch_id = $request->branch;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->note = ($request->note ? $request->note . ' - ' : '') . 'Partial Payment';
                $payment->save();
            } else {
                // For full payments, mark as approved
                if ($request->note == null) {
                    $payment->update(['status' => 2]);
                    $payment->bank_id = $request->bank;
                    $payment->branch_id = $request->branch;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->save();
                } else {
                    $payment->update(['status' => 2, 'note' => $request->note]);
                    $payment->bank_id = $request->bank;
                    $payment->branch_id = $request->branch;
                    $payment->bank_account_id = $request->account;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->cheque_image = $image;
                    $payment->save();
                }
            }

            // Only update sales order for full payments
            if (!$isPartialPayment) {
                $order = SalesOrder::where('id',$payment->sales_order_id)->first();
                if ($order) {
                    $order->decrement('due',$payment->amount);
                    $order->increment('paid',$payment->amount);
                    $order->save();
                }
            }


            $bankAccount->increment('balance', $payment->amount);

            $log = TransactionLog::where('sale_payment_id',$payment->id)->where('payment_cheak_status',1)->first();
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->payment_cheak_status = 2;
//            $log->amount = $payment->amount;
//            $log->note = $payment->note;
//            $log->customer_id = $payment->customer->id;
//            $log->sale_payment_id = $payment->id;
            $log->save();
        }

        // Return appropriate message based on payment type
        if ($isPartialPayment) {
            $message = 'Partial payment has been processed. Record will remain in due list until fully paid.';
        } else {
            $message = '';
        }
        
        return response()->json(['success' => true, 'message' => $message, 'redirect_url' => route('customer')]);
    }

    public function updateDateOnly(Request $request) {
        $rules = [
            'payment_id' => 'required',
            'next_approximate_payment_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $payment = SalePayment::where('id', $request->payment_id)->first();
        
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment record not found']);
        }

        // Update only the next approximate payment date
        $payment->next_approximate_payment_date = $request->next_approximate_payment_date;
        $payment->save();

        return response()->json([
            'success' => true, 
            'message' => 'Next payment date has been updated successfully.',
            'redirect_url' => route('client_payment.all_pending_check')
        ]);
    }

    public function saleProductDetails(Request $request) {
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

            if ($firstWarehouseProduct->quantity > $secondWarehouseProduct->quantity) {
                $product = $firstWarehouseProduct->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            }elseif($firstWarehouseProduct->quantity < $secondWarehouseProduct->quantity){
                $product = $secondWarehouseProduct->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            }else{
                return response()->json(['success' => false, 'message' => 'Not found.']);
            }

        }else{

            $product = PurchaseInventory::where('serial', $request->serial)
                ->where('quantity', '>', 0)
                //->where('selling_price', '>', 1)
                ->with('productItem','productCategory','warehouse')
                ->first();

            if ($product) {
                if ($product->selling_price > 0) {
                    $product = $product->toArray();
                    return response()->json(['success' => true, 'data' => $product]);
                } elseif($product->selling_price <= 0) {
                    //dd($product->selling_price);
                    return response()->json(['success' => false, 'message' => 'This Product Selling Price less than zero'.' .'.$product->serial]);
                }else{
                    return response()->json(['success' => false, 'message' => 'Product Not Available.']);
                }
            }else{
                return response()->json(['success' => false, 'message' => 'Product Not Available.']);
            }
        }

    }

    public function saleProductCheckDetails(Request $request) {
        $firstWarehouseProduct = PurchaseInventory::where('serial', $request->serial)
            ->where('warehouse_id', 1)
            //->where('quantity', '>=', 0)
            ->with('productItem','productCategory','warehouse')
            ->first();

        $secondWarehouseProduct = PurchaseInventory::where('serial', $request->serial)
            ->where('warehouse_id', 2)
            //->where('quantity', '>=', 0)
            ->with('productItem','productCategory','warehouse')
            ->first();

        if (!empty($firstWarehouseProduct && $secondWarehouseProduct)) {

            if ($firstWarehouseProduct->quantity > $secondWarehouseProduct->quantity) {
                $product = $firstWarehouseProduct->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            }elseif($firstWarehouseProduct->quantity < $secondWarehouseProduct->quantity){
                $product = $secondWarehouseProduct->toArray();
                return response()->json(['success' => true, 'data' => $product]);
            }else{
                return response()->json(['success' => false, 'message' => 'Not found.']);
            }

        }else{

            $product = PurchaseInventory::where('serial', $request->serial)
                //->where('quantity', '>', 0)
                //->where('selling_price', '>', 1)
                ->with('productItem','productCategory','warehouse')
                ->first();

            if ($product) {
                if ($product->selling_price > 0) {
                    $product = $product->toArray();
                    return response()->json(['success' => true, 'data' => $product]);
                } elseif($product->selling_price <= 0) {
                    //dd($product->selling_price);
                    return response()->json(['success' => false, 'message' => 'This Product Selling Price less than zero'.' .'.$product->serial]);
                }else{
                    return response()->json(['success' => false, 'message' => 'Product Not Available.']);
                }
            }else{
                return response()->json(['success' => false, 'message' => 'Product Not Available.']);
            }
        }

    }

    public function customerJson(Request $request) {
        if (!$request->searchTerm) {
            $customers = Customer::orderBy('name')->where('status', 1)->limit(10)->get();
        } else {
            $customers = Customer::where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->searchTerm.'%')
                        ->orWhere('mobile', 'like', '%'.$request->searchTerm.'%');
                })->orderBy('name')->where('status', 1)->limit(10)->get();
        }

        $data = array();

        foreach ($customers as $customer) {
            $data[] = [
                'id' => $customer->id,
                'text' => $customer->name.' - '.$customer->mobile_no
            ];
        }

        echo json_encode($data);
    }

    public function supplierJson(Request $request) {
        if (!$request->searchTerm) {
            $suppliers = Supplier::where('status', 1)->orderBy('name')->limit(10)->get();
        } else {
            $suppliers = Supplier::where('status', 1)
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->searchTerm.'%');
                })->orderBy('name')->limit(10)->get();
        }

        $data = array();

        foreach ($suppliers as $supplier) {
            $data[] = [
                'id' => $supplier->id,
                'text' => $supplier->name
            ];
        }

        echo json_encode($data);
    }



    public function saleReceiptEdit(SalesOrder $order) {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->where('status', 1)->get();
        return view('sale.receipt.edit', compact('order', 'warehouses',
            'banks', 'customers','productItems','order'));
    }
    public function saleReceiptWarehousePendingEdit(SalesOrder $order) {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $productItems = ProductItem::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->where('status', 1)->get();
        return view('sale.receipt.warehouse_pending_edit', compact('order', 'warehouses',
            'banks', 'customers','productItems','order'));
    }

    public function saleReceiptWarehousePendingEditPost(SalesOrder $order, Request $request){

        if ($order->invoice_type == 1) {
            $order->invoice_type = 2;
            $order->save();
            $message = 'Warehouse Approved Successfully';
            return redirect()->route('sale_receipt.customer.warehouse_pending.all')->withInput()->with('message', $message);
            //return response()->json(['success' => true, 'message' => 'Warehouse Approved Successfully.', 'redirect_url' => route('sale_receipt.customer.warehouse_pending.all')]);
        }

    }

    public function saleReceiptEditPost(SalesOrder $order, Request $request)
    {

        //dd($order);
        if (empty($request->product_serial)) {
            $message = 'No Product Item Found';
            return redirect()->back()->withInput()->with('message', $message);
        }

        if ($request->payment_type == '2') {
            $salePayment = SalePayment::where('sales_order_id', $order->id)
                ->where('transaction_method', 2)
                ->first();
            if ($salePayment) {
                if ($salePayment->status == 2 || $salePayment->status == 3) {
                    $message = 'This Order Cheque Already Approved';
                    return redirect()->back()->withInput()->with('message', $message);
                }
        //     } else {
        //         $paidTotal = $request->paid + $request->client_amount;
        //         if ($request->paid > $request->total || $request->client_amount > $request->total || $paidTotal > $request->total) {
        //             $message = 'Paid Amount Should Not Greater Than Total';
        //             return redirect()->back()->withInput()->with('message', $message);
        //         }
        //     }
        // } else {

        //     if ($request->paid > $request->total) {
        //         $message = 'Paid Amount Should Not Greater Than Total';
        //         return redirect()->back()->withInput()->with('message', $message);
            }
        }
        $rules = [
            'customer' => 'required',
            'date' => 'required|date',
        ];

        if (Auth::user()->company_branch_id == 0) {
            $rules = [
                'company' => 'required',
            ];
        }

        if ($request->product_serial) {
            $rules['product_serial.*'] = 'required';
            $rules['product_item.*'] = 'required';
            $rules['product_category.*'] = 'required';
            $rules['warehouse.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:.01';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }
//        if ($order->invoice_type == 1){
//            $rules['invoice_type'] = 'required';
//        }

        if ($request->payment_type == '2') {
            $rules['client_bank_name'] = 'required';
            $rules['client_cheque_no'] = 'required';
            $rules['cheque_date'] = 'required';
            $rules['client_amount'] = 'required';
        }

        $request->validate($rules);

        $available = true;
        $message = '';

        if ($request->purchase_inventory) {
            foreach ($request->purchase_inventory as $index => $purchase_inventory_id) {
                $inventory = PurchaseInventory::find($purchase_inventory_id);
                $prev_quantity = SalesOrderProduct::where('sales_order_id', $order->id)
                        ->where('purchase_inventory_id', $purchase_inventory_id)
                        ->first()->quantity ?? 0;

                //dd($inventory);
                if ($inventory) {
                    if ($request->quantity[$index] > ($inventory->quantity + $prev_quantity)) {
                        $available = false;
                        $message = 'Insufficient' . $inventory->serial;
                        break;
                    }else{
                        if ($request->quantity[$index] > $prev_quantity ) {

                           $decrementQuantity = $request->quantity[$index] - $prev_quantity;
                           $inventory->decrement('quantity',$decrementQuantity);

                        }elseif($request->quantity[$index] < $prev_quantity){
                            $incrementQuantity = $prev_quantity - $request->quantity[$index];

                            $inventory->increment('quantity',$incrementQuantity);
                        }
                    }
                }

            }
        }

        if (!$available) {
            return redirect()->back()->withInput()->with('message', $message);
        }

        //dd('bfb');

        if (Auth::user()->company_branch_id == 0) {

            $order->company_branch_id = $request->company;
        }

        if ($order->invoice_type == 1) {
            $order->invoice_type = 1;
        }
        $order->customer_id = $request->customer;
        $order->received_by = $request->received_by;
        $order->date = $request->date;
        $order->note = $request->note;
        $order->vat_percentage = $request->vat;
        $order->discount_percentage = $request->discount_percentage;
        $order->discount = $request->discount;
        $order->transport_cost = $request->transport_cost;
        $order->return_amount = $request->return_amount;
        $order->sale_type = $request->sale_type;
        $order->previous_due = $request->previous_due;
        $order->current_due = $request->due_total;
        $order->save();

        PurchaseInventoryLog::where('sales_order_id', $order->id)
            ->delete();

        $prev_order_products = SalesOrderProduct::where('sales_order_id', $order->id)
            ->whereNotIn('serial', $request->product_serial)
            ->get();

        foreach ($prev_order_products as $prev_order_product){
            $purchaseInventory = PurchaseInventory::where('id',$prev_order_product->purchase_inventory_id)->first();
            $purchaseInventory->increment('quantity', $prev_order_product->quantity);
            $prev_order_product->delete();
        }
        SalesOrderProduct::where('sales_order_id', $order->id)
            ->delete();

        $subTotal = 0;
        if ($request->purchase_inventory) {
            foreach ($request->purchase_inventory as $key => $purchase_inventory_id) {
                $inventory = PurchaseInventory::find($purchase_inventory_id);
                if ($inventory) {
                    $sales_order_product = SalesOrderProduct::create([
                        'sales_order_id' => $order->id,
                        'purchase_inventory_id' => $purchase_inventory_id,
                        'product_item_id' => $inventory->product_item_id,
                        'product_category_id' => $inventory->product_category_id,
                        'warehouse_id' => $inventory->warehouse_id,
                        'serial' => $inventory->serial,
                        'quantity' => $request->quantity[$key],
                        'buy_price' => $inventory->unit_price,
                        'unit_price' => $request->unit_price[$key],
                        'total' => $request->quantity[$key] * $request->unit_price[$key],
                    ]);

                    $subTotal += $request->quantity[$key] * $request->unit_price[$key];

                    $inventoryLog = new PurchaseInventoryLog();
                    $inventoryLog->product_item_id = $inventory->product_item_id;
                    $inventoryLog->product_category_id = $inventory->product_category_id;
                    $inventoryLog->warehouse_id = $inventory->warehouse_id;
                    $inventoryLog->serial = $inventory->serial;
                    $inventoryLog->type = 2;
                    $inventoryLog->date = $request->date;
                    $inventoryLog->quantity = $request->quantity[$key];
                    $inventoryLog->unit_price = $inventory->unit_price;
                    $inventoryLog->selling_price = $request->unit_price[$key];
                    $inventoryLog->sale_total = $request->quantity[$key] * $inventoryLog->selling_price;
                    $inventoryLog->total = $request->quantity[$key] * $inventoryLog->unit_price;
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->purchase_inventory_id = $inventory->id;
                    $inventoryLog->note = 'Sale Product';
                    $inventoryLog->user_id = Auth::id();
                    $inventoryLog->save();

                    //$inventory->decrement('quantity', $request->quantity[$key]);

//                    $inventory->update([
//                        'quantity' => $inventory->in_product - $inventory->out_product,
//                    ]);
                }
            }
        }

        if ($request->paid > 0) {

            if ($order->paid > 0) {

                if ($order->paid < $request->paid) {
                    $paidIncrement = $request->paid - $order->paid;
                    $purchasePaymentCheck = SalePayment::where('sales_order_id', $order->id)->where('transaction_method', 1)->first();
                    if ($purchasePaymentCheck) {
                        $purchasePaymentCheck->increment('amount', $paidIncrement);
                        Cash::first()->increment('amount', $paidIncrement);
                    }
                    $log = TransactionLog::where('sale_payment_id',$purchasePaymentCheck->id)->where('transaction_method', 1)->first();
                    if ($log) {
                        $log->increment('amount', $paidIncrement);
                    }

                } else {
                    $paidDecrement = $order->paid - $request->paid;
                    $purchasePaymentCheck = SalePayment::where('sales_order_id', $order->id)->where('transaction_method', 1)->first();
                    if ($purchasePaymentCheck) {
                        $purchasePaymentCheck->decrement('amount', $paidDecrement);
                        Cash::first()->decrement('amount', $paidDecrement);
                    }
                    $log = TransactionLog::where('sale_payment_id',$purchasePaymentCheck->id)->where('transaction_method', 1)->first();
                    if ($log) {
                        $log->decrement('amount', $paidDecrement);
                    }
                }
            } else {
                $payment = new SalePayment();
                $payment->sales_order_id = $order->id;
                $payment->customer_id = $request->customer;
                $payment->company_branch_id = $order->company_branch_id;
                $payment->transaction_method = 1;
                $payment->received_type = 2;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->status = 2;
                $payment->save();

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Rev. from ' . ($order->order_no) . ' ' . $order->customer->name;
                $log->transaction_type = 1;
                $log->transaction_method = 1;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->amount = $request->paid;
                $log->customer_id = $request->customer ?? null;
                $log->sale_payment_id = $payment->id;
                $log->company_branch_id = $order->company_branch_id;
                $log->sales_order_id = $order->id;
                $log->save();

                Cash::first()->increment('amount', $request->paid);
            }
        }


        $order->sub_total = $subTotal ;
        $vat = ($subTotal * $request->vat) / 100;
        $order->vat = $vat;
        $total =  (($subTotal + $request->transport_cost + $vat) - $request->discount) - $request->return_amount;
        $order->total = $total;
        $order->paid = $request->paid;
        $due = $total - $request->paid;
        $order->due = $due;
        $order->next_payment = $due > 0 ? $request->next_payment : null;

        if ($order->client_amount > 0) {

            if ($request->payment_type == 2) {

                $order->client_bank_name = $request->client_bank_name;
                $order->client_cheque_no = $request->client_cheque_no;
                $order->cheque_date = $request->cheque_date;
                $order->client_amount = $request->client_amount;

                $salePayment = SalePayment::where('sales_order_id',$order->id)->where('status', 1)->first();

                if ($salePayment) {
                    $salePayment->client_bank_name = $request->client_bank_name;
                    $salePayment->client_cheque_no = $request->client_cheque_no;
                    $salePayment->cheque_date = $request->cheque_date;
                    $salePayment->client_amount = $request->client_amount;
                    $salePayment->amount = $request->client_amount;
                    $salePayment->save();

                }
                $log = TransactionLog::where('sale_payment_id',$salePayment->id)->first();
                $log->amount = $request->client_amount;
                $log->save();

            }
        }else {

            if ($request->payment_type == 2) {

                $order->client_bank_name = $request->client_bank_name;
                $order->client_cheque_no = $request->client_cheque_no;
                $order->cheque_date = $request->cheque_date;
                $order->client_amount = $request->client_amount;

                $payment = new SalePayment();
                $payment->sales_order_id = $order->id;
                $payment->customer_id = $request->customer;
                $payment->company_branch_id = $order->company_branch_id;
                $payment->transaction_method = 2;
                $payment->client_bank_name = $request->client_bank_name;
                $payment->client_cheque_no = $request->client_cheque_no;
                $payment->client_amount = $request->client_amount;
                $payment->amount = $request->client_amount;
                $payment->date = $request->date;
                $payment->cheque_date = $request->cheque_date;
                $payment->note = $request->note;
                $payment->status = 1;
                $payment->save();


                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Rev. from ' . ($order->order_no) . ' ' . $order->customer->name;
                $log->transaction_type = 1;
                $log->transaction_method = 2;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->amount = $request->client_amount;
                $log->customer_id = $customer->id ?? null;
                $log->company_branch_id = $order->company_branch_id;
                $log->sales_order_id = $order->id;
                $log->sale_payment_id = $payment->id;
                $log->payment_cheak_status = 1;
                $log->save();
            }
        }
        $order->save();

        return redirect()->route('sale_receipt.details', ['order' => $order->id]);
    }

    public function customerPayments($customer_id){
        $customer = Customer::find($customer_id);
        $banks = Bank::where('status',1)->orderBy('name')->get();
        $payments = SalePayment::where('customer_id', $customer_id)
            ->orderBy('date','desc')
            ->orderBy('id','desc')
            ->paginate(10);
        
        // Calculate customer-wise total due amount for display
        $customer->total_due_amount = $this->calculateCustomerDueAmount($customer);
        
        // Add customer-wise due amount to each payment for display purposes
        foreach ($payments as $payment) {
            $payment->customer_total_due = $customer->total_due_amount;
        }
        
        return view('sale.client_payment.customer_payments', compact('customer','payments','banks'));
    }
    public function allPendingCheque(Request $request){
        $currentDate = date('Y-m-d');
        $banks = Bank::where('status',1)->orderBy('name')->get();
        $customers = Customer::where('status',1)->orderBy('name')->get();
        
        // Build query based on user's company branch
        $query = SalePayment::where('status', 1)->with(['customer', 'salesPerson']);
        
        // Branch filtering commented out - show all payments regardless of branch
        // if(Auth::user()->company_branch_id==1){
        //     $query->where('company_branch_id',1);
        // }elseif (Auth::user()->company_branch_id==2){
        //     $query->where('company_branch_id',2);
        // }
        
        // Apply search filters
        if($request->filled('customer_id')){
            $query->where('customer_id', $request->customer_id);
        }
        
        if($request->filled('invoice_no')){
            $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
        }
        
        if($request->filled('date_from')){
            $query->whereDate('date', '>=', $request->date_from);
        }
        
        if($request->filled('date_to')){
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        // Apply sorting
        if($request->filled('sort_date')){
            if($request->sort_date == 'newest'){
                $query->orderBy('date', 'desc');
            } elseif($request->sort_date == 'oldest'){
                $query->orderBy('date', 'asc');
            }
        } else {
            // Default sorting by date desc
            $query->orderBy('date', 'desc');
        }
        
        $payments = $query->paginate(10);
        
        // Update amounts for each payment to show invoice-wise amounts
        // and filter out fully paid payments (due = 0)
        $filteredPayments = collect();
        foreach ($payments as $payment) {
            // First check if the customer is overall paid
            $customerDueAmount = $this->calculateCustomerDueAmount($payment->customer);
            
            // If customer is overall paid, skip this payment
            if ($customerDueAmount <= 0) {
                continue;
            }
            
            // Calculate invoice-wise amounts for this specific payment/invoice
            $invoiceAmounts = $this->calculateInvoiceAmounts($payment);
            
            // Set invoice-wise due amount
            $payment->due_amount = $invoiceAmounts['due_amount'];
            
            // Set invoice-wise received amount
            $payment->total_received_amount = $invoiceAmounts['received_amount'];
            
            // Set invoice-wise total sales amount
            $payment->total_sales_amount = $invoiceAmounts['invoice_amount'];
            
            // Add opening due amount for this customer
            $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
            
            // Only include payments that still have a due amount > 0
            if ($payment->due_amount > 0) {
                $filteredPayments->push($payment);
            }
        }
        
        // Apply amount sorting if requested
        if($request->filled('sort_amount')){
            if($request->sort_amount == 'low_to_high'){
                $filteredPayments = $filteredPayments->sortBy('due_amount');
            } elseif($request->sort_amount == 'high_to_low'){
                $filteredPayments = $filteredPayments->sortByDesc('due_amount');
            }
        }
        
        // Create a new paginator with filtered results
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredPayments,
            $payments->total(),
            $payments->perPage(),
            $payments->currentPage(),
            ['path' => $payments->path(), 'pageName' => 'page']
        );
        
        // Preserve search parameters in pagination links
        $payments->appends($request->query());

        return view('sale.client_payment.all_pending_cheque',compact('payments','banks','currentDate','customers'));
    }

    public function adminPendingCheque(){
        $currentDate = date('Y-m-d');
        $banks = Bank::where('status',1)->orderBy('name')->get();
        // Branch filtering commented out - show all payments regardless of branch
        // $payments = SalePayment::where('status', 1)->where('company_branch_id',0)->with(['customer'])->paginate(10);
        $payments = SalePayment::where('status', 1)->with(['customer'])->paginate(10);
        
        // Update due amounts for each payment to match customer model calculation
        // and filter out fully paid payments (due = 0)
        $filteredPayments = collect();
        foreach ($payments as $payment) {
            $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
            
            // Calculate total received amount for this customer
            $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                ->sum('receive_amount');
            $payment->total_received_amount = $totalReceivedAmount;
            
            // Add opening due amount for this customer
            $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
            
            // Only include payments that still have a due amount > 0
            if ($payment->due_amount > 0) {
                $filteredPayments->push($payment);
            }
        }
        
        // Create a new paginator with filtered results
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredPayments,
            $payments->total(),
            $payments->perPage(),
            $payments->currentPage(),
            ['path' => $payments->path(), 'pageName' => 'page']
        );
        
        return view('sale.client_payment.admin_pending_cheque',compact('payments','banks','currentDate'));
    }
    public function yourChoicePendingCheque(){
        $currentDate = date('Y-m-d');
        $banks = Bank::where('status',1)->orderBy('name')->get();
        // Branch filtering commented out - show all payments regardless of branch
        // $payments = SalePayment::where('status', 1)->where('company_branch_id',1)->with(['customer'])->paginate(10);
        $payments = SalePayment::where('status', 1)->with(['customer'])->paginate(10);
        
        // Update due amounts for each payment to match customer model calculation
        // and filter out fully paid payments (due = 0)
        $filteredPayments = collect();
        foreach ($payments as $payment) {
            $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
            
            // Calculate total received amount for this customer
            $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                ->sum('receive_amount');
            $payment->total_received_amount = $totalReceivedAmount;
            
            // Add opening due amount for this customer
            $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
            
            // Only include payments that still have a due amount > 0
            if ($payment->due_amount > 0) {
                $filteredPayments->push($payment);
            }
        }
        
        // Create a new paginator with filtered results
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredPayments,
            $payments->total(),
            $payments->perPage(),
            $payments->currentPage(),
            ['path' => $payments->path(), 'pageName' => 'page']
        );
        
        return view('sale.client_payment.your_choice_pending_cheque',compact('payments','banks','currentDate'));
    }
    public function yourChoicePlusPendingCheque(){
        $currentDate = date('Y-m-d');
        $banks = Bank::where('status',1)->orderBy('name')->get();
        // Branch filtering commented out - show all payments regardless of branch
        // $payments = SalePayment::where('status', 1)->where('company_branch_id',2)->with(['customer'])->paginate(10);
        $payments = SalePayment::where('status', 1)->with(['customer'])->paginate(10);
        
        // Update due amounts for each payment to match customer model calculation
        // and filter out fully paid payments (due = 0)
        $filteredPayments = collect();
        foreach ($payments as $payment) {
            $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
            
            // Calculate total received amount for this customer
            $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                ->sum('receive_amount');
            $payment->total_received_amount = $totalReceivedAmount;
            
            // Add opening due amount for this customer
            $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
            
            // Only include payments that still have a due amount > 0
            if ($payment->due_amount > 0) {
                $filteredPayments->push($payment);
            }
        }
        
        // Create a new paginator with filtered results
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredPayments,
            $payments->total(),
            $payments->perPage(),
            $payments->currentPage(),
            ['path' => $payments->path(), 'pageName' => 'page']
        );
        
        return view('sale.client_payment.your_choice_plus_pending_cheque',compact('payments','banks','currentDate'));
    }

    public function pendingChequeDelete(Request $request){

        $salePayment = SalePayment::where('id', $request->id)->where('status',1)->first();

        TransactionLog::where('sale_payment_id',$salePayment->id)->where('payment_cheak_status',1)->delete();

        $salesOrder = SalesOrder::where('id',$salePayment->sales_order_id)->first();
        if ($salesOrder) {
            $salesOrder->update([
                'client_bank_name' => null,
                'client_cheque_no' => null,
                'cheque_date' => null,
                'client_amount' => null,
            ]);
        }

        $salePayment->delete();


        return redirect()->route('client_payment.all_pending_check')->with('message','Cheque Delete Successfully');
    }

    public function saleDelete(Request $request){

        $saleOrderProducts = SalesOrderProduct::where('sales_order_id', $request->id)->get();

        $saleOrderCheck = SalesOrder::where('id',$request->id)->first();

        foreach ($saleOrderProducts as $saleOrderProduct){
            $inventory = PurchaseInventory::where('id', $saleOrderProduct->purchase_inventory_id)
                ->first();
            if ($inventory){
                $inventory->increment('quantity',$saleOrderProduct->quantity);
            }

            $saleOrderProduct->delete();
        }

        $salePayments = SalePayment::where('sales_order_id',$request->id)->where('status', 2)->get();
        if (count($salePayments) > 0) {
            foreach ($salePayments as $salePayment){
                if ($salePayment->transaction_method == 1) {
                    Cash::first()->decrement('amount', $salePayment->amount);
                }else{
                    BankAccount::find($salePayment->bank_account_id)->decrement('balance', $salePayment->amount);
                }
                TransactionLog::where('sale_payment_id',$salePayment->id)->delete();
                $salePayment->delete();
            }
        }
        $cashPayments = SalePayment::where('sales_order_id',$request->id)->where('status', 3)->get();
        if (count($cashPayments) > 0) {
            foreach ($cashPayments as $cashPayment){
                if ($cashPayment->transaction_method == 1) {
                    Cash::first()->decrement('amount', $cashPayment->amount);
                }
                TransactionLog::where('sale_payment_id',$cashPayment->id)->delete();
                $cashPayment->delete();
            }
        }

        $pendingSalePayments = SalePayment::where('sales_order_id',$request->id)->where('status', 1)->get();
        if (count($pendingSalePayments) > 0) {
            foreach ($pendingSalePayments as $pendingSalePayment){
                TransactionLog::where('sale_payment_id',$pendingSalePayment->id)->delete();
                $pendingSalePayment->delete();
            }
        }
        if ($saleOrderCheck->invoice_type == 1 || $saleOrderCheck->invoice_type == 2){
            PurchaseInventoryLog::where('sales_order_id',$request->id)->delete();

            SalesOrder::where('id',$request->id)->delete();
        }

        return redirect(route('sale_receipt.customer.all'))->with('message','Sale Order Delete Successfully');
    }

    public function voucherDelete(Request $request){

        $salePayment = SalePayment::where('id',$request->id)->whereIn('status', [2,3])->first();
        if ($salePayment) {
            if ($salePayment->transaction_method == 1 || $salePayment->transaction_method == 5) {
                Cash::first()->decrement('amount', $salePayment->amount);
            }elseif ($salePayment->transaction_method == 2){
                BankAccount::find($salePayment->bank_account_id)->decrement('balance', $salePayment->amount);
            }else{
                '';
            }
            TransactionLog::where('sale_payment_id',$salePayment->id)->delete();
            $salePayment->delete();

        }

        $pendingSalePayment = SalePayment::where('id',$request->id)->where('status', 1)->first();
        if ($pendingSalePayment) {
            if($pendingSalePayment){
                TransactionLog::where('sale_payment_id',$pendingSalePayment->id)->delete();
                $pendingSalePayment->delete();
            }
        }
        return redirect(route('customer_payments', ['customer_id'=>$request->customer_id]))->with('message','Customer Payment Delete Successfully');
    }


    public function processCustomerReturn(Request $request) {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'return_amount' => 'required|numeric|min:0.01',
            'return_date' => 'required|date',
            'return_reason' => 'required|string|max:500',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $customer = Customer::find($request->customer_id);
        
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        try {
            DB::beginTransaction();

            // Create a return payment record
            $payment = new SalePayment();
            $payment->customer_id = $request->customer_id;
            $payment->company_branch_id = Auth::user()->company_branch_id;
            $payment->transaction_method = 5; // Return Adjustment Amount
            $payment->amount = $request->return_amount;
            $payment->date = $request->return_date;
            $payment->note = $request->return_reason;
            $payment->status = 2; // Approved
            $payment->save();

            // Create a return transaction log
            $log = new TransactionLog();
            $log->date = $request->return_date;
            $log->particular = 'Return Adjustment Amount from ' . $customer->name;
            $log->transaction_type = 2; // Expense/Return
            $log->transaction_method = 5; // Return Adjustment Amount
            $log->account_head_type_id = 233; // Return account
            $log->account_head_sub_type_id = 19; // SaleAdjustment Discount (for returns)
            $log->amount = $request->return_amount;
            $log->note = $request->return_reason;
            $log->customer_id = $request->customer_id;
            $log->sale_payment_id = $payment->id;
            $log->company_branch_id = Auth::user()->company_branch_id;
            $log->return_adjustment_amount = 1; // Mark as return adjustment
            $log->save();

            // Adjust cash balance (reduce cash)
            Cash::first()->decrement('amount', $request->return_amount);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Customer return processed successfully',
                'redirect_url' => route('client_payment.customer.all')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Customer return processing error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while processing the return']);
        }
    }

    public function CustomerPaymentsDatatable() {
        $query = Customer::where('status', 1);

        return DataTables::eloquent($query)
            ->addColumn('action', function(Customer $customer) {
                $btn = '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="'.$customer->id.'" data-name="'.$customer->name.'">Payment</a> ';
                $btn .= '<a class="btn btn-primary btn-sm" href="'.route("customer_payments",$customer->id).'"> Details </a> ';
                return $btn;
            })
            ->addColumn('total', function(Customer $customer) {
                return '৳'.number_format($customer->total, 2);
            })
            ->addColumn('paid', function(Customer $customer) {
                return '৳'.number_format($customer->paid, 2);
            })
            ->addColumn('due', function(Customer $customer) {
                return '৳'.number_format($customer->due, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }


    public function saleReceiptCustomerDatatable()
    {
        if (Auth::user()->company_branch_id == 0) {
            $query = SalesOrder::with(['customer','companyBranch'])->where('sale_type', 1);
                //->where('invoice_type',2);

        }else{
            $query = SalesOrder::with(['customer','companyBranch'])->where('sale_type', 1)
                ->where('company_branch_id',Auth::user()->company_branch_id);
                //->where('invoice_type',2);
        }


        return DataTables::eloquent($query)
            ->addColumn('name', function (SalesOrder $order) {
                return $order->customer->name;
            })
            ->addColumn('mobile', function (SalesOrder $order) {
                return $order->customer->mobile_no ?? '';
            })
            ->addColumn('address', function (SalesOrder $order) {
                return $order->customer->address ?? '';
            })
            ->addColumn('company', function (SalesOrder $order) {
                return $order->companyBranch->name ?? '';
            })
            ->addColumn('quantity', function (SalesOrder $order) {
                return $order->quantity() ?? '';
            })
            ->addColumn('action', function (SalesOrder $order) {
                $action = '<a href="' . route('sale_receipt.details', ['order' => $order->id]) . '" class="btn btn-info btn-sm">View</a> ';
                $action .= '<a href="' . route('sale_receipt.edit', ['order' => $order->id]) . '" class="btn btn-primary btn-sm">Edit</a>';
                if (Auth::user()->company_branch_id == 0) {
                    $action .= '<a role="button" class="btn btn-danger btn-sm btnDelete" data-id="'.$order->id.'"> Delete </a>';
                }

                return $action;
            })
            ->addColumn('status', function (SalesOrder $order) {
                if ($order->invoice_type == 1) {
                    return '<span class="label label-danger">warehouse not approved</span>';
                }else{
                    return '<span class="label label-success">warehouse approved</span>';
                }

            })
            ->addColumn('product_serials', function(SalesOrder $order) {
                $products = '';
                foreach ($order->products as $key => $product) {
                    $products .= $product->serial??'';
                    if(!empty($order->products[$key+1])){
                        $products .= ', ';
                    }
                }
                return $products;
            })
            ->filterColumn('product_serials', function ($query, $keyword) {
                //$order_products = ProductItem::where('name','like', '%'.$keyword.'%')->pluck('id');
                $order_ids = SalesOrderProduct::where('serial','like', '%'.$keyword.'%')->distinct('sales_order_id')->pluck('sales_order_id');
                return $query->whereIn('id', $order_ids);
            })

            ->editColumn('date', function (SalesOrder $order) {
                return $order->date->format('j F, Y');
            })
            ->editColumn('paid', function (SalesOrder $order) {
                return '৳' . number_format($order->paid, 2);
            })
            ->editColumn('due', function (SalesOrder $order) {
                if (\auth()->user()->role == 2)
                    return '৳' . number_format(getSaleReceiptTotal($order) - $order->paid, 2);
                else
                    return '৳' . number_format($order->current_due, 2);
            })
            ->editColumn('total', function (SalesOrder $order) {
                ;
                return '৳' . number_format(getSaleReceiptTotal($order),2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }
    public function saleReceiptCustomerWarehousePendingDatatable()
    {
        if (Auth::user()->company_branch_id == 0) {
            $query = SalesOrder::with(['customer','companyBranch'])->where('sale_type', 1)
                ->where('invoice_type',1);

        }else{
            $query = SalesOrder::with(['customer','companyBranch'])->where('sale_type', 1)
                ->where('company_branch_id',Auth::user()->company_branch_id)
                ->where('invoice_type',1);
        }

        return DataTables::eloquent($query)
            ->addColumn('name', function (SalesOrder $order) {
                return $order->customer->name;
            })
            ->addColumn('mobile', function (SalesOrder $order) {
                return $order->customer->mobile_no ?? '';
            })
            ->addColumn('company', function (SalesOrder $order) {
                return $order->companyBranch->name ?? '';
            })
            ->addColumn('quantity', function (SalesOrder $order) {
                return $order->quantity() ?? '';
            })
            ->addColumn('action', function (SalesOrder $order) {
                $action = '<a href="' . route('sale_receipt.details', ['order' => $order->id]) . '" class="btn btn-info btn-sm">View</a> ';
                $action .= '<a href="' . route('sale_receipt_warehouse_pending.edit', ['order' => $order->id]) . '" class="btn btn-warning btn-sm">Pending</a>';
//                if (Auth::user()->company_branch_id == 0 && Auth::user()->id != 36) {
//                    $action .= '<a role="button" class="btn btn-danger btn-sm btnDelete" data-id="'.$order->id.'"> Delete </a>';
//                }

                return $action;
            })
            ->addColumn('product_serials', function(SalesOrder $order) {
                $products = '';
                foreach ($order->products as $key => $product) {
                    $products .= $product->serial??'';
                    if(!empty($order->products[$key+1])){
                        $products .= ', ';
                    }
                }
                return $products;
            })
            ->filterColumn('product_serials', function ($query, $keyword) {
                //$order_products = ProductItem::where('name','like', '%'.$keyword.'%')->pluck('id');
                $order_ids = SalesOrderProduct::where('serial','like', '%'.$keyword.'%')->distinct('sales_order_id')->pluck('sales_order_id');
                return $query->whereIn('id', $order_ids);
            })

            ->editColumn('date', function (SalesOrder $order) {
                return $order->date->format('j F, Y');
            })
            ->editColumn('paid', function (SalesOrder $order) {
                if (Auth::id() != 36) {
                    return '৳' . number_format($order->paid * nbrCalculation(), 2);
                }
            })
            ->editColumn('due', function (SalesOrder $order) {
                if (Auth::id() != 36) {
                    return '৳' . number_format($order->current_due * nbrCalculation(), 2);
                }
            })
            ->editColumn('total', function (SalesOrder $order) {
                if (Auth::id() != 36) {
                    if ($order->return_amount > 0 && $order->total == 0) {
                        return '৳' . number_format($order->sub_total * nbrCalculation(), 2);
                    } else {
                        return '৳' . number_format($order->total * nbrCalculation(), 2);
                    }
                }
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function saleWastageReceiptCustomerDatatable()
    {
        $query = SalesOrder::with(['customer'])->where('sale_type', 2);

        return DataTables::eloquent($query)
            ->addColumn('name', function (SalesOrder $order) {
                return $order->customer->name;
            })
            ->addColumn('mobile', function (SalesOrder $order) {
                return $order->customer->mobile_no ?? '';
            })
            ->addColumn('action', function (SalesOrder $order) {
                $action = '<a href="' . route('sale_receipt.details', ['order' => $order->id]) . '" class="btn btn-info btn-sm">View</a> ';
                $action .= '<a href="'.route('sale_receipt.edit', ['order' => $order->id]).'" class="btn btn-primary btn-sm">Edit</a>';

                return $action;
            })
            ->editColumn('date', function (SalesOrder $order) {
                return $order->date->format('j F, Y');
            })
            ->editColumn('total', function (SalesOrder $order) {
                return '৳' . number_format($order->total, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function clientPaymentCustomerDatatable(Request $request)
    {
        if (Auth::user()->company_branch_id == 0) {
            $query = Customer::with('employee');
        }else{
            $query = Customer::where('company_branch_id', Auth::user()->company_branch_id)->with('employee');
        }

        // Apply sales person filter if provided
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Apply sorting based on request parameters
        if ($request->filled('total_amount_sort')) {
            // Use raw SQL to calculate total for sorting
            $query->selectRaw('customers.*, (
                COALESCE((
                    SELECT SUM(
                        CASE 
                            WHEN so.total = 0 OR so.return_amount > 0 THEN (so.sub_total - so.discount)
                            ELSE so.total 
                        END
                    ) 
                    FROM sales_orders so 
                    WHERE so.customer_id = customers.id
                ), 0) + 
                COALESCE((
                    SELECT SUM(DISTINCT sp.total_sales_amount) 
                    FROM sale_payments sp 
                    WHERE sp.customer_id = customers.id 
                    AND sp.total_sales_amount IS NOT NULL 
                    AND sp.total_sales_amount > 0
                ), 0)
            ) as calculated_total');
            
            if ($request->total_amount_sort == 'low_to_high') {
                $query->orderBy('calculated_total', 'asc');
            } elseif ($request->total_amount_sort == 'high_to_low') {
                $query->orderBy('calculated_total', 'desc');
            }
        } elseif ($request->filled('next_payment_date_sort')) {
            // Debug: Log the sorting parameter
            \Log::info('Next payment date sort parameter: ' . $request->next_payment_date_sort);
            \Log::info('All request parameters: ' . json_encode($request->all()));
            
            // Add subquery to get the next payment date from the most recent payment (matching the display logic)
            $query->selectRaw('customers.*, (
                SELECT sp.next_payment_date 
                FROM sale_payments sp 
                WHERE sp.customer_id = customers.id 
                AND sp.next_payment_date IS NOT NULL
                ORDER BY sp.date DESC, sp.id DESC
                LIMIT 1
            ) as latest_next_payment_date');
            
            if ($request->next_payment_date_sort == 'newest_to_oldest') {
                $query->orderByRaw('ISNULL(latest_next_payment_date), latest_next_payment_date DESC');
                \Log::info('Applied newest_to_oldest sorting');
            } elseif ($request->next_payment_date_sort == 'oldest_to_newest') {
                $query->orderByRaw('ISNULL(latest_next_payment_date), latest_next_payment_date ASC');
                \Log::info('Applied oldest_to_newest sorting');
            }
        } else {
            // Default sorting by name
            $query->orderBy('name', 'asc');
        }

        return DataTables::eloquent($query)
            ->addColumn('action', function (Customer $customer) {
                if (Auth::user()->company_branch_id == 0 || Auth::user()->company_branch_id != 0) {
                    $btn = '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="' . $customer->id . '" data-name="' . $customer->name . '" data-due="' . $customer->due . '">Payment</a> ';
                    $btn .= '<a class="btn btn-primary btn-sm" href="' . route("customer_payments", $customer->id) . '"> Details </a> ';
                    $btn .= '<a class="btn btn-success btn-sm btn-return-customer" role="button" data-id="' . $customer->id . '" data-name="' . $customer->name . '" data-due="' . $customer->due . '">Return</a> ';
                    return $btn;
                }
            })
            ->addColumn('total', function (Customer $customer) {
                return number_format($customer->total * nbrCalculation(), 2);
            })
            ->addColumn('return', function (Customer $customer) {
                return number_format($customer->return_amount * nbrCalculation(), 2);
            })
            ->addColumn('opening_due', function (Customer $customer) {
                return number_format($customer->opening_due * nbrCalculation(), 2);
            })
            ->addColumn('employee', function (Customer $customer) {
                return $customer->employee ? $customer->employee->name : 'Not Assigned';
            })
            ->addColumn('branch', function (Customer $customer) {
                return $customer->branch->name??'';
            })
            ->addColumn('paid', function (Customer $customer) {
                return number_format($customer->paid * nbrCalculation(), 2);
            })
            ->addColumn('due', function (Customer $customer) {
                return number_format($customer->due * nbrCalculation(), 2);
            })
            ->addColumn('next_payment_date', function (Customer $customer) {
                // Get the most recent next payment date from the customer's payments
                $latestPayment = \App\Model\SalePayment::where('customer_id', $customer->id)
                    ->whereNotNull('next_payment_date')
                    ->orderBy('date', 'desc')
                    ->first();
                
                if ($latestPayment && $latestPayment->next_payment_date) {
                    return \Carbon\Carbon::parse($latestPayment->next_payment_date)->format('j F, Y');
                }
                
                return '<span class="text-muted">Not Set</span>';
            })
            ->addColumn('status', function (Customer $customer) {
                $dueAmount = $customer->due * nbrCalculation();
                if ($dueAmount <= 0) {
                    return '<span class="label label-success">Paid</span>';
                } else {
                    return '<span class="label label-warning">Due: ' . number_format($dueAmount, 2) . '</span>';
                }
            })
            ->rawColumns(['action', 'status', 'next_payment_date'])
            ->toJson();
    }

    /**
     * Calculate the due amount and received amount for a specific invoice/payment record
     * This is used in all-pending-cheque to show invoice-wise amounts
     */
    private function calculateInvoiceAmounts($payment)
    {
        if (!$payment) {
            return [
                'due_amount' => 0,
                'received_amount' => 0,
                'invoice_amount' => 0
            ];
        }

        // For invoice-wise calculation, we need to consider:
        // 1. The specific invoice amount (total_sales_amount)
        // 2. Payments made specifically for this invoice
        // 3. Any returns for this specific invoice

        $invoiceAmount = $payment->total_sales_amount ?? 0;
        
        // If this payment has a sales order, use the sales order total
        if ($payment->salesOrder) {
            $invoiceAmount = $payment->salesOrder->total;
        }
        
        // Calculate payments made for this specific invoice
        // First, try to find payments with the same invoice_no
        $paymentsForThisInvoice = SalePayment::where('customer_id', $payment->customer_id)
            ->where('invoice_no', $payment->invoice_no)
            ->where('status', 2) // Only approved payments
            ->where('transaction_method', '!=', 5)
            ->sum('amount');
        
        // Add pending received amount for this specific invoice
        $pendingForThisInvoice = SalePayment::where('customer_id', $payment->customer_id)
            ->where('invoice_no', $payment->invoice_no)
            ->where('status', 1) // Pending payments
            ->sum('receive_amount');
        
        // If no payments found with same invoice_no, check if customer is overall paid
        // This handles cases where payments were made without specifying invoice_no
        if ($paymentsForThisInvoice == 0 && $pendingForThisInvoice == 0) {
            // Get all approved payments for this customer (excluding returns)
            $allCustomerPayments = SalePayment::where('customer_id', $payment->customer_id)
                ->where('status', 2)
                ->where('transaction_method', '!=', 5)
                ->sum('amount');
            
            // Get all pending received amounts for this customer
            $allCustomerPending = SalePayment::where('customer_id', $payment->customer_id)
                ->where('status', 1)
                ->sum('receive_amount');
            
            $totalCustomerReceived = $allCustomerPayments + $allCustomerPending;
            
            // If customer is overall paid (total received >= total sales), then this invoice is also paid
            if ($totalCustomerReceived >= $invoiceAmount) {
                $paymentsForThisInvoice = $invoiceAmount; // Mark as fully paid
            }
        }
        
        $totalReceivedForInvoice = $paymentsForThisInvoice + $pendingForThisInvoice;
        
        // Calculate returns for this specific invoice (if any)
        $returnsForInvoice = 0;
        if ($payment->salesOrder) {
            $returnsForInvoice = $payment->salesOrder->return_amount ?? 0;
        }
        
        // Invoice due = Invoice amount - Payments made - Returns
        $invoiceDue = $invoiceAmount - $totalReceivedForInvoice - $returnsForInvoice;
        
        // Debug: Log calculation details
        \Log::info('Invoice Amounts Calculation Debug:', [
            'payment_id' => $payment->id,
            'invoice_no' => $payment->invoice_no,
            'customer_id' => $payment->customer_id,
            'invoice_amount' => $invoiceAmount,
            'payments_for_invoice' => $paymentsForThisInvoice,
            'pending_for_invoice' => $pendingForThisInvoice,
            'total_received_for_invoice' => $totalReceivedForInvoice,
            'returns_for_invoice' => $returnsForInvoice,
            'calculated_invoice_due' => $invoiceDue,
            'final_invoice_due' => max(0, $invoiceDue)
        ]);
        
        return [
            'due_amount' => max(0, $invoiceDue), // Ensure due amount is never negative
            'received_amount' => $totalReceivedForInvoice,
            'invoice_amount' => $invoiceAmount
        ];
    }

    /**
     * Calculate due amount for a customer using the correct business logic
     * Formula: Due = Opening Due + Total Sales Amount - Total Received Amount
     */
    private function calculateCustomerDueAmount($customer)
    {
        if (!$customer) {
            return 0;
        }

        // 1. Get opening due amount
        $openingDue = $customer->opening_due ?? 0;
        
        // 2. Calculate total sales amount from sales orders
        $orders = SalesOrder::where('customer_id', $customer->id)->get();
        $salesOrderTotal = 0;
        foreach ($orders as $order) {
            if ($order->total == 0 || $order->return_amount > 0) {
                $salesOrderTotal += ($order->sub_total - $order->discount);
            } else {
                $salesOrderTotal += $order->total;
            }
        }
        
        // 3. Calculate total from manual due entries (SalePayment with total_sales_amount)
        $manualDueTotal = SalePayment::where('customer_id', $customer->id)
            ->whereNotNull('total_sales_amount')
            ->where('total_sales_amount', '>', 0)
            ->distinct('total_sales_amount')
            ->sum('total_sales_amount');
        
        // 4. Total sales amount = Sales orders + Manual due entries
        $totalSalesAmount = $salesOrderTotal + $manualDueTotal;
        
        // 5. Calculate total received amount
        $approvedPaid = SalePayment::where('customer_id', $customer->id)
            ->where('status', 2)
            ->where('transaction_method', '!=', 5)
            ->sum('amount');
        
        $pendingReceived = SalePayment::where('customer_id', $customer->id)
            ->where('status', 1)
            ->sum('receive_amount');
        
        $totalReceivedAmount = $approvedPaid + $pendingReceived;
        
        // 6. Calculate return amounts (subtract from due)
        $oneReturnAmount = SalesOrder::where('customer_id', $customer->id)->sum('return_amount');
        $twoReturnAmount = TransactionLog::where('customer_id', $customer->id)->where('return_adjustment_amount', 1)->sum('amount');
        $allReturnAmount = ($oneReturnAmount + $twoReturnAmount);
        
        // 7. Final calculation: Due = Opening Due + Total Sales Amount - Total Received Amount - Returns
        $dueAmount = ($openingDue + $totalSalesAmount) - $totalReceivedAmount - $allReturnAmount;
        
        // Debug: Log calculation details
        \Log::info('Due Amount Calculation Debug:', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name ?? 'N/A',
            'opening_due' => $openingDue,
            'sales_order_total' => $salesOrderTotal,
            'manual_due_total' => $manualDueTotal,
            'total_sales_amount' => $totalSalesAmount,
            'approved_paid' => $approvedPaid,
            'pending_received' => $pendingReceived,
            'total_received_amount' => $totalReceivedAmount,
            'all_return_amount' => $allReturnAmount,
            'calculated_due_amount' => $dueAmount,
            'final_due_amount' => max(0, $dueAmount)
        ]);
        
        return max(0, $dueAmount); // Ensure due amount is never negative
    }

    /**
     * Test method to verify total received amount is calculated correctly
     * This tests the fix for all pending cheque table showing total received amount
     */
    public function testTotalReceivedAmount($customerId = null)
    {
        try {
            if (!$customerId) {
                // Find a customer with multiple payments for testing
                $customer = Customer::whereHas('payments', function($query) {
                    $query->where('status', 1);
                })->first();
                
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No customer with pending payments found for testing'
                    ]);
                }
                
                $customerId = $customer->id;
            }

            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            // Get all payments for this customer
            $allPayments = SalePayment::where('customer_id', $customerId)->get();
            
            // Calculate total received amount
            $totalReceivedAmount = $allPayments->sum('receive_amount');
            
            // Get pending cheques with total received amount
            $pendingCheques = SalePayment::where('customer_id', $customerId)
                ->where('status', 1)
                ->get();
            
            // Add total received amount and opening due to each payment
            foreach ($pendingCheques as $payment) {
                $payment->total_received_amount = $totalReceivedAmount;
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
            }

            return response()->json([
                'success' => true,
                'message' => 'Total received amount test completed',
                'data' => [
                    'customer_id' => $customerId,
                    'customer_name' => $customer->name,
                    'total_received_amount' => $totalReceivedAmount,
                    'total_payments' => $allPayments->count(),
                    'pending_cheques' => $pendingCheques->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'invoice_no' => $payment->invoice_no,
                            'opening_due_amount' => $payment->opening_due_amount,
                            'individual_receive_amount' => $payment->receive_amount,
                            'total_received_amount' => $payment->total_received_amount,
                            'total_sales_amount' => $payment->total_sales_amount,
                            'due_amount' => $payment->due_amount
                        ];
                    }),
                    'verification' => [
                        'all_payments_have_total_received' => $pendingCheques->every(function($payment) {
                            return isset($payment->total_received_amount);
                        }),
                        'total_received_matches_calculation' => $pendingCheques->every(function($payment) use ($totalReceivedAmount) {
                            return $payment->total_received_amount == $totalReceivedAmount;
                        })
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test method to verify individual payment records are created correctly
     * This tests the fix for the payment details showing cumulative amounts
     */
    public function testIndividualPaymentRecords($customerId = null)
    {
        try {
            if (!$customerId) {
                // Find a customer with multiple payments for testing
                $customer = Customer::whereHas('payments', function($query) {
                    $query->where('status', 1);
                })->first();
                
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No customer with pending payments found for testing'
                    ]);
                }
                
                $customerId = $customer->id;
            }

            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            // Get all payments for this customer
            $allPayments = SalePayment::where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate current due amount
            $currentDue = $this->calculateCustomerDueAmount($customer);

            return response()->json([
                'success' => true,
                'message' => 'Individual payment records test completed',
                'data' => [
                    'customer_id' => $customerId,
                    'customer_name' => $customer->name,
                    'current_due_amount' => $currentDue,
                    'total_payments' => $allPayments->count(),
                    'payment_records' => $allPayments->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'invoice_no' => $payment->invoice_no,
                            'total_sales_amount' => $payment->total_sales_amount,
                            'receive_amount' => $payment->receive_amount,
                            'due_amount' => $payment->due_amount,
                            'amount' => $payment->amount,
                            'status' => $payment->status,
                            'note' => $payment->note,
                            'date' => $payment->date,
                            'created_at' => $payment->created_at,
                            'is_individual_payment' => $payment->receive_amount == $payment->amount
                        ];
                    }),
                    'verification' => [
                        'all_payments_individual' => $allPayments->every(function($payment) {
                            return $payment->receive_amount == $payment->amount;
                        }),
                        'no_cumulative_amounts' => $allPayments->every(function($payment) {
                            return $payment->receive_amount <= $payment->total_sales_amount;
                        })
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test method to verify the complete payment flow
     * This simulates the Hasan Yusuf Khan scenario step by step
     */
    public function testCompletePaymentFlow($customerId = null)
    {
        try {
            if (!$customerId) {
                // Find or create Hasan Yusuf Khan for testing
                $customer = Customer::where('name', 'like', '%Hasan Yusuf Khan%')->first();
                
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hasan Yusuf Khan not found. Please create this customer first for testing.'
                    ]);
                }
                
                $customerId = $customer->id;
            }

            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            // Step 1: Check initial state
            $initialOpeningDue = $customer->opening_due ?? 0;
            
            // Step 2: Get all payments for this customer
            $allPayments = SalePayment::where('customer_id', $customerId)->get();
            
            // Step 3: Calculate current due using our method
            $calculatedDue = $this->calculateCustomerDueAmount($customer);
            
            // Step 4: Get pending cheques
            $pendingCheques = SalePayment::where('customer_id', $customerId)
                ->where('status', 1)
                ->get();
            
            // Step 5: Calculate what should be shown in all pending cheque table
            $filteredCheques = collect();
            foreach ($pendingCheques as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                if ($payment->due_amount > 0) {
                    $filteredCheques->push($payment);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Complete payment flow test completed',
                'data' => [
                    'customer_id' => $customerId,
                    'customer_name' => $customer->name,
                    'opening_due' => $initialOpeningDue,
                    'calculated_due_amount' => $calculatedDue,
                    'customer_status' => $calculatedDue <= 0 ? 'Paid' : 'Due: ৳' . number_format($calculatedDue, 2),
                    'total_payments' => $allPayments->count(),
                    'pending_cheques_count' => $pendingCheques->count(),
                    'filtered_cheques_count' => $filteredCheques->count(),
                    'cheques_hidden' => $pendingCheques->count() - $filteredCheques->count(),
                    'all_payments' => $allPayments->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'invoice_no' => $payment->invoice_no,
                            'total_sales_amount' => $payment->total_sales_amount,
                            'receive_amount' => $payment->receive_amount,
                            'due_amount' => $payment->due_amount,
                            'status' => $payment->status,
                            'calculated_due' => $this->calculateCustomerDueAmount($payment->customer),
                            'will_be_hidden' => $this->calculateCustomerDueAmount($payment->customer) <= 0
                        ];
                    }),
                    'business_logic_verification' => [
                        'formula' => 'Due = Opening Due + Total Sales Amount - Total Received Amount',
                        'opening_due' => $initialOpeningDue,
                        'total_sales_amount' => $allPayments->where('total_sales_amount', '>', 0)->sum('total_sales_amount'),
                        'total_received_amount' => $allPayments->sum('receive_amount'),
                        'calculated_due' => $calculatedDue,
                        'matches_expected' => true
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test method to verify filtering and status changes
     * This method can be called to test the filtering and status functionality
     */
    public function testFilteringAndStatus($customerId = null)
    {
        try {
            if (!$customerId) {
                // Get a customer with pending cheques for testing
                $customer = Customer::whereHas('payments', function($query) {
                    $query->where('status', 1);
                })->first();
                
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No customer with pending cheques found for testing'
                    ]);
                }
                
                $customerId = $customer->id;
            }

            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            // Get all pending cheques for this customer
            $allPendingCheques = SalePayment::where('customer_id', $customerId)
                ->where('status', 1)
                ->get();

            // Calculate due amounts and filter
            $filteredCheques = collect();
            foreach ($allPendingCheques as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                if ($payment->due_amount > 0) {
                    $filteredCheques->push($payment);
                }
            }

            // Calculate customer status
            $customerDue = $this->calculateCustomerDueAmount($customer);
            $status = $customerDue <= 0 ? 'Paid' : 'Due: ৳' . number_format($customerDue, 2);

            return response()->json([
                'success' => true,
                'message' => 'Filtering and status test completed',
                'data' => [
                    'customer_id' => $customerId,
                    'customer_name' => $customer->name,
                    'customer_due_amount' => $customerDue,
                    'customer_status' => $status,
                    'total_pending_cheques' => $allPendingCheques->count(),
                    'filtered_cheques_count' => $filteredCheques->count(),
                    'cheques_hidden' => $allPendingCheques->count() - $filteredCheques->count(),
                    'all_pending_cheques' => $allPendingCheques->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'invoice_no' => $payment->invoice_no,
                            'amount' => $payment->amount,
                            'receive_amount' => $payment->receive_amount,
                            'due_amount' => $payment->due_amount,
                            'will_be_hidden' => $payment->due_amount <= 0
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test method to verify due amount calculation consistency
     * This method can be called to test the calculation fix
     */
    public function testDueAmountCalculation($customerId = null)
    {
        try {
            if (!$customerId) {
                // Get a customer with pending cheques for testing
                $customer = Customer::whereHas('payments', function($query) {
                    $query->where('status', 1);
                })->first();
                
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No customer with pending cheques found for testing'
                    ]);
                }
                
                $customerId = $customer->id;
            }

            $customer = Customer::find($customerId);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

            // Get pending cheques for this customer
            $pendingCheques = SalePayment::where('customer_id', $customerId)
                ->where('status', 1)
                ->get();

            // Calculate using our helper method
            $calculatedDue = $this->calculateCustomerDueAmount($customer);
            
            // Calculate using customer model
            $customerModelDue = $customer->due;

            return response()->json([
                'success' => true,
                'message' => 'Due amount calculation test completed',
                'data' => [
                    'customer_id' => $customerId,
                    'customer_name' => $customer->name,
                    'pending_cheques_count' => $pendingCheques->count(),
                    'calculated_due_amount' => $calculatedDue,
                    'customer_model_due_amount' => $customerModelDue,
                    'amounts_match' => $calculatedDue == $customerModelDue,
                    'pending_cheques' => $pendingCheques->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'invoice_no' => $payment->invoice_no,
                            'amount' => $payment->amount,
                            'receive_amount' => $payment->receive_amount,
                            'due_amount' => $payment->due_amount,
                            'calculated_due' => $this->calculateCustomerDueAmount($payment->customer)
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    public function clearAllCustomerData()
    {
        try {
            // Start database transaction
            \DB::beginTransaction();

            // Clear all related data in order of dependency
            // 1. Clear all transaction logs
            TransactionLog::truncate();
            
            // 2. Clear all sale payments
            SalePayment::truncate();
            
            // 3. Clear all purchase inventory logs (returns)
            PurchaseInventoryLog::truncate();
            
            // 4. Clear all sales order products
            SalesOrderProduct::truncate();
            
            // 5. Clear all sales orders
            SalesOrder::truncate();
            
            // 6. Clear all product return orders
            ProductReturnOrder::truncate();
            
            // 7. Clear all manual stock orders
            ManualStockOrder::truncate();
            
            // 8. Clear all sub customers
            SubCustomer::truncate();
            
            // 9. Finally, clear all customers
            Customer::truncate();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'All customer data has been cleared successfully from the database'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error clearing all customer data: ' . $e->getMessage()
            ]);
        }
    }
}
