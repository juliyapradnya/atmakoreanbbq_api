<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;  
use App\Stok; 
use App\Bahan;
use App\Menu;

class StokController extends Controller
{
    //read

    public function index(){
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> select('stoks.*','bahans.nama_bahan', 'users.nama_karyawan','menus.nama_menu')
                    -> get();

        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        } //return data semua  stok dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data stok kosong
    } 

    //search

    public function show($id){
        $stok = Stok::find($id); //mencari data stok berdasarkan id

        if(!is_null($stok)){
            return response([
                'message' => 'Retrieve stok Success',
                'data' => $stok
            ],200);
        } //return data stok yang ditemukan dalam bentuk json

        return response([
            'message' => 'Stok Not Found',
            'data' => null
        ],404); //return message saat data stok tidak ditemukan
    }

    public function laporanStokPerMenu($menu,$tahun,$bulan){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> where('menus.nama_menu',$menu)
                    -> whereYear('stoks.tanggal_stok_masuk',$tahun)
                    -> whereMonth('stoks.tanggal_stok_masuk',$bulan)
                    -> select(DB::raw('DATE_FORMAT(stoks.tanggal_stok_masuk, "%d %M %Y") as tanggal'),'stoks.unit_stok',DB::raw('sum(stoks.jumlah_stok_masuk) as stok_masuk'),'stoks.sisa_stok','menus.nama_menu')
                    -> groupBy('tanggal')
                    -> get();

        if(!is_null($stok)){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);

    }

    //laporan stok periode custom makanan
    public function laporanStokMenuMakanan($tanggal,$tanggal2){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> where('menus.jenis_menu','makanan utama')
                    -> whereBetween('stoks.tanggal_stok_masuk',[$tanggal,$tanggal2]) //tahun sekian sampai tahun sekian
                    -> select('menus.nama_menu','stoks.unit_stok', DB::raw('sum(stoks.jumlah_stok_masuk) as jumlah_stok_masuk'),'stoks.sisa_stok', 'menus.nama_menu')
                    -> groupBy('menus.nama_menu')
                    -> get();

        if(!is_null($stok)){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);

    }

    //laporan stok periode custom sidedish

    public function laporanStokMenuSideDish($tanggal,$tanggal2){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> where('menus.jenis_menu','side dish')
                    -> whereBetween('stoks.tanggal_stok_masuk',[$tanggal,$tanggal2]) //tahun sekian sampai tahun sekian
                    -> select('menus.nama_menu','stoks.unit_stok', DB::raw('sum(stoks.jumlah_stok_masuk) as jumlah_stok_masuk'),'stoks.sisa_stok', 'menus.nama_menu')
                    -> groupBy('menus.nama_menu')
                    -> get();

        if(!is_null($stok)){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);

    }

    //laporan stok periode custom minuman
    public function laporanStokMenuMinuman($tanggal,$tanggal2){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu') 
                    -> join('bahans','bahans.id','=','menus.id_bahan') 
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> where('menus.jenis_menu','minuman')
                    -> whereBetween('stoks.tanggal_stok_masuk',[$tanggal,$tanggal2])
                    -> select('menus.nama_menu','stoks.unit_stok', DB::raw('sum(stoks.jumlah_stok_masuk) as jumlah_stok_masuk'),'stoks.sisa_stok', 'menus.nama_menu')
                    -> groupBy('menus.nama_menu')
                    -> get();

        if(!is_null($stok)){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);

    }

    // laporan pengeluaran
    public function laporanPengeluaranMakananBulanan($tahun){        

        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> whereYear('stoks.tanggal_stok_masuk','=',$tahun)
                    -> where('menus.jenis_menu','makanan utama')
                    -> select(DB::raw("MONTHNAME(stoks.tanggal_stok_masuk) month"),DB::raw('sum(stoks.harga_stok) as sub_total'))
                    -> groupBy('month')
                    -> get();

        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    public function laporanPengeluaranMinumanBulanan($tahun){        

        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> whereYear('stoks.tanggal_stok_masuk','=',$tahun)
                    -> where('menus.jenis_menu','minuman')
                    -> select(DB::raw("MONTHNAME(stoks.tanggal_stok_masuk) month"),DB::raw('sum(stoks.harga_stok) as sub_total'))
                    -> groupBy('month')
                    -> get();
                    

        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    public function laporanPengeluaranSideDishBulanan($tahun){        

        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> whereYear('stoks.tanggal_stok_masuk','=',$tahun)
                    -> where('menus.jenis_menu','side dish')
                    -> select(DB::raw("MONTHNAME(stoks.tanggal_stok_masuk) month"),DB::raw('sum(stoks.harga_stok) as sub_total'))
                    -> groupBy('month')
                    -> get();
                    


        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPengeluaranMakananTahunan($tahun1, $tahun2){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> whereBetween(DB::raw('YEAR(stoks.tanggal_stok_masuk)'), array($tahun1, $tahun2))
                    -> where('menus.jenis_menu','makanan utama')
                    -> select(DB::raw("YEAR(stoks.tanggal_stok_masuk) year"),DB::raw('sum(stoks.harga_stok) as sub_total'))
                    -> groupBy('year')
                    -> get();
                    


        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    public function laporanPengeluaranMinumanTahunan($tahun1, $tahun2){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> whereBetween(DB::raw('YEAR(stoks.tanggal_stok_masuk)'), array($tahun1, $tahun2))
                    -> where('menus.jenis_menu','minuman')
                    -> select(DB::raw("YEAR(stoks.tanggal_stok_masuk) year"),DB::raw('sum(stoks.harga_stok) as sub_total'))
                    -> groupBy('year')
                    -> get();


        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    public function laporanPengeluaranSideDishTahunan($tahun1, $tahun2){        
        $stok = DB::table('stoks')
                    -> join('menus','menus.id','=','stoks.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> join('users','users.id','=','stoks.id_karyawan')
                    -> whereBetween(DB::raw('YEAR(stoks.tanggal_stok_masuk)'), array($tahun1, $tahun2))
                    -> where('menus.jenis_menu','side dish')
                    -> select(DB::raw("YEAR(stoks.tanggal_stok_masuk) year"),DB::raw('sum(stoks.harga_stok) as sub_total'))
                    -> groupBy('year')
                    -> get();
                   
        if(count($stok) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $stok
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    
    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_menu' => 'required|numeric',
            'id_karyawan' => 'required|numeric',
            'jumlah_stok_masuk' => 'required|numeric',
            'harga_stok' => 'required|numeric',
            'unit_stok' => 'required',
            'tanggal_stok_masuk' => 'required|date-format:Y-m-d',
            'sisa_stok' => 'numeric',

        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $stok = Stok::create($storeData); //menambah data Stok baru
            $menu = Menu::find($stok->id_menu);
            $bahan = Bahan::find($menu->id_bahan);

        return response([
            $bahan->jumlah_bahan = $bahan->jumlah_bahan+$stok->jumlah_stok_masuk, //menjumlahkan stok masuk dengan jumlah stok masuk
            $stok->sisa_stok=$bahan->jumlah_bahan,
            $stok->save(),
            $bahan->save(),
            'message' => 'Add Stok Success',
            'data' => $stok,
        ],200); //return data Stok baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $stok = Stok::find($id); //mencari data Stok berdasarkan id
        $menu = Menu::find($stok->id_menu);
        $bahan = Bahan::find($menu->id_bahan);
        if($bahan->jumlah_bahan <0)
        {
            $bahan->jumlah_bahan=0;
        }
        $bahan->save();
        if(is_null($stok)){
            return response([
                'message' => 'Stok Not Found',
                'data' => null
            ],404);
        } //return message saat data Stok tidak ditemukan

        if($stok->delete()){
            return response([
                'message' => 'Delete Stok Success',
                'data' => $stok,
            ],200);
        } //return message saat berhasil menghapus data Stok
        return response([
            'message' => 'Delete Stok Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data Stok
    }

    //(update)

    public function update(Request $request, $id){
        $stok = Stok::find($id); //mencari data Stok berdasarkan id
        if(is_null($stok)){
            return response([
                'message' => 'Stok Not Found',
                'data' => null
            ],404); 
        } //return message saat data Stok tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_menu' => 'numeric',
            'id_karyawan' => 'numeric',
            'jumlah_stok_masuk' => 'numeric',
            'harga_stok' => 'numeric',
            'unit_stok' => 'required',
            'tanggal_stok_masuk' => 'date-format:Y-m-d',
            'sisa_stok' => 'numeric',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $stok->id_menu = $updateData['id_menu']; //edit id_bahan
            $stok->id_karyawan = $updateData['id_karyawan']; //edit id_karyawan
            $stok->jumlah_stok_masuk = $updateData['jumlah_stok_masuk']; //edit jumlah stok
            $stok->harga_stok = $updateData['harga_stok']; //edit harga
            $stok->unit_stok = $updateData['unit_stok']; //edit unit
            $stok->tanggal_stok_masuk = $updateData['tanggal_stok_masuk']; //edit tgl

            $stoks = Stok::find($id);
            $menu = Menu::find($stok->id_menu);
            $bahan = Bahan::find($menu->id_bahan);
            $bahan->jumlah_bahan = $bahan->jumlah_bahan - $stoks->jumlah_stok_masuk; //update jumlah
            $bahan->save();

        if($stok->save()){
            return response([
                $menu = Menu::find($stok->id_menu),
                $bahan = Bahan::find($menu->id_bahan),
                
                $bahan->jumlah_bahan = $bahan->jumlah_bahan + ($stok->jumlah_stok_masuk),
                $stok->sisa_stok=$bahan->jumlah_bahan,
                $stok->save(),
                $bahan->save(),
                'message' => 'Update stok Success',
                'data' => $stok,
            ],200);
        } //return data stok yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update stok Failed',
            'data' => null,
        ],400); //return message saat stok gagal di edit
    } 
}