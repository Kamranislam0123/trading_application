<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductSalesOrder extends Model
{
    protected $guarded = [];
    protected $table = 'product_sales_order';

    public function product_item(){
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function item_products($sales_order_id, $product_item_id){
        return ProductSalesOrder::where('sales_order_id', $sales_order_id)
            ->where('product_item_id', $product_item_id)
            ->get();
    }
}
