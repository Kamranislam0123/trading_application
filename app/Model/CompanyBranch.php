<?php

namespace App\Model;
use App\User;

use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $fillable = [
        'name', 'address', 'status',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
