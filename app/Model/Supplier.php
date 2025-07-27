<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function getDueAttribute() {
        $supplier = Supplier::find($this->id);
        $total = PurchaseOrder::where('supplier_id', $this->id)->sum('total');
        $paid = PurchasePayment::where('supplier_id', $this->id)->sum('amount');
        return $total - $paid + $supplier->opening_due;
    }

    public function getPaidAttribute() {
        return PurchasePayment::where('supplier_id', $this->id)->sum('amount');
    }

    public function getTotalAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('total');
    }

    public function getSaleOrderDueAttribute() {
        return SalesOrder::where('supplier_id', $this->id)->sum('due');
    }

    public function getSaleOrderPaidAttribute() {
        return SalesOrder::where('supplier_id', $this->id)->sum('paid');
    }

    public function getSaleOrderTotalAttribute() {
        return SalesOrder::where('supplier_id', $this->id)->sum('total');
    }
}
