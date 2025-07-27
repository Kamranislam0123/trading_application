<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    protected $fillable = [
        'product_item_id', 'product_id', 'description',
    ];
    public function productItem() {
        return $this->belongsTo(ProductItem::class);
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
