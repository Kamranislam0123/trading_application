<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeTarget extends Model
{
    protected $guarded = [];
    protected $dates = ['from_date', 'to_date'];
    
    // Add relationship to employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
