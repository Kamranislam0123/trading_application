<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function bank() {
        return $this->belongsTo(Bank::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }
}
