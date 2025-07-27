<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubCustomer extends Model
{
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getDueAttribute()
    {
        return SalesOrder::where('sub_customer_id', $this->id)->sum('due');
    }

    public function getPaidAttribute()
    {
        return SalesOrder::where('sub_customer_id', $this->id)->sum('paid');
    }

    public function getTotalAttribute()
    {
        return SalesOrder::where('sub_customer_id', $this->id)->sum('total');
    }

}
