<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id', 'name', 'name', 'status'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
