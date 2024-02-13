<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\Reservasi; 
use App\Meja;

class ReservasiController extends Controller
{
    //read

    public function index(){
        $reservasi = DB::table('reservasis')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('users','users.id','=','reservasis.id_karyawan')
                    -> select('reservasis.*','customers.nama_cust', 'mejas.no_meja', 'users.nama_karyawan')
                    -> get();
        
        if(count($reservasi) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $reservasi
            ],200);
        } //return data semua reservasi dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data reservasi kosong
    } 

    //search

    public function show($id){
        $reservasi = Reservasi::find($id); //mencari data reservasi berdasarkan id

        if(!is_null($reservasi)){
            return response([
                'message' => 'Retrieve reservasi Success',
                'data' => $reservasi
            ],200);
        } //return data reservasi yang ditemukan dalam bentuk json

        return response([
            'message' => 'reservasi Not Found',
            'data' => null
        ],404); //return message saat data reservasi tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_customer' => 'required|numeric',
            'id_meja' => 'required|numeric',
            'id_karyawan' => 'required|numeric',
            'tgl_reservasi' => 'required|date-format:Y-m-d|after:yesterday',
            'jam_reservasi' => 'required|in:lunch,dinner,on the spot',
            'status_reservasi' => 'required|in:selesai,belum selesai',

        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $reservasi = Reservasi::create($storeData); //menambah data Reservasi baru
        return response([
            'message' => 'Add Reservasi Success',
            'data' => $reservasi,
        ],200); //return data Reservasi baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $reservasi = Reservasi::find($id); //mencari data reservasi berdasarkan id
        
        if(is_null($reservasi)){
            return response([
                'message' => 'reservasi Not Found',
                'data' => null
            ],404);
        } //return message saat data Reservasi tidak ditemukan

        if($reservasi->delete()){
            return response([
                'message' => 'Delete reservasi Success',
                'data' => $reservasi,
            ],200);
        } //return message saat berhasil menghapus data reservasi
        return response([
            'message' => 'Delete reservasi Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data reservasi
    }

    //(update)

    public function update(Request $request, $id){
        $reservasi = Reservasi::find($id); //mencari data reservasi berdasarkan id
        if(is_null($reservasi)){
            return response([
                'message' => 'reservasi Not Found',
                'data' => null
            ],404); 
        } //return message saat data reservasi tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_customer' => 'numeric',
            'id_meja' => 'numeric',
            'id_karyawan' => 'numeric',
            'tgl_reservasi' => 'date-format:Y-m-d|after:yesterday',
            'jam_reservasi' => 'in:lunch,dinner,on the spot',
            'status_reservasi' => 'in:selesai,belum selesai',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $reservasi->id_customer = $updateData['id_customer']; //edit id_cust
        $reservasi->id_meja = $updateData['id_meja']; //edit id_meja
        $reservasi->id_karyawan = $updateData['id_karyawan']; //edit id_meja
        $reservasi->tgl_reservasi = $updateData['tgl_reservasi']; //edit tgl reservasi
        $reservasi->jam_reservasi = $updateData['jam_reservasi']; //edit jam reservasi
        $reservasi->status_reservasi = $updateData['status_reservasi']; //edit jam reservasi
        
        
        if($reservasi->save()){
            return response([
                'message' => 'Update reservasi Success',
                'data' => $reservasi,
            ],200);
        } //return data reservasi yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update reservasi Failed',
            'data' => null,
        ],400); //return message saat reservasi gagal di edit
    } 
}