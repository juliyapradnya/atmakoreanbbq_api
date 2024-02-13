<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaksi extends Model
{
    protected $fillable = [
        'id_pesanan', 'id_karyawan', 'kode_transaksi', 'jenis_pembayaran', 'no_kartu', 'nama_pemilik_kartu', 'kode_verifikasi', 'tgl_transaksi', 'total_sub_total', 'service', 'tax', 'total_harga'
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
