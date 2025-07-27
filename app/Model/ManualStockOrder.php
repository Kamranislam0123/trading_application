<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ManualStockOrder extends Model
{
    public function logs() {
        return $this->hasMany(PurchaseInventoryLog::class);
    }
    public function order_products() {
        return $this->hasMany(PurchaseOrderProduct::class,'purchase_order_id','id');
    }
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }
}
