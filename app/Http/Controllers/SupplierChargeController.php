<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\MobileBanking;
use App\Model\PurchaseOrder;
use App\Model\Supplier;
use App\Model\SupplierCharge;
use App\Model\SupplierChargePayment;
use App\Model\TransactionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;

class SupplierChargeController extends Controller
{
    public function index() {
        $banks  = Bank::where("status",1)->get();
        $charges = Supplier::all();
        return view('supplier_charge.all', compact('charges','banks'));
    }

    public function add() {
        $suppliers = Supplier::all();
        return view('supplier_charge.add',compact("suppliers"));
    }

    public function addPost(Request $request) {
        $request->validate([
            'supplier' => 'required',
            'charge' => 'required',
            'description' => 'required',
            'date' => 'required'
        ]);
        $supplier = Supplier::find($request->supplier);
        $charge = new SupplierCharge();
        $charge->supplier_id = $request->supplier;
        $charge->description = $request->description;
        $charge->charge = $request->charge;
        $charge->date = $request->date;
        $charge->save();
        $supplier->increment("charge",$request->charge);
        $supplier->increment("charge_due",$request->charge);

        return redirect()->route('supplier_charge')->with('message', 'Supplier Charge add successfully.');

    }

    public function edit(SupplierCharge $supplierCharge) {
        $suppliers = Supplier::all();
        return view('supplier_charge.edit', compact('suppliers','supplierCharge'));
    }

    public function editPost(SupplierCharge $supplierCharge, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $bank->name = $request->name;
        $bank->status = $request->status;
        $bank->save();

        return redirect()->route('supplier_charge')->with('message', 'Bank edit successfully.');
    }
    public function supplierChargeDetails(Request $request){
        $supplier = SupplierCharge::where('id', $request->supplierId)->first()->toArray();
        return response()->json($supplier);
    }
    public function supplierChargePayment(Request $request){
        //dd($request->all());
        $rules = [
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

//        if ($request->order != '') {
//            $order = PurchaseOrder::find($request->order);
//            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
//        }

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

        $supplier  = Supplier::find($request->supplier_id);

        if ($request->payment_type == 1 ) {
            $payment = new SupplierChargePayment();
            $payment->supplier_id = $request->supplier_id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

                Cash::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$supplier->name;
            $log->transaction_type = 3;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 5;
            $log->account_head_sub_type_id = 5;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->supplier_id = $supplier->id;
            $log->supplier_charge_payment_id = $payment->id;
            $log->save();

        } elseif ($request->payment_type == 2 ) {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/supplier_charge_payment';
                $file->move($destinationPath, $filename);

                $image = 'uploads/supplier_charge_payment/'.$filename;
            }

            $payment = new SupplierChargePayment();
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
            $log->particular = 'Paid to '.$supplier->name;
            $log->transaction_type = 3;
            $log->transaction_method = 2;
            $log->account_head_type_id = 5;
            $log->account_head_sub_type_id = 5;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->supplier_id = $supplier->id;
            $log->supplier_charge_payment_id = $payment->id;
            $log->save();
        }

        $supplier->increment('charge_paid', $request->amount);
        $supplier->decrement('charge_due', $request->amount);

//        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('supplier_charge.payment_details', ['payment' => $payment->id])]);

    }
    public function paymentDetails(SupplierChargePayment $payment){
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('supplier_charge.payment_details', compact('payment'));
    }
    public function paymentPrint(SupplierChargePayment $payment){
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('supplier_charge.payment_print', compact('payment'));
    }
    public function supplierServiceChargeDetails(Supplier $supplier){
        $payments = SupplierChargePayment::where("supplier_id",$supplier->id)->get();
        return view('supplier_charge.supplier_charge_details', compact('payments'));

    }
}
