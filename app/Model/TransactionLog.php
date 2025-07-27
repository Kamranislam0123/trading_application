<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function bank() {
        return $this->belongsTo(Bank::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    public function accountHead() {
        return $this->belongsTo(AccountHeadType::class, 'account_head_type_id', 'id');
    }

    public function accountSubHead() {
        return $this->belongsTo(AccountHeadSubType::class, 'account_head_sub_type_id', 'id');
    }
}
