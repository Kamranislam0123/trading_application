<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index() {
        return view('sale.customer.all');
    }

    public function add() {
        return view('sale.customer.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'opening_due' => 'required|numeric',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->company_branch_id = Auth::user()->company_branch_id;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->opening_due = $request->opening_due;
        $customer->status = $request->status;
        $customer->save();

        return redirect()->route('customer')->with('message', 'Customer add successfully.');
    }

    public function edit(Customer $customer) {
        return view('sale.customer.edit', compact('customer'));
    }

    public function editPost(Customer $customer, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'opening_due' => 'required|numeric',
        ]);

        $customer->name = $request->name;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->opening_due = $request->opening_due;
        $customer->status = $request->status;
        $customer->save();;

        return redirect()->route('customer')->with('message', 'Customer edit successfully.');
    }

    public function datatable() {
        if (Auth::user()->company_branch_id == 0) {
            $query = Customer::with('branch');
        }else{
            $query = Customer::where('company_branch_id', Auth::user()->company_branch_id);
        }


        return DataTables::eloquent($query)
            ->addColumn('action', function(Customer $customer) {
                return '<a class="btn btn-info btn-sm" href="'.route('customer.edit', ['customer' => $customer->id]).'"> Edit';
            })
            ->addColumn('branch', function(Customer $customer) {
                return $customer->branch->name??'';
            })
            ->addColumn('status', function(Customer $customer) {
                if ($customer->status == 1) {
                    return '<span class="label label-success">Active</span>';
                }else {
                    return '<span class="label label-danger">Inactive</span>';
                }
            })
            ->addColumn('branch_status', function(Customer $customer) {
                if ($customer->company_branch_id == 1) {
                    return '<span class="label label-success">Datascape Trading</span>';
                }else {
                    return '<span class="label label-danger">Datascape IT Plus</span>';
                }
            })
            ->rawColumns(['action','status','branch_status'])
            ->toJson();
    }
}
