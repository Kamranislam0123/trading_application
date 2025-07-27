<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use App\Model\SubCustomer;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Validation\Rule;

class SubCustomerController extends Controller
{
    public function index()
    {
        return view('sale.sub_customer.all');
    }

    public function add()
    {
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        return view('sale.sub_customer.add', compact('customers'));
    }

    public function addPost(Request $request)
    {
        $request->validate([
            'customer' => 'required',
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'sub_customer_old_id' => 'nullable|unique:sub_customers',
        ]);

        $sub_customer = new SubCustomer();
        $sub_customer->customer_id = $request->customer;
        $sub_customer->name = $request->name;
        $sub_customer->mobile_no = $request->mobile_no;
        $sub_customer->address = $request->address;
        $sub_customer->sub_customer_old_id = $request->sub_customer_old_id;
        $sub_customer->save();

        return redirect()->route('sub_customer')->with('message', 'Sub Customer add successfully.');
    }

    public function edit(SubCustomer $subCustomer)
    {
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        return view('sale.sub_customer.edit', compact('subCustomer', 'customers'));
    }

    public function editPost(SubCustomer $subCustomer, Request $request)
    {
        $request->validate([
            'customer' => 'required',
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'sub_customer_old_id' => 'nullable|unique:sub_customers,sub_customer_old_id,'. $subCustomer->id,
            Rule::unique('sub_customers')->ignore($subCustomer->id),
        ]);

        $subCustomer->customer_id = $request->customer;
        $subCustomer->name = $request->name;
        $subCustomer->mobile_no = $request->mobile_no;
        $subCustomer->address = $request->address;
        $subCustomer->sub_customer_old_id = $request->sub_customer_old_id;
        $subCustomer->save();;

        return redirect()->route('sub_customer')->with('message', 'Sub Customer edit successfully.');
    }

    public function datatable()
    {
        $query = SubCustomer::with(['customer']);

        return DataTables::eloquent($query)
            ->addColumn('customer', function (SubCustomer $subCustomer) {
                return $subCustomer->customer->name??'';
            })
            ->addColumn('action', function (SubCustomer $subCustomer) {
                return '<a class="btn btn-info btn-sm" href="' . route('sub_customer.edit', ['subCustomer' => $subCustomer->id]) . '"> Edit</a> ';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
