<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\carbon;

class Stok extends Model
{
    protected $fillable = [
        'id_menu', 'id_karyawan', 'jumlah_stok_masuk', 'harga_stok', 'unit_stok', 'tanggal_stok_masuk','sisa_stok'
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
