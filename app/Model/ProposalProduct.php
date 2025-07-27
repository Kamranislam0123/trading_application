<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProposalProduct extends Model
{
    protected $guarded = [];

    public function productItem(){
        return $this->belongsTo(ProductItem::class);
    }

    public function item_products($proposal_id, $product_item_id){
        return ProposalProduct::where('proposal_id', $proposal_id)
            ->where('product_item_id', $product_item_id)
            ->get();
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
}
