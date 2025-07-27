<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseInventory extends Model
{
    protected $guarded = [];

    public function productItem() {
        return $this->belongsTo(ProductItem::class);
    }

    public function productCategory() {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productColor() {
        return $this->belongsTo(ProductColor::class);
    }

    public function productSize() {
        return $this->belongsTo(ProductSize::class);
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public function getInProductAttribute() {
        return PurchaseInventoryLog::where('purchase_inventory_id', $this->id)->where('type', 1)->sum('quantity');
    }

    public function getOutProductAttribute() {
        return PurchaseInventoryLog::where('purchase_inventory_id', $this->id)->where('type', 2)->sum('quantity');
    }
}
