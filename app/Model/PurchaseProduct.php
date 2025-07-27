<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    protected $fillable = [
        'category_id', 'sub_category_id', 'name', 'code', 'description', 'status'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class);
    }
}
