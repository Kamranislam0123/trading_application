<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductReturnOrder extends Model
{
    protected $guarded = [];
    protected $appends = [
        'quantity'
    ];

    public function logs() {
        return $this->hasMany(PurchaseInventoryLog::class);
    }
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function getQuantityAttribute(){
        return $this->hasMany(PurchaseInventoryLog::class)->sum('quantity');
    }
}
