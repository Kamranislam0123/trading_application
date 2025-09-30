<?php

namespace App\Http\Controllers;

use App\Model\AccountHeadSubType;
use App\Model\AccountHeadType;
use App\Model\BankAccount;
use App\Model\Branch;
use App\Model\Cash;
use App\Model\Customer;
use App\Model\Designation;
use App\Model\Employee;
use App\Model\EmployeeTarget;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Model\ProductDescription;
use App\Model\ProductItem;
use App\Model\ProductSalesOrder;
use App\Model\Proposal;
use App\Model\ProposalProduct;
use App\Model\PurchaseInventory;
use App\Model\SalaryProcess;
use App\Model\SalePayment;
use App\Model\SalesOrder;
use App\Model\SubCategory;
use App\Model\SubCustomer;
use App\Model\TransactionLog;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getProduct(Request $request)
    {
        $products = Product::where('product_item_id', $request->productItemId)
            ->where('status', 1)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($products);
    }

    public function getBranch(Request $request)
    {
        $branches = Branch::where('bank_id', $request->bankId)
            ->where('status', 1)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($branches);
    }

    public function getBankAccount(Request $request)
    {
        $accounts = BankAccount::where('branch_id', $request->branchId)
            ->where('status', 1)
            ->orderBy('account_no')
            ->get()->toArray();

        return response()->json($accounts);
    }
    public function getSaleOrder(Request $request)
    {
        $orders = SalesOrder::where('customer_id', $request->customerId)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function getCustomerSalesPerson(Request $request)
    {
        $customer = Customer::find($request->customerId);
        
        if ($customer && $customer->employee_id) {
            return response()->json([
                'success' => true,
                'sales_person_id' => $customer->employee_id,
                'sales_person_name' => $customer->employee ? $customer->employee->name : null
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No sales person assigned to this customer'
        ]);
    }
    public function getCustomer(Request $request)
    {
        $customers = Customer::where('company_branch_id', $request->branchId)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($customers);
    }

    public function orderDetails(Request $request)
    {
        $order = SalesOrder::where('id', $request->orderId)->with('customer')->first()->toArray();

        return response()->json($order);
    }

    public function getAccountHeadType(Request $request)
    {
        $types = AccountHeadType::where('transaction_type', $request->type)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2, 3, 4])
            ->orderBy('name')
            ->get()->toArray();
            //dd($types);

        return response()->json($types);
    }

    public function getAccountHeadSubType(Request $request)
    {
        $subTypes = AccountHeadSubType::where('account_head_type_id', $request->typeId)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2, 3, 4])
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($subTypes);
    }

    public function getAccountHeadSubTypeTrx(Request $request)
    {
        $subTypes = getAccountHeadSubType::where('account_head_type_id', $request->typeId)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2, 3, 4, 5, 6])
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($subTypes);
    }

    public function getDesignation(Request $request)
    {
        $designations = Designation::where('department_id', $request->departmentId)
            ->where('status', 1)
            ->orderBy('name')->get()->toArray();

        return response()->json($designations);
    }

    public function getEmployeeDetails(Request $request)
    {
        $employee = Employee::where('id', $request->employeeId)
            ->with('department', 'designation')->first();

        return response()->json($employee);
    }

    public function getMonth(Request $request)
    {
        $salaryProcess = SalaryProcess::select('month')
        ->where('year', $request->year)
            ->get();

        $proceedMonths = [];
        $result = [];

        foreach ($salaryProcess as $item)
            $proceedMonths[] = $item->month;

        for ($i = 1; $i <= 12; $i++) {
            if (!in_array($i, $proceedMonths)) {
                $result[] = [
                    'id' => $i,
                    'name' => date('F', mktime(0, 0, 0, $i, 10)),
                ];
            }
        }

        return response()->json($result);
    }
    public function getMonthSalaryMonth(Request $request)
    {

        $salaryProcess = SalaryProcess::select('month')
        ->where('year', $request->year)
            ->get();

        $proceedMonths = [];
        $result = [];

        foreach ($salaryProcess as $item)
            $proceedMonths[] = $item->month;

        for ($i = 1; $i <= 12; $i++) {
            if (in_array($i, $proceedMonths)) {
                $result[] = [
                    'id' => $i,
                    'name' => date('F', mktime(0, 0, 0, $i, 10)),
                ];
            }
        }

        return response()->json($result);
    }

    public function get_employee_target(Request $request)
    {
        $employee_target = EmployeeTarget::where([
            'employee_id'=> $request->employee_id,
            'from_date'=> $request->from_date,
            'to_date'=> $request->to_date,
            ])->first();
        if($employee_target){
            return $employee_target->amount;
        }else{
            return 0;
        }
    }


    public function cash(Request $request)
    {
        $cash = Cash::first();
        return view('cash.add', compact('cash'));
    }

    public function cashPost(Request $request)
    {
        $this->validate($request, [
            'opening_balance' => 'required'
        ]);
        $data = $request->all();
        $cash = Cash::first();
        if ($cash) {
            $old_amount = $request->opening_balance - $cash->opening_balance;
            $cash->opening_balance = $request->opening_balance;
            $cash->amount = $cash->amount + $old_amount;
            $cash->save();
        }else {
            $data['amount'] = $request->opening_balance;
            Cash::create($data);
        }
        return redirect()->back()->with('message','Cash added successfully done.');
    }

    public function getSerialSuggestion(Request $request)
    {
        // return PurchaseProduct::take(2)->select('name')->get();
        if ($request->has('term')) {
            $productItemIds = ProductItem::where('name', 'like', '%' . $request->input('term') . '%')->pluck('id');
            $productsIds = PurchaseInventory::with(['productItem','productCategory','productColor','productSize', 'warehouse'])->where('serial', 'like', '%' . $request->input('term') . '%')
                ->orWhereIn('product_item_id', $productItemIds)->take(15)->get();
            return $productsIds;
            // return PurchaseInventory::whereIn('purchase_product_id', $productsIds)->where('quantity','>',0)->get();
        }
    }

    public function getreceivedBySuggestion(Request $request)
    {
        if ($request->has('term')) {
            $receivedBy = SalesOrder::where('received_by', 'like', '%' . $request->input('term') . '%')->limit(10)->get();
            return $receivedBy;
        }
    }
    public function getAddressSuggestion(Request $request)
    {
        if ($request->has('term')) {
            $address = Customer::where('address', 'like', '%' . $request->input('term') . '%')->limit(10)->get();
            return $address;
        }
    }
    public function getMobileSuggestion(Request $request)
    {
        if ($request->has('term')) {
            $mobile_no = Customer::where('mobile_no', 'like', '%' . $request->input('term') . '%')->limit(10)->get();
            return $mobile_no;
        }
    }
    public function getCustomerSuggestion(Request $request)
    {
        if ($request->has('term')) {
            $customerName = Customer::where('name', 'like', '%' . $request->input('term') . '%')->limit(10)->get();
            return $customerName;
        }
    }

    public function getProductItemSuggestion(Request $request)
    {
        // return PurchaseProduct::take(2)->select('name')->get();
        if ($request->has('term')) {
            $productItemIds = ProductItem::where('name', 'like', '%' . $request->input('term') . '%')->select('name','type')->get();
            //dd($productItemIds);
            //$productsIds = PurchaseInventory::with(['productItem','productCategory', 'warehouse'])->where('serial', 'like', '%' . $request->input('term') . '%')
                //->orWhereIn('product_item_id', $productItemIds)->take(15)->get();
            //return $productsIds;


            return $productItemIds;

            // return PurchaseInventory::whereIn('purchase_product_id', $productsIds)->where('quantity','>',0)->get();
        }
    }

    public function getCategoryItemSuggestion(Request $request)
    {
        // return PurchaseProduct::take(2)->select('name')->get();
        if ($request->has('term')) {
            $categoryItemIds = ProductCategory::where('name', 'like', '%' . $request->input('term') . '%')->select('name','type')->get();
            //$productsIds = PurchaseInventory::with(['productItem','productCategory', 'warehouse'])->where('serial', 'like', '%' . $request->input('term') . '%')
                //->orWhereIn('product_item_id', $categoryItemIds)->take(15)->get();
            //return $productsIds;
            return $categoryItemIds;
            // return PurchaseInventory::whereIn('purchase_product_id', $productsIds)->where('quantity','>',0)->get();
        }
    }

    public function getCustomerDue(Request $request)
    {

        return Customer::find($request->customer_id)->due??0;
    }


    public function inventoryTest(Request $request){
        $inventory = PurchaseInventory::first();
        return $inventory->in_product;
    }
    public function getInventoryDetails(Request $request) {

        $product = PurchaseInventory::with('warehouse')->where('id', $request->productId)
            //->where('quantity','>',0)
            ->first();

        return response()->json($product);
    }
    public function getWarehouseWiseProduct(Request $request) {

        $products = PurchaseInventory::with('productItem','productCategory','warehouse')->where('warehouse_id', $request->sourceWarehouseId)
            ->where('quantity','>',0)
            ->get()
            ->toArray();
        //dd($flats);

        return response()->json($products);
    }
    public function getUnitPrice(Request $request) {


        $product_item = ProductItem::where('name',$request->product_item)->first();
        $product_category = ProductCategory::where('name',$request->product_category)->first();

        $product = PurchaseInventory::where('product_item_id', $product_item->id)
            ->where('product_category_id', $product_category->id)
            ->first();

        return response()->json($product);

    }
}
