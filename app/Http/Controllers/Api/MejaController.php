<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;  
use App\Meja; 
use App\Reservasi;

class MejaController extends Controller
{
    //read

    public function index(){

        $todayDate = Carbon::now('Asia/Jakarta');

        $meja1 = DB::table('mejas')
                ->join('reservasis', 'reservasis.id_meja', '=', 'mejas.id')
                ->where('reservasis.status_reservasi',"selesai")
                ->update(['mejas.status_meja' => 'tersedia']);

        $meja2 = DB::table('mejas')
                 ->join('reservasis', 'reservasis.id_meja', '=', 'mejas.id')
                 ->where('reservasis.tgl_reservasi', '=', $todayDate->toDateString())
                 ->where('reservasis.status_reservasi',"belum selesai")
                 ->update(['mejas.status_meja' => 'tidak tersedia']);

        $mejas = DB::table('mejas')
                ->select('mejas.*')
                ->get(); //mengambil semua data meja
        
        if(count($mejas) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $mejas
            ],200);
        } //return data semua meja dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data meja kosong
    } 

    //search

    public function show($id_meja){
        $meja = Meja::find($id_meja); //mencari data meja berdasarkan id

        if(!is_null($meja)){
            return response([
                'message' => 'Retrieve meja Success',
                'data' => $meja
            ],200);
        } //return data meja yang ditemukan dalam bentuk json

        return response([
            'message' => 'meja Not Found',
            'data' => null
        ],404); //return message saat data meja tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'no_meja' => 'required|numeric',
            'status_meja' => 'required',
            
        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $mejas = Meja::create($storeData); //menambah data meja baru
        return response([
            'message' => 'Add meja Success',
            'data' => $mejas,
        ],200); //return data meja baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $mejas = Meja::find($id); //mencari data meja berdasarkan id
        
        if(is_null($mejas)){
            return response([
                'message' => 'meja Not Found',
                'data' => null
            ],404);
        } //return message saat data meja tidak ditemukan

        if($mejas->delete()){
            return response([
                'message' => 'Delete meja Success',
                'data' => $mejas,
            ],200);
        } //return message saat berhasil menghapus data meja
        return response([
            'message' => 'Delete meja Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data meja
    }

    //(update)

    public function update(Request $request, $id){
        $mejas = Meja::find($id); //mencari data meja berdasarkan id
        if(is_null($mejas)){
            return response([
                'message' => 'meja Not Found',
                'data' => null
            ],404); 
        } //return message saat data meja tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'no_meja' => 'numeric',
            'status_meja' => 'required',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $mejas->no_meja = $updateData['no_meja']; //edit no_meja
        $mejas->status_meja = $updateData['status_meja']; //edit status_meja
        
        if($mejas->save()){
            return response([
                'message' => 'Update meja Success',
                'data' => $mejas,
            ],200);
        } //return data meja yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update meja Failed',
            'data' => null,
        ],400); //return message saat meja gagal di edit
    } 
}

