<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function getDueAttribute() {
        $customer = Customer::find($this->id);
        
        // Calculate total from sales orders
        $orders = SalesOrder::where('customer_id', $this->id)->get();
        $salesOrderTotal = 0;
        foreach ($orders as $order) {
            if ($order->total == 0 || $order->return_amount > 0) {
                $salesOrderTotal += ($order->sub_total-$order->discount);
            }else{
                $salesOrderTotal += $order->total;
            }
        }
        
        // Calculate total from manual due entries (SalePayment with total_sales_amount)
        $manualDueTotal = SalePayment::where('customer_id', $this->id)
            ->whereNotNull('total_sales_amount')
            ->where('total_sales_amount', '>', 0)
            ->distinct('total_sales_amount')
            ->sum('total_sales_amount');
        
        $total = $salesOrderTotal + $manualDueTotal;
        
        // Get paid amount using the same logic as getPaidAttribute()
        $approvedPaid = SalePayment::where('customer_id', $this->id)
            ->where('status', 2)
            ->where('transaction_method', '!=', 5)
            ->sum('amount');
        
        $pendingReceived = SalePayment::where('customer_id', $this->id)
            ->where('status', 1)
            ->sum('receive_amount');
        
        $paid = $approvedPaid + $pendingReceived;
        
        // Calculate return amounts (these should reduce the due amount)
        $one_return_amount = SalesOrder::where('customer_id', $this->id)->sum('return_amount');
        $two_return_amount = TransactionLog::where('customer_id', $this->id)->where('return_adjustment_amount',1)->sum('amount');
        $allReturnAmount = ($one_return_amount + $two_return_amount);
        
        // Final calculation: Due = Opening Due + Total Sales Amount - Total Received Amount - Returns
        return ($total - $paid - $allReturnAmount) + $customer->opening_due;
    }

    public function getPaidAttribute() {
        // Get paid amount from approved payments (excluding returns)
        $approvedPaid = SalePayment::where('customer_id', $this->id)
            ->where('status', 2)
            ->where('transaction_method', '!=', 5)
            ->sum('amount');
        
        // Get receive amount from pending payments
        $pendingReceived = SalePayment::where('customer_id', $this->id)
            ->where('status', 1)
            ->sum('receive_amount');
        
        return $approvedPaid + $pendingReceived;
    }

    public function getTotalAttribute() {
        // Get total from sales orders
        $salesOrderTotal = SalesOrder::where('customer_id', $this->id)->sum('total');
        
        // Get total from manual due entries (SalePayment with total_sales_amount)
        // Count all payments to get the total sales amount
        $manualDueTotal = SalePayment::where('customer_id', $this->id)
            ->whereNotNull('total_sales_amount')
            ->where('total_sales_amount', '>', 0)
            ->distinct('total_sales_amount')
            ->sum('total_sales_amount');
        
        return $salesOrderTotal + $manualDueTotal;
    }

    public function getReturnAmountAttribute() {
        // Get return amounts from sales orders
        $salesOrderReturns = SalesOrder::where('customer_id', $this->id)->sum('return_amount');
        
        // Get return amounts from transaction logs (return adjustment amounts)
        $transactionLogReturns = TransactionLog::where('customer_id', $this->id)
            ->where('return_adjustment_amount', 1)
            ->sum('amount');
        
        return $salesOrderReturns + $transactionLogReturns;
    }

    public function getRefundAttribute() {
        return SalesOrder::where('customer_id', $this->id)->sum('refund');
    }
    public function branch(){
        return $this->belongsTo(CompanyBranch::class,'company_branch_id','id');
    }

    public function getQuantityAttribute() {
        $orders = SalesOrder::where('customer_id', $this->id)->pluck('id');
        $totalQuantity = SalesOrderProduct::whereIn('sales_order_id',$orders)->sum('quantity');
        return $totalQuantity;
    }
}
