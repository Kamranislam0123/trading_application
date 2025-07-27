<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StockTransferOrder extends Model
{
    protected $guarded = [];

    public function sourchWarehouse() {
        return $this->belongsTo(Warehouse::class,'sourch_warehouse_id');
    }
    public function targetWarehouse() {
        return $this->belongsTo(Warehouse::class,'target_warehouse_id');
    }
    public function products() {
        return $this->hasMany(PurchaseInventoryLog::class)->where('type',10);
    }
    public function quantity() {
        return $this->hasMany(PurchaseInventoryLog::class)
            ->where('type',10)
            ->sum('quantity');
    }
}
