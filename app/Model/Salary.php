<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $dates = ['date'];

    protected $fillable = [
        'salary_process_id', 'employee_id', 'date', 'month', 'year', 'basic_salary',
        'house_rent', 'travel', 'medical', 'tax', 'others_deduct', 'gross_salary'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->with('department','designation');
    }

    public function getDueAttribute() {

        return EmployeeAttendance::where('supplier_id', $this->id)->sum('due');
    }



}
