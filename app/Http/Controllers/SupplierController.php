<?php

namespace App\Http\Controllers;

use App\Model\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index() {
        $suppliers = Supplier::all();

        return view('purchase.supplier.all', compact('suppliers'));
    }

    public function add() {
        return view('purchase.supplier.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'mobile_no' => 'required|digits:11',
            'alternative_mobile_no' => 'nullable|digits:11',
            'email' => 'nullable|email|string|max:255',
            'address' => 'required|string|max:255',
            'opening_due' => 'required|numeric',
            'status' => 'required'
        ]);

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->owner_name = $request->owner_name;
        $supplier->mobile = $request->mobile_no;
        $supplier->alternative_mobile = $request->alternative_mobile_no;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->opening_due = $request->opening_due;
        $supplier->status = $request->status;
        $supplier->save();

        return redirect()->route('supplier')->with('message', 'Supplier add successfully.');
    }

    public function edit(Supplier $supplier) {
        return view('purchase.supplier.edit', compact('supplier'));
    }

    public function editPost(Supplier $supplier, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'mobile_no' => 'required|digits:11',
            'alternative_mobile_no' => 'nullable|digits:11',
            'email' => 'nullable|email|string|max:255',
            'address' => 'required|string|max:255',
            'opening_due' => 'required|numeric',
            'status' => 'required'
        ]);

        $supplier->name = $request->name;
        $supplier->owner_name = $request->owner_name;
        $supplier->mobile = $request->mobile_no;
        $supplier->alternative_mobile = $request->alternative_mobile_no;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->opening_due = $request->opening_due;
        $supplier->status = $request->status;
        $supplier->save();

        return redirect()->route('supplier')->with('message', 'Supplier edit successfully.');
    }
}
