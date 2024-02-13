<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\Bahan; 

class BahanController extends Controller
{
    //read jumlah bahan yang habis
    public function habis(){
        $bahan = DB::table('bahans') //mengambil semua data Bahan
                -> where('bahans.jumlah_bahan', '=', 0)
                -> get();
        
        if(count($bahan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahan
            ],200);
        } //return data semua Bahan dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data Bahan kosong
    } 

    public function index(){
        $bahan = Bahan::all(); //mengambil semua data Bahan
        
        if(count($bahan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahan
            ],200);
        } //return data semua Bahan dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data Bahan kosong
    } 

    //search

    public function show($id){
        $bahan = Bahan::find($id); //mencari data Bahan berdasarkan id

        if(!is_null($bahan)){
            return response([
                'message' => 'Retrieve Bahan Success',
                'data' => $bahan
            ],200);
        } //return data Bahan yang ditemukan dalam bentuk json

        return response([
            'message' => 'Bahan Not Found',
            'data' => null
        ],404); //return message saat data Bahan tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'nama_bahan' => 'required',
            'unit_bahan' => 'required',
            'jumlah_bahan' => 'required|numeric',
            'serving_size' => 'required|numeric',
            'satuan' => 'required|in:g,ml'
            
        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $bahan = Bahan::create($storeData); //menambah data Bahan baru
        return response([
            'message' => 'Add Bahan Success',
            'data' => $bahan,
        ],200); //return data Bahan baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $bahan = Bahan::find($id); //mencari data Bahan berdasarkan id
        
        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Not Found',
                'data' => null
            ],404);
        } //return message saat data Bahan tidak ditemukan

        if($bahan->delete()){
            return response([
                'message' => 'Delete Bahan Success',
                'data' => $bahan,
            ],200);
        } //return message saat berhasil menghapus data Bahan
        return response([
            'message' => 'Delete Bahan Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data Bahan
    }

    //(update)

    public function update(Request $request, $id){
        $bahan = Bahan::find($id); //mencari data Bahan berdasarkan id
        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Not Found',
                'data' => null
            ],404); 
        } //return message saat data Bahan tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'nama_bahan' => 'required',
            'unit_bahan' => 'required',
            'jumlah_bahan' => 'numeric',
            'serving_size' => 'numeric',
            'satuan' => 'in:g,ml',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $bahan->nama_bahan = $updateData['nama_bahan']; //edit nama_bahan
        $bahan->unit_bahan = $updateData['unit_bahan']; //edit unit_bahan
        $bahan->jumlah_bahan = $updateData['jumlah_bahan']; //edit jumlah_bahan
        $bahan->serving_size = $updateData['serving_size']; //edit serving_size
        $bahan->satuan = $updateData['satuan']; //edit satuan

        if($bahan->save()){
            return response([
                'message' => 'Update bahan Success',
                'data' => $bahan,
            ],200);
        } //return data Bahan yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update Bahan Failed',
            'data' => null,
        ],400); //return message saat Bahan gagal di edit
    } 
}

