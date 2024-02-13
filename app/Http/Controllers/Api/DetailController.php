<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\Detail; 

class DetailController extends Controller
{
    //read

    public function index(){
        $detail = DB::table('details')
                    -> join('menus','menus.id','=','details.id_menu')
                    -> join('pesanans','pesanans.id','=','details.id_pesanan')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> select('details.*', 'pesanans.qty', 'menus.nama_menu', 'mejas.no_meja')
                    -> get();
        
        if(count($detail) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $detail
            ],200);
        } //return data semua detail transaksi dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data detail transaksi kosong
    } 

    //search

    public function show($id){
        $detail = Detail::find($id); //mencari data detail transaksi berdasarkan id

        if(!is_null($detail)){
            return response([
                'message' => 'Retrieve detail transaksi Success',
                'data' => $detail
            ],200);
        } //return data detail transaksi yang ditemukan dalam bentuk json

        return response([
            'message' => 'detail transaksi Not Found',
            'data' => null
        ],404); //return message saat data detail transaksi tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_menu' => 'required|numeric',
            'id_pesanan' => 'required|numeric',
            'qty' => 'required|numeric',
            'harga' => 'required|numeric',
            'sub_total' => 'required|numeric',
            'status_pembayaran' => 'required|in:sudah bayar,belum bayar',

        ]); //membuat rule validasi input

        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $detail = Detail::create($storeData); //menambah data detail transaksi baru
        return response([
            'message' => 'Add detail transaksi Success',
            'data' => $detail,
        ],200); //return data detail transaksi baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $detail = Detail::find($id); //mencari data detail transaksi berdasarkan id
        
        if(is_null($detail)){
            return response([
                'message' => 'detail transaksi Not Found',
                'data' => null
            ],404);
        } //return message saat data detail transaksi tidak ditemukan

        if($detail->delete()){
            return response([
                'message' => 'Delete detail transaksi Success',
                'data' => $detail,
            ],200);
        } //return message saat berhasil menghapus data detail transaksi
        return response([
            'message' => 'Delete detail transaksi Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data detail transaksi
    }

    //(update)

    public function update(Request $request, $id){
        $detail = Detail::find($id); //mencari data detail transaksi berdasarkan id
        if(is_null($detail)){
            return response([
                'message' => 'detail transaksi Not Found',
                'data' => null
            ],404); 
        } //return message saat data detail transaksi tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_menu' => 'numeric',
            'id_pesanan' => 'numeric',
            'qty' => 'numeric',
            'harga' => 'numeric',
            'sub_total' => 'numeric',
            'status_pembayaran' => 'in:sudah bayar,belum bayar',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        
        $detail->id_menu = $updateData['id_menu']; //edit id menu
        $detail->id_pesanan = $updateData['id_pesanan']; //id pesanan
        $detail->qty = $updateData['qty']; //edit qty
        $detail->harga = $updateData['harga']; //edit jharga
        $detail->status_pembayaran = $updateData['status_pembayaran']; //edit status


        if($detail->save()){
            return response([
                'message' => 'Update detail transaksi Success',
                'data' => $detail,
            ],200);
        } //return data detail transaksi yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update detail transaksi Failed',
            'data' => null,
        ],400); //return message saat detail transaksi gagal di edit
    } 
}