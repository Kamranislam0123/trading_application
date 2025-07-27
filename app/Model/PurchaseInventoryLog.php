<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseInventoryLog extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class,'purchase_order_id');
    }
    public function saleOrder()
    {
        return $this->belongsTo(SalesOrder::class,'sales_order_id');
    }
    public function saleReturnOrder()
    {
        return $this->belongsTo(PurchaseInventoryLog::class,'product_return_order_id');
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productColor()
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseInventory() {
        return $this->belongsTo(PurchaseInventory::class);
    }
    public function transferOrder() {
        return $this->belongsTo(StockTransferOrder::class,'stock_transfer_order_id');
    }
}
