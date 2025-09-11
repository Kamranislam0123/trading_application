<?php

namespace App\Http\Controllers;

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
            $totalInvoiceAmount = SalesOrder::sum('total');
            $totalReceivedAmount = SalePayment::where('type', 1)->sum('amount');
            $totalDue = SalesOrder::sum('due');
            
            // Keep today's values for other calculations
            $todaySale = SalesOrder::whereDate('date', date('Y-m-d'))->sum('total');
            $todayYourChoiceSale = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 1)->sum('total');
            $todayYourChoicePlusSale = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 2)->sum('total');
            $todayDue = SalesOrder::whereDate('date', date('Y-m-d'))->sum('due');
            $todayYourChoiceDue = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 1)->sum('due');
            $todayYourChoicePlusDue = SalesOrder::whereDate('date', date('Y-m-d'))->where('company_branch_id', 2)->sum('due');
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
                'recentlyProducts' => $recentlyProducts
            ];
        }else{
            //dd('kj');
            // New total values (all time) for branch users
            $totalInvoiceAmount = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->sum('total');
            $totalReceivedAmount = SalePayment::where('company_branch_id', Auth::user()->company_branch_id)->where('type', 1)->sum('amount');
            $totalDue = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->sum('due');
            
            // Keep today's values for other calculations
            $todaySale = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))->sum('total');
            $todayDue = SalesOrder::where('company_branch_id', Auth::user()->company_branch_id)->whereDate('date', date('Y-m-d'))->sum('due');
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
                'recentlyProducts' => $recentlyProducts
            ];
        }

        return view('dashboard', $data);
    }
}
