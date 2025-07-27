<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPurchaseProduct extends Model
{
    protected $table = 'purchase_order_purchase_product';
    public function prod(){
        return $this->belongsTo(PurchaseProduct::class,'purchase_product_id','id');
    }
    public function category(){
        return $this->belongsTo(PurchaseProductCategory::class,'purchase_product_category_id','id');
    }
    public function subcategory(){
        return $this->belongsTo(PurchaseProductSubCategory::class,'purchase_product_sub_category_id','id');
    }

}
