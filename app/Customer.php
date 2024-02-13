<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;



class Customer extends Model
{

    protected $fillable = [
        'nama_cust', 'email_cust', 'no_telp_cust'
    ];

    public function getCreatedAtAttribute() {
        if(!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }
    
    public function getUpdatedAtAttribute() {
        if(!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    
}
