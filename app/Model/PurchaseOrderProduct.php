<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model
{
    protected $guarded = [];
    protected $dates = ['created_at'];

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
}
