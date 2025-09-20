<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use App\Model\PurchaseOrder;
use App\Model\PurchaseOrderProduct;
use App\Model\SalePayment;
use App\Model\SalesOrder;
use App\Model\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {
        //dd('fbf');
        if (Auth::user()->company_branch_id == 0) {
            // New total values (all time)
            // Calculate total invoice amount as sum of all total amounts from due list
            $salesOrderTotal = SalesOrder::sum('total');
            $manualDueTotal = SalePayment::whereNotNull('total_sales_amount')
                ->where('total_sales_amount', '>', 0)
                ->distinct('total_sales_amount')
                ->sum('total_sales_amount');
            $totalInvoiceAmount = $salesOrderTotal + $manualDueTotal;
            
            $totalReceivedAmount = SalePayment::where('type', 1)->sum('amount');
            
            // Calculate total due amount by summing all customer due amounts
            $customers = Customer::where('status', 1)->get();
            $totalDue = 0;
            foreach ($customers as $customer) {
                $totalDue += $customer->due;
            }
            
            // Keep today's values for other calculations
            $todaySale = SalesOrder::whereDate('date', date('Y-m-d'))->sum('total');
            $todayYourChoiceSale = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 1)->sum('total');
            $todayYourChoicePlusSale = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 2)->sum('total');
            $todayDue = SalesOrder::whereDate('date', date('Y-m-d'))->sum('due');
            $todayYourChoiceDue = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 1)->sum('due');
            $todayYourChoicePlusDue = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 2)->sum('due');
            
            // Calculate Today's Total Collection as sum of total due and total receive amount
            $todayTotalDue = $todayDue;
            $todayTotalReceive = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('type', 1)
                ->whereNotIn('transaction_method', [4, 5])
                ->sum('amount');
            $todaySale = $todayTotalDue + $todayTotalReceive;
            $todayDueCollection = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('type', 1)
                ->where('received_type', 2)
                ->whereNotIn('transaction_method', [2])
                ->whereNotIn('transaction_method', [4,5])
                ->sum('amount');
            $todayChoiceDueCollection = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id', 1)
                ->where('type', 1)
                ->whereNotIn('transaction_method', [2])
                ->where('received_type', 2)->sum('amount');
            $todayChoicePlusDueCollection = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id', 2)
                ->where('type', 1)
                ->whereNotIn('transaction_method', [2])
                ->where('received_type', 2)->sum('amount');
            $todayCashSale = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('type', 1)
                ->where('received_type', 1)->sum('amount');
            $todayChoiceCashSale = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id', 1)
                ->where('type', 1)
                ->where('received_type', 1)->sum('amount');
            $todayChoicePlusCashSale = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id', 2)
                ->where('type', 1)
                ->where('received_type', 1)->sum('amount');
            $todayExpense = TransactionLog::whereDate('date', date('Y-m-d'))
                ->whereIn('transaction_type', [3, 2, 6])
                ->whereNotIn('transaction_method', [4, 5])
                ->sum('amount');
            $todayChoiceExpense = TransactionLog::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id',1)
                ->whereIn('transaction_type', [3, 2, 6])->sum('amount');
            $todayChoicePlusExpense = TransactionLog::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id',2)
                ->whereIn('transaction_type', [3, 2, 6])->sum('amount');

            $todaySaleReceipt = SalesOrder::whereDate('date', date('Y-m-d'))
                ->with('customer')
                ->orderBy('created_at', 'desc')->get();
           // $todaySaleReceipt->setPageName('sale_receipt');
            $todayPurchaseReceipt = PurchaseOrder::whereDate('date', date('Y-m-d'))
                ->with('supplier')
                ->orderBy('created_at', 'desc')->get();
           // $todayPurchaseReceipt->setPageName('purchase_receipt');

            // Order Count By Month
            $startDate = [];
            $endDate = [];
            $saleAmountLabel = [];
            $saleAmount = [];

            for($i=11; $i >= 0; $i--) {
                $date = Carbon::now();
                $saleAmountLabel[] = $date->startOfMonth()->subMonths($i)->format('M, Y');
                $startDate[] = $date->format('Y-m-d');
                $endDate[] = $date->endOfMonth()->format('Y-m-d');
            }

            for($i=0; $i < 12; $i++) {
                $saleAmount[] = SalesOrder::where('date', '>=', $startDate[$i])
                    ->where('date', '<=', $endDate[$i])
                    ->sum('total');
            }

            // Product Upload chart
            $orderCount = [];

            for($i=0; $i < 12; $i++) {
                $orderCount[] = SalesOrder::where('date', '>=', $startDate[$i])
                    ->where('date', '<=', $endDate[$i])
                    ->count();
            }

            // Best Seller Products
            $bestSellingItemsSql = "SELECT purchase_order_products.id, count
                FROM purchase_order_products
                LEFT JOIN (SELECT product_item_id, SUM(quantity) count FROM sales_order_products GROUP BY product_item_id) t ON purchase_order_products.id = t.product_item_id
                WHERE purchase_order_products.status = 1
                ORDER BY count DESC
                LIMIT 10";

            $bestSellingItemsResult = DB::select($bestSellingItemsSql);
            $bestSellingItemsIds = [];

            foreach ($bestSellingItemsResult as $item)
                $bestSellingItemsIds[] = $item->id;

            $bestSellingItemsIdsString = implode(",", $bestSellingItemsIds);
            $bestSellingProductsQuery = PurchaseOrderProduct::query();
            $bestSellingProductsQuery->whereIn('id', $bestSellingItemsIds);

            if (count($bestSellingItemsIds) > 0)
                $bestSellingProductsQuery->orderByRaw('FIELD(id,'.$bestSellingItemsIdsString.')');
            $bestSellingProducts = $bestSellingProductsQuery->get();

            foreach ($bestSellingProducts as $product) {
                $product->count = DB::table('sales_order_products')
                    ->where('product_item_id', $product->id)
                    ->sum('quantity');
            }

            // Recently Added Product
            $recentlyProducts = PurchaseOrderProduct::take(10)->latest()->get();

            // Get pending cheques for all branches
            $pendingCheques = SalePayment::where('status', 1)
                ->with(['customer', 'salesPerson'])
                ->orderBy('date', 'desc')
                ->take(20)
                ->get();

            // Update due amounts for each payment
            $filteredPendingCheques = collect();
            foreach ($pendingCheques as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                // Calculate total received amount for this customer
                $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                    ->sum('receive_amount');
                $payment->total_received_amount = $totalReceivedAmount;
                
                // Add opening due amount for this customer
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
                
                // Only include payments that still have a due amount > 0
                if ($payment->due_amount > 0) {
                    $filteredPendingCheques->push($payment);
                }
            }

            // Get payments due next day (tomorrow) - for admin users
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $nextDayPayments = SalePayment::where('status', 1)
                ->where(function($query) use ($tomorrow) {
                    $query->where('next_payment_date', $tomorrow)
                          ->orWhere('next_approximate_payment_date', $tomorrow);
                })
                ->with(['customer', 'salesPerson'])
                ->orderBy('date', 'desc')
                ->get();

            // Update due amounts for next day payments
            $filteredNextDayPayments = collect();
            foreach ($nextDayPayments as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                // Calculate total received amount for this customer
                $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                    ->sum('receive_amount');
                $payment->total_received_amount = $totalReceivedAmount;
                
                // Add opening due amount for this customer
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
                
                // Only include payments that still have a due amount > 0
                if ($payment->due_amount > 0) {
                    $filteredNextDayPayments->push($payment);
                }
            }

            // Get today's payments - for admin users
            $today = date('Y-m-d');
            $todayPayments = SalePayment::where('status', 1)
                ->whereDate('date', $today)
                ->with(['customer', 'salesPerson'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Update due amounts for today's payments
            $filteredTodayPayments = collect();
            foreach ($todayPayments as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                // Calculate total received amount for this customer
                $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                    ->sum('receive_amount');
                $payment->total_received_amount = $totalReceivedAmount;
                
                // Add opening due amount for this customer
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
                
                // Only include payments that still have a due amount > 0
                if ($payment->due_amount > 0) {
                    $filteredTodayPayments->push($payment);
                }
            }

            $data = [
                // New total values
                'totalInvoiceAmount' => $totalInvoiceAmount,
                'totalReceivedAmount' => $totalReceivedAmount,
                'totalDue' => $totalDue,
                
                // Keep today's values for other calculations
                'todaySale' => $todaySale,
                'todayYourChoiceSale' => $todayYourChoiceSale,
                'todayYourChoicePlusSale' => $todayYourChoicePlusSale,
                'todayDue' => $todayDue,
                'todayYourChoiceDue' => $todayYourChoiceDue,
                'todayYourChoicePlusDue' => $todayYourChoicePlusDue,
                'todayDueCollection' => $todayDueCollection,
                'todayChoiceDueCollection' => $todayChoiceDueCollection,
                'todayChoicePlusDueCollection' => $todayChoicePlusDueCollection,
                'todayExpense' => $todayExpense,
                'todayChoiceExpense' => $todayChoiceExpense,
                'todayChoicePlusExpense' => $todayChoicePlusExpense,
                'todayCashSale' => $todayCashSale,
                'todayChoiceCashSale' => $todayChoiceCashSale,
                'todayChoicePlusCashSale' => $todayChoicePlusCashSale,
                'todaySaleReceipt' => $todaySaleReceipt,
                'todayPurchaseReceipt' => $todayPurchaseReceipt,
                'saleAmountLabel' => json_encode($saleAmountLabel),
                'saleAmount' => json_encode($saleAmount),
                'orderCount' => json_encode($orderCount),
                'bestSellingProducts' => $bestSellingProducts,
                'recentlyProducts' => $recentlyProducts,
                'pendingCheques' => $filteredPendingCheques,
                'nextDayPayments' => $filteredNextDayPayments,
                'todayPayments' => $filteredTodayPayments
            ];
        }else{
            //dd('kj');
            // New total values (all time) for branch users
            // Calculate total invoice amount as sum of all total amounts from due list
            $salesOrderTotal = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->sum('total');
            $manualDueTotal = SalePayment::where('company_branch_id', Auth::user()->company_branch_id)
                ->whereNotNull('total_sales_amount')
                ->where('total_sales_amount', '>', 0)
                ->distinct('total_sales_amount')
                ->sum('total_sales_amount');
            $totalInvoiceAmount = $salesOrderTotal + $manualDueTotal;
            
            $totalReceivedAmount = SalePayment::where('company_branch_id', Auth::user()->company_branch_id)->where('type', 1)->sum('amount');
            
            // Calculate total due amount by summing all customer due amounts for this branch
            $customers = Customer::where('status', 1)->where('company_branch_id', Auth::user()->company_branch_id)->get();
            $totalDue = 0;
            foreach ($customers as $customer) {
                $totalDue += $customer->due;
            }
            
            // Keep today's values for other calculations
            $todaySale = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))->sum('total');
            $todayDue = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))->sum('due');
            
            // Calculate Today's Total Collection as sum of total due and total receive amount for branch
            $todayTotalDue = $todayDue;
            $todayTotalReceive = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id', Auth::user()->company_branch_id)
                ->where('type', 1)
                ->whereNotIn('transaction_method', [4, 5])
                ->sum('amount');
            $todaySale = $todayTotalDue + $todayTotalReceive;
            $todayDueCollection = SalePayment::whereDate('date', date('Y-m-d'))
                ->where('company_branch_id', Auth::user()->company_branch_id)
                ->where('type', 1)
                ->where('received_type', 2)
                ->whereNotIn('transaction_method', [2])
                ->sum('amount');
            $todayCashSale = SalePayment::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))
                ->where('type', 1)
                ->where('received_type', 1)->sum('amount');
            $todayExpense = TransactionLog::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))
                ->whereIn('transaction_type', [3, 2, 6])
                ->whereNotIn('transaction_method', [4,5])
                ->sum('amount');

            $todaySaleReceipt = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))
                ->with('customer')
                ->orderBy('created_at', 'desc')->get();
           // $todaySaleReceipt->setPageName('sale_receipt');
            $todayPurchaseReceipt = PurchaseOrder::whereDate('date', date('Y-m-d'))
                ->with('supplier')
                ->orderBy('created_at', 'desc')->get();
           // $todayPurchaseReceipt->setPageName('purchase_receipt');

            // Order Count By Month
            $startDate = [];
            $endDate = [];
            $saleAmountLabel = [];
            $saleAmount = [];

            for($i=11; $i >= 0; $i--) {
                $date = Carbon::now();
                $saleAmountLabel[] = $date->startOfMonth()->subMonths($i)->format('M, Y');
                $startDate[] = $date->format('Y-m-d');
                $endDate[] = $date->endOfMonth()->format('Y-m-d');
            }

            for($i=0; $i < 12; $i++) {
                $saleAmount[] = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->where('date', '>=', $startDate[$i])
                    ->where('date', '<=', $endDate[$i])
                    ->sum('total');
            }

            // Product Upload chart
            $orderCount = [];

            for($i=0; $i < 12; $i++) {
                $orderCount[] = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->where('date', '>=', $startDate[$i])
                    ->where('date', '<=', $endDate[$i])
                    ->count();
            }

            // Best Seller Products
            $bestSellingItemsSql = "SELECT purchase_order_products.id, count
                FROM purchase_order_products
                LEFT JOIN (SELECT product_item_id, SUM(quantity) count FROM sales_order_products GROUP BY product_item_id) t ON purchase_order_products.id = t.product_item_id
                WHERE purchase_order_products.status = 1
                ORDER BY count DESC
                LIMIT 10";

            $bestSellingItemsResult = DB::select($bestSellingItemsSql);
            $bestSellingItemsIds = [];

            foreach ($bestSellingItemsResult as $item)
                $bestSellingItemsIds[] = $item->id;

            $bestSellingItemsIdsString = implode(",", $bestSellingItemsIds);
            $bestSellingProductsQuery = PurchaseOrderProduct::query();
            $bestSellingProductsQuery->whereIn('id', $bestSellingItemsIds);

            if (count($bestSellingItemsIds) > 0)
                $bestSellingProductsQuery->orderByRaw('FIELD(id,'.$bestSellingItemsIdsString.')');
            $bestSellingProducts = $bestSellingProductsQuery->get();

            foreach ($bestSellingProducts as $product) {
                $product->count = DB::table('sales_order_products')
                    ->where('product_item_id', $product->id)
                    ->sum('quantity');
            }

            // Recently Added Product
            $recentlyProducts = PurchaseOrderProduct::take(10)->latest()->get();

            // Get pending cheques for this branch
            // Branch filtering commented out - show all payments regardless of branch
            $pendingCheques = SalePayment::where('status', 1)
                // ->where('company_branch_id', Auth::user()->company_branch_id)
                ->with(['customer', 'salesPerson'])
                ->orderBy('date', 'desc')
                ->take(20)
                ->get();

            // Update due amounts for each payment
            $filteredPendingCheques = collect();
            foreach ($pendingCheques as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                // Calculate total received amount for this customer
                $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                    ->sum('receive_amount');
                $payment->total_received_amount = $totalReceivedAmount;
                
                // Add opening due amount for this customer
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
                
                // Only include payments that still have a due amount > 0
                if ($payment->due_amount > 0) {
                    $filteredPendingCheques->push($payment);
                }
            }

            $data = [
                // New total values
                'totalInvoiceAmount' => $totalInvoiceAmount,
                'totalReceivedAmount' => $totalReceivedAmount,
                'totalDue' => $totalDue,
                
                // Keep today's values for other calculations
                'todaySale' => $todaySale,
                'todayDue' => $todayDue,
                'todayDueCollection' => $todayDueCollection,
                'todayExpense' => $todayExpense,
                'todayCashSale' => $todayCashSale,
                'todaySaleReceipt' => $todaySaleReceipt,
                'todayPurchaseReceipt' => $todayPurchaseReceipt,
                'saleAmountLabel' => json_encode($saleAmountLabel),
                'saleAmount' => json_encode($saleAmount),
                'orderCount' => json_encode($orderCount),
                'bestSellingProducts' => $bestSellingProducts,
                'recentlyProducts' => $recentlyProducts,
                'pendingCheques' => $filteredPendingCheques
            ];

            // Get payments due next day (tomorrow) - for branch users
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $nextDayPayments = SalePayment::where('status', 1)
                ->where('company_branch_id', Auth::user()->company_branch_id)
                ->where(function($query) use ($tomorrow) {
                    $query->where('next_payment_date', $tomorrow)
                          ->orWhere('next_approximate_payment_date', $tomorrow);
                })
                ->with(['customer', 'salesPerson'])
                ->orderBy('date', 'desc')
                ->get();

            // Update due amounts for next day payments
            $filteredNextDayPayments = collect();
            foreach ($nextDayPayments as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                // Calculate total received amount for this customer
                $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                    ->sum('receive_amount');
                $payment->total_received_amount = $totalReceivedAmount;
                
                // Add opening due amount for this customer
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
                
                // Only include payments that still have a due amount > 0
                if ($payment->due_amount > 0) {
                    $filteredNextDayPayments->push($payment);
                }
            }

            // Get today's payments - for branch users
            $today = date('Y-m-d');
            $todayPayments = SalePayment::where('status', 1)
                ->where('company_branch_id', Auth::user()->company_branch_id)
                ->whereDate('date', $today)
                ->with(['customer', 'salesPerson'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Update due amounts for today's payments
            $filteredTodayPayments = collect();
            foreach ($todayPayments as $payment) {
                $payment->due_amount = $this->calculateCustomerDueAmount($payment->customer);
                
                // Calculate total received amount for this customer
                $totalReceivedAmount = SalePayment::where('customer_id', $payment->customer_id)
                    ->sum('receive_amount');
                $payment->total_received_amount = $totalReceivedAmount;
                
                // Add opening due amount for this customer
                $payment->opening_due_amount = $payment->customer->opening_due ?? 0;
                
                // Only include payments that still have a due amount > 0
                if ($payment->due_amount > 0) {
                    $filteredTodayPayments->push($payment);
                }
            }

            // Add payment lists to data array for branch users
            $data['nextDayPayments'] = $filteredNextDayPayments;
            $data['todayPayments'] = $filteredTodayPayments;
        }

        return view('dashboard', $data);
    }

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
        
        return $dueAmount;
    }
}
