<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\HistoryStok; 

class HistoryStokController extends Controller
{
    //read
    public function index(){
        $historystok = DB::table('history_stoks')
                    -> join('bahans','bahans.id','=','history_stoks.id_bahan')
                    -> select('history_stoks.*', 'bahans.nama_bahan')
                    -> get();

        if(count($historystok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $historystok
            ],200);
        } //return data semua  History Stok dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data History Stok kosong
    } 

    //search

    public function show($id){
        $historystok = HistoryStok::find($id); //mencari data History Stok berdasarkan id

        if(!is_null($historystok)){
            return response([
                'message' => 'Retrieve History Stok Success',
                'data' => $historystok
            ],200);
        } //return data History Stok yang ditemukan dalam bentuk json

        return response([
            'message' => 'History Stok Not Found',
            'data' => null
        ],404); //return message saat data History Stok tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_bahan' => 'required|numeric',
            'sisa_stok' => 'required|numeric',
            'jumlah_stok_keluar' => 'required|numeric',
            'tanggal_history' => 'required|date-format:Y-m-d',

        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $historystok = HistoryStok::create($storeData); //menambah data History Stok baru
        return response([
            'message' => 'Add History Stok Success',
            'data' => $historystok,
        ],200); //return data History Stok baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $historystok = HistoryStok::find($id); //mencari data History Stok berdasarkan id
        
        if(is_null($historystok)){
            return response([
                'message' => 'History Stok Not Found',
                'data' => null
            ],404);
        } //return message saat data History Stok tidak ditemukan

        if($historystok->delete()){
            return response([
                'message' => 'Delete History Stok Success',
                'data' => $historystok,
            ],200);
        } //return message saat berhasil menghapus data History Stok
        return response([
            'message' => 'Delete History Stok Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data History Stok
    }

    //(update)

    public function update(Request $request, $id){
        $historystok = HistoryStok::find($id); //mencari data History Stok berdasarkan id
        if(is_null($historystok)){
            return response([
                'message' => 'History Stok Not Found',
                'data' => null
            ],404); 
        } //return message saat data History Stok tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_bahan' => 'numeric',
            'sisa_stok' => 'numeric',
            'jumlah_stok_keluar' => 'numeric',
            'tanggal_history' => 'date-format:Y-m-d',

        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $historystok->id_bahan = $updateData['id_bahan']; //edit id_bahan
            $historystok->sisa_stok = $updateData['sisa_stok']; //edit jumlah stok
            $historystok->jumlah_stok_keluar = $updateData['jumlah_stok_keluar']; //edit harga
            $historystok->tanggal_history = $updateData['tanggal_history']; //edit tgl

        if($historystok->save()){
            return response([
                'message' => 'Update History Stok Success',
                'data' => $historystok,
            ],200);
        } //return data History Stok yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update History Stok Failed',
            'data' => null,
        ],400); //return message saat History Stok gagal di edit
    } 
}