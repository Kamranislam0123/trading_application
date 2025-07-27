<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClientOperation extends Model
{
    protected $guarded = [];

    public function client_service(){
        return $this->belongsTo(ClientService::class,'client_service_id');
    }
}
