<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\StokTerbuang;
use App\HistoryStok;
use App\Bahan; 


class StokTerbuangController extends Controller
{
    //read

    public function index(){
        $stokterbuang = DB::table('stok_terbuangs')
                    -> join('bahans','bahans.id','=','stok_terbuangs.id_bahan')
                    -> join('users','users.id','=','stok_terbuangs.id_karyawan')
                    -> select('stok_terbuangs.*','bahans.nama_bahan', 'users.nama_karyawan')
                    -> get();

        if(count($stokterbuang) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stokterbuang
            ],200);
        } //return data semua  stok terbuang terbuang dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data stok terbuang kosong
    } 

    //search

    public function show($id){
        $stokterbuang = StokTerbuang::find($id); //mencari data stok berdasarkan id

        if(!is_null($stokterbuang)){
            return response([
                'message' => 'Retrieve stok terbuang Success',
                'data' => $stokterbuang
            ],200);
        } //return data stok terbuang yang ditemukan dalam bentuk json

        return response([
            'message' => 'Stok Terbuang Not Found',
            'data' => null
        ],404); //return message saat data stok terbuang tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_bahan' => 'required|numeric',
            'id_karyawan' => 'required|numeric',
            'jumlah_stok_terbuang' => 'nullable',
            'satuan' => 'required',
            'tgl_terbuang' => 'required|date-format:Y-m-d',

        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $stokterbuang = StokTerbuang::create($storeData); //menambah data Stok terbuang baru
        $bahan = Bahan::find($stokterbuang->id_bahan);
        if($stokterbuang->jumlah_stok_terbuang > $bahan->jumlah_bahan){ //jika stok terbuang lebih kecil
            DB::table('stok_terbuangs')
            ->where('stok_terbuangs.id', $stokterbuang->id)
            ->delete();
            return response(['message' => 'stok terbuang melebihi jumlah stok'],400);
        }
        else{
            return response([
                $bahan->jumlah_bahan = $bahan->jumlah_bahan-($stokterbuang->jumlah_stok_terbuang),
                $bahan->save(),
                'message' => 'Add Stok terbuang Success',
                'data' => $stokterbuang,
            ],200); //return data Stok terbuang baru dalam bentuk json
        }
        
    } 

    //(delete)

    public function destroy($id){
        $stokterbuang = StokTerbuang::find($id); //mencari data Stok terbuang berdasarkan id
        $bahan = Bahan::find($stokterbuang->id_bahan);
        $bahan->jumlah_bahan = $bahan->jumlah_bahan + ($stokterbuang->jumlah_stok_terbuang); //kalau jumlah stok terbuang dibuang, maka jumlah bahan akan update
        $bahan->save();

        if(is_null($stokterbuang)){
            return response([
                'message' => 'Stok Not Found',
                'data' => null
            ],404);
        } //return message saat data Stok terbuang tidak ditemukan

        if($stokterbuang->delete()){
            return response([
                'message' => 'Delete Stok terbuang Success',
                'data' => $stokterbuang,
            ],200);
        } //return message saat berhasil menghapus data Stok terbuang
        return response([
            'message' => 'Delete Stok terbuang Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data Stok terbuang
    }

    //(update)

    public function update(Request $request, $id){
        $stokterbuang = StokTerbuang::find($id); //mencari data Stok terbuang berdasarkan id
        if(is_null($stokterbuang)){
            return response([
                'message' => 'Stok Terbuang Not Found',
                'data' => null
            ],404); 
        } //return message saat data Stok terbuang tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_bahan' => 'numeric',
            'id_karyawan' => 'numeric',
            'jumlah_stok_terbuang' => 'nullable',
            'satuan' => 'required',
            'tgl_terbuang' => 'date-format:Y-m-d',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $stokterbuang->id_bahan = $updateData['id_bahan']; //edit id_bahan
            $stokterbuang->id_karyawan = $updateData['id_karyawan']; //edit id_karyawan
            $stokterbuang->jumlah_stok_terbuang = $updateData['jumlah_stok_terbuang']; //edit jumlah stok terbuang
            $stokterbuang->satuan = $updateData['satuan']; //edit harga
            $stokterbuang->tgl_terbuang = $updateData['tgl_terbuang']; //edit unit

            $stok = StokTerbuang::find($id);
            $bahan = Bahan::find($stokterbuang->id_bahan);
            if($stokterbuang->jumlah_stok_terbuang > $bahan->jumlah_bahan) {
                return response(['message' => 'Stok Terbuang Melebihi Jumlah Stok'],400);
            }
            
            else {
                $bahan->jumlah_bahan = $bahan->jumlah_bahan + ($stok->jumlah_stok_terbuang); 
                $bahan->save();
                if($stokterbuang->save()){
                    return response([
                        $bahan = Bahan::find($stokterbuang->id_bahan),
                        $bahan->jumlah_bahan = $bahan->jumlah_bahan - ($stokterbuang->jumlah_stok_terbuang), //jumlah bahan kurang jumlah stok terbuang
                        $bahan->save(),
                        'message' => 'Update stok terbuang Success',
                        'data' => $stokterbuang,
                    ],200);
                } //return data stok terbuang yang telah di edit dalam bentuk json
            }
        
        return response([
            'message' => 'Update stok terbuang Failed',
            'data' => null,
        ],400); //return message saat stok terbuang gagal di edit
    } 
}