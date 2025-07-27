<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClientManagement extends Model
{
    protected $guarded = [];
    protected $dates = ['date','last_updated','last_contact_date'];

    public function marketing(){
        return $this->belongsTo(Employee::class,'marketing_id');
    }

    public function client_service(){
        return $this->belongsTo(ClientService::class,'client_service_id');
    }

    public function client_source(){
        return $this->belongsTo(ClientSource::class,'client_source_id');
    }

    public function employee_client_orders($marketing_id){
        return ClientManagement::where('marketing_id', $marketing_id)->get();
    }

    public function employee_monthly_client_orders($marketing_id,$month,$year){
        $client = ClientManagement::where([
            'marketing_id' => $marketing_id,
            'status' => 4,
            ])->whereMonth('order_complete_date', $month)->whereYear('order_complete_date', $year)->get();
        $employee_target = EmployeeTarget::where([
            'employee_id'=> $marketing_id,
            'month'=> $month,
            'year'=> $year,
            ])->first();   
            
        return [
            'data' => $client,
            'target' => $employee_target->amount??0,
        ];
    }

}
