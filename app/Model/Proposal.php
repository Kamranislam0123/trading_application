<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $guarded = [];
    protected $dates = ['date', 'created_at'];

    public function products()
    {
        return $this->hasMany(ProposalProduct::class, 'proposal_id');
    }

    public function product_items()
    {
        return $this->hasMany(ProposalProduct::class, 'proposal_id')
            // ->where('product_item_id', $product_item_id)
            ->groupBy('product_item_id');
            // ->get();
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function subCustomer() {
        return $this->belongsTo(SubCustomer::class);
    }

    public function user() {
        return $this->belongsTo(User::class,'created_by');
    }

}
