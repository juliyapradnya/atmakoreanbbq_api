<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\Menu; 

class MenuController extends Controller
{
    //read

    public function index(){
        $menu = DB::table('menus')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> select('menus.*','bahans.nama_bahan')
                    -> get();
        
        if(count($menu) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $menu
            ],200);
        } //return data semua Menu dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data Menu kosong
    }
    
    public function tampilMenu(){
        $menu = DB::table('menus')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> where('bahans.jumlah_bahan', '>=',DB::raw('bahans.serving_size'))
                    -> select('menus.*','bahans.nama_bahan', 'bahans.jumlah_bahan')
                    -> get();
        
        if(count($menu) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $menu
            ],200);
        } //return data semua Menu dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data Menu kosong
    }

    //search

    public function show($id){
        $menu = Menu::find($id); //mencari data Menu berdasarkan id

        if(!is_null($menu)){
            return response([
                'message' => 'Retrieve Menu Success',
                'data' => $menu
            ],200);
        } //return data Menu yang ditemukan dalam bentuk json

        return response([
            'message' => 'Menu Not Found',
            'data' => null
        ],404); //return message saat data Menu tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_bahan' => 'required|numeric',
            'jenis_menu' => 'required',
            'nama_menu' => 'required',
            'deskripsi_menu' => 'required',
            'unit_menu' => 'required',
            'harga_menu' => 'required'
            
        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $menu = Menu::create($storeData); //menambah data Menu baru
        return response([
            'message' => 'Add Menu Success',
            'data' => $menu,
        ],200); //return data Menu baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $menu = Menu::find($id); //mencari data Menu berdasarkan id
        
        if(is_null($menu)){
            return response([
                'message' => 'Menu Not Found',
                'data' => null
            ],404);
        } //return message saat data Menu tidak ditemukan

        if($menu->delete()){
            return response([
                'message' => 'Delete Menu Success',
                'data' => $menu,
            ],200);
        } //return message saat berhasil menghapus data Menu
        return response([
            'message' => 'Delete Menu Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data Menu
    }

    //(update)

    public function update(Request $request, $id){
        $menu = Menu::find($id); //mencari data Menu berdasarkan id
        if(is_null($menu)){
            return response([
                'message' => 'Menu Not Found',
                'data' => null
            ],404); 
        } //return message saat data Menu tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_bahan' => 'required|numeric',
            'jenis_menu' => 'required',
            'nama_menu' => 'required',
            'deskripsi_menu' => 'required',
            'unit_menu' => 'required',
            'harga_menu' => 'required'
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $menu->id_bahan = $updateData['id_bahan']; //edit id_bahan
        $menu->jenis_menu = $updateData['jenis_menu']; //edit jenis_menu
        $menu->nama_menu = $updateData['nama_menu']; //edit nama_menu
        $menu->deskripsi_menu = $updateData['deskripsi_menu']; //edit deskripsi_menu
        $menu->unit_menu = $updateData['unit_menu']; //edit unit_menu
        $menu->harga_menu = $updateData['harga_menu']; //edit harga_menu

        if($menu->save()){
            return response([
                'message' => 'Update Menu Success',
                'data' => $menu,
            ],200);
        } //return data Menu yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update menu Failed',
            'data' => null,
        ],400); //return message saat Menu gagal di edit
    } 
}

