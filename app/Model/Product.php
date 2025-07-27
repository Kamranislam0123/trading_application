<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_item_id', 'name', 'code', 'image', 'description', 'status'
    ];

    public function productItem() {
        return $this->belongsTo(ProductItem::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }
}
