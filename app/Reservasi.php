<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\carbon;

class Reservasi extends Model
{
    protected $fillable = [
        'id_customer', 'id_meja', 'id_karyawan', 'tgl_reservasi', 'jam_reservasi', 'status_reservasi'
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
