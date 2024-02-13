<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Detail extends Model
{
    protected $fillable = [
        'id_menu', 'id_pesanan', 'qty', 'harga', 'sub_total', 'status_pembayaran'
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
