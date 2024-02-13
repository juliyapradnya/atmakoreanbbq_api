<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;  //tambahkan ini
use Carbon\Carbon; //import library time

class User extends Authenticatable
{
    use Notifiable, HasApiTokens; //edit ini
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_karyawan', 'jenis_kelamin_karyawan', 'no_telp_karyawan', 'posisi_karyawan', 'tgl_gabung', 'email', 'password', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    } //convert atribute created_at ke format Y-m-d H:i:s

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    } //convert atribute created_at ke format Y-m-d H:i:s
    
    protected $dates = ['deleted_at'];
}

