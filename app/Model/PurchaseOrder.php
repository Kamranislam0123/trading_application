<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = [];
    protected $dates = ['date','created_at'];

    public function products() {
        return $this->hasMany(PurchaseOrderProduct::class);
    }
    public function order_products() {
        return $this->hasMany(PurchaseOrderProduct::class,'purchase_order_id','id');
    }

    public function quantity(){
        return $this->hasMany(PurchaseOrderProduct::class)->sum('quantity');
    }


    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function payments() {
        return $this->hasMany(PurchasePayment::class);
    }
}
