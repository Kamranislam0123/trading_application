<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    protected $guarded=[];

    //protected $dates = ['late_time'];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->with('designation','department');
    }
}
