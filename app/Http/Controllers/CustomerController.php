<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use App\Model\Employee;
use App\Model\Bank;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();
        return view('sale.customer.all', compact('banks', 'employees'));
    }

    public function add() {
        $employees = Employee::orderBy('name')->get();
        return view('sale.customer.add', compact('employees'));
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'opening_due' => 'required|numeric',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->company_branch_id = Auth::user()->company_branch_id;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->employee_id = $request->employee_id;
        $customer->opening_due = $request->opening_due;
        $customer->status = $request->status;
        $customer->save();

        return redirect()->route('customer')->with('message', 'Customer add successfully.');
    }

    public function edit(Customer $customer) {
        $employees = Employee::orderBy('name')->get();
        return view('sale.customer.edit', compact('customer', 'employees'));
    }

    public function editPost(Customer $customer, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
            'opening_due' => 'required|numeric',
        ]);

        $customer->name = $request->name;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->employee_id = $request->employee_id;
        $customer->opening_due = $request->opening_due;
        $customer->status = $request->status;
        $customer->save();

        return redirect()->route('customer')->with('message', 'Customer edit successfully.');
    }

    public function datatable(Request $request) {
        if (Auth::user()->company_branch_id == 0) {
            $query = Customer::with('branch', 'employee');
        }else{
            $query = Customer::where('company_branch_id', Auth::user()->company_branch_id)->with('employee');
        }

        // Apply sales person filter if provided
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return DataTables::eloquent($query)
            ->addColumn('action', function(Customer $customer) {
                $btn = '<a class="btn btn-info btn-sm" href="'.route('customer.edit', ['customer' => $customer->id]).'" style="margin-bottom: 10px;">Edit</a>';
                $btn .= '<a class="btn btn-success btn-sm btn-pay" role="button" data-id="' . $customer->id . '" data-name="' . $customer->name . '" data-due="' . $customer->due . '">Payment</a>';
                return $btn;
            })
            ->addColumn('branch', function(Customer $customer) {
                return $customer->branch->name??'';
            })
            ->addColumn('employee', function(Customer $customer) {
                return $customer->employee ? $customer->employee->name : 'Not Assigned';
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
                    return '<span class="label label-success">AT International</span>';
                }else {
                    return '<span class="label label-danger">Datascape IT Plus</span>';
                }
            })
            ->rawColumns(['action','status','branch_status'])
            ->toJson();
    }
}
