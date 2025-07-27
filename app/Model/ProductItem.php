<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    protected $guarded = [];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
