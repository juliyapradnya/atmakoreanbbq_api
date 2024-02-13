<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;  
use App\Pesanan; 
use App\Bahan;
use App\HistoryStok;
use App\Menu;

class PesananController extends Controller
{
    //read

    //read pesanan belum disajikan
    public function join(){
        $pesanan = DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi') //di pesanan ada id reservasi
                    -> join('customers','customers.id','=','reservasis.id_customer') //mengambil id customer dari reservasi
                    -> join('mejas','mejas.id','=','reservasis.id_meja') //mengambil id meja dari reservasi
                    -> join('menus','menus.id','=','pesanans.id_menu') //di pesanan ada id menu
                    -> join('bahans','bahans.id','=','menus.id_bahan') //di pesanan ada id bahan
                    -> select('pesanans.*','customers.nama_cust', 'mejas.no_meja', 'menus.nama_menu', 'menus.harga_menu')
                    -> where('pesanans.status_pesanan', '=', 'belum disajikan')
                    -> get();
        
        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        } //return data semua pesanan dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data pesanan kosong
    } 

    public function index(){
        $pesanan = DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi') // di pesanan ada reservasi
                    -> join('customers','customers.id','=','reservasis.id_customer') //mengambil id cust dari reservasi
                    -> join('mejas','mejas.id','=','reservasis.id_meja') //mengambil id meja dari reservasi
                    -> join('menus','menus.id','=','pesanans.id_menu') //mengambil id menu dari pesanan
                    -> join('bahans','bahans.id','=','menus.id_bahan') //di menu ada id bahan
                    -> select('pesanans.*','customers.nama_cust', 'mejas.no_meja', 'menus.nama_menu', 'bahans.jumlah_bahan')
                    -> get();
        
        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        } //return data semua pesanan dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data pesanan kosong
    } 

    //search

    public function show($id){
        $pesanan = Pesanan::find($id); //mencari data pesanan berdasarkan id

        if(!is_null($pesanan)){
            return response([
                'message' => 'Retrieve pesanan Success',
                'data' => $pesanan
            ],200);
        } //return data pesanan yang ditemukan dalam bentuk json

        return response([
            'message' => 'pesanan Not Found',
            'data' => null
        ],404); //return message saat data pesanan tidak ditemukan
    }

    //menampilkan di struk
    public function showPesananReservasi($id){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('users','users.id','=','reservasis.id_karyawan')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> select('pesanans.*', 'users.nama_karyawan', 'mejas.no_meja','customers.nama_cust','menus.nama_menu','menus.harga_menu','bahans.nama_bahan')
                    -> where('pesanans.id_reservasi','=',$id)
                    -> where('pesanans.status_pesanan','=','selesai')
                    -> get($id); 

        if(!is_null($pesanan)){
            return response([
                'message' => 'Retrieve pesanan Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Pesanan Not Found',
            'data' => null
        ],404);
    }


    //
    public function pesananSudahDisajikan(){
        $todayDate = Carbon::now('Asia/Jakarta');

        $pesanan=DB::table('pesanans')
                    -> leftjoin('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> where('pesanans.status_pesanan','=','selesai')
                    -> whereDate('pesanans.created_at','=',$todayDate->toDateString())
                    -> select('pesanans.id', 'mejas.no_meja')
                    -> groupBy('mejas.no_meja')
                    -> get();  


        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan,
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }


    //jumlah qty
    public function showJumlah($id){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> where('pesanans.id_reservasi','=',$id)
                    -> where('pesanans.status_pesanan','=','selesai')
                    -> sum('pesanans.qty');
                    

        if(!is_null($pesanan)){
            return response([
                'message' => 'Retrieve pesanan Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Pesanan Not Found',
            'data' => null
        ],404);
    }

    //jumlah menu yang dipesan
    public function showCount($id){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> where('pesanans.id_reservasi','=',$id)
                    -> where('pesanans.status_pesanan','=','selesai')
                    -> count();

        if(!is_null($pesanan)){
            return response([
                'message' => 'Retrieve pesanan Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Pesanan Not Found',
            'data' => null
        ],404);
    }

    public function laporanPenjualanMakananTahunan($tahun){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> where('menus.jenis_menu','makanan utama')
                    -> select('menus.nama_menu', 'menus.unit_menu', DB::raw('max(pesanans.qty) as qty'),DB::raw('sum(pesanans.qty) as Total'))
                    -> groupBy('menus.nama_menu', 'menus.unit_menu')
                    -> get();  

        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPenjualanSideDishTahunan($tahun){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> where('menus.jenis_menu','side dish')
                    -> select('menus.nama_menu', 'menus.unit_menu', DB::raw('max(pesanans.qty) as qty'),DB::raw('sum(pesanans.qty) as Total'))
                    -> groupBy('menus.nama_menu', 'menus.unit_menu')
                    -> get();  

        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPenjualanMinumanTahunan($tahun){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> where('menus.jenis_menu','minuman')
                    -> select('menus.nama_menu', 'menus.unit_menu', DB::raw('max(pesanans.qty) as qty'),DB::raw('sum(pesanans.qty) as Total'))
                    -> groupBy('menus.nama_menu', 'menus.unit_menu')
                    -> get();  

        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPenjualanMakananBulanan($tahun,$bulan){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> whereMonth('pesanans.created_at','=',$bulan)
                    -> where('menus.jenis_menu','makanan utama')
                    -> select('menus.nama_menu', 'menus.unit_menu', DB::raw('max(pesanans.qty) as qty'),DB::raw('sum(pesanans.qty) as Total'))
                    -> groupBy('menus.nama_menu', 'menus.unit_menu')
                    -> get();  

        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPenjualanSideDishBulanan($tahun,$bulan){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)//ngambil tahun
                    -> whereMonth('pesanans.created_at','=',$bulan)//ngambil bulan
                    -> where('menus.jenis_menu','side dish')
                    -> select('menus.nama_menu', 'menus.unit_menu', DB::raw('max(pesanans.qty) as qty'),DB::raw('sum(pesanans.qty) as Total'))
                    -> groupBy('menus.nama_menu', 'menus.unit_menu')
                    -> get();  

        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPenjualanMinumanBulanan($tahun,$bulan){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> whereMonth('pesanans.created_at','=',$bulan)
                    -> where('menus.jenis_menu','minuman')
                    -> select('menus.nama_menu', 'menus.unit_menu', DB::raw('max(pesanans.qty) as qty'),DB::raw('sum(pesanans.qty) as Total'))
                    -> groupBy('menus.nama_menu', 'menus.unit_menu')
                    -> get();  

        if(count($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPendapatanMakananBulanan($tahun){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> where('menus.jenis_menu','makanan utama')
                    -> select(DB::raw("MONTHNAME(pesanans.created_at) month"),DB::raw('sum(pesanans.qty * menus.harga_menu) as sub_total'))
                    -> groupBy('month')
                    -> get();  

        if(!is_null($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPendapatanMinumanBulanan($tahun){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> where('menus.jenis_menu','minuman')
                    -> select(DB::raw("MONTHNAME(pesanans.created_at) month"),DB::raw('sum(pesanans.qty * menus.harga_menu) as sub_total'))
                    -> groupBy('month')
                    -> get();  

        if(!is_null($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPendapatanSideBulanan($tahun){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereYear('pesanans.created_at','=',$tahun)
                    -> where('menus.jenis_menu','side dish')
                    -> select(DB::raw("MONTHNAME(pesanans.created_at) month"),DB::raw('sum(pesanans.qty * menus.harga_menu) as sub_total'))
                    -> groupBy('month')
                    -> get();  

        if(!is_null($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }


    public function laporanPendapatanMakananTahunan($tahun1,$tahun2){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereBetween(DB::raw('YEAR(pesanans.created_at)'), array($tahun1, $tahun2))
                    -> where('menus.jenis_menu','makanan utama')
                    -> select(DB::raw("YEAR(pesanans.created_at) year"),DB::raw('sum(pesanans.qty * menus.harga_menu) as sub_total'))
                    -> groupBy('year')
                    -> get();  
        

        if(!is_null($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    public function laporanPendapatanMinumanTahunan($tahun1,$tahun2){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereBetween(DB::raw('YEAR(pesanans.created_at)'), array($tahun1, $tahun2))
                    -> where('menus.jenis_menu','minuman')
                    -> select(DB::raw("YEAR(pesanans.created_at) year"),DB::raw('sum(pesanans.qty * menus.harga_menu) as sub_total'))
                    -> groupBy('year')
                    -> get();  
        

        if(!is_null($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }



    public function laporanPendapatanSideTahunan($tahun1,$tahun2){
        $pesanan=DB::table('pesanans')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('menus','menus.id','=','pesanans.id_menu')
                    -> join('bahans','bahans.id','=','menus.id_bahan')
                    -> whereBetween(DB::raw('YEAR(pesanans.created_at)'), array($tahun1, $tahun2))
                    -> where('menus.jenis_menu','side dish')
                    -> select(DB::raw("YEAR(pesanans.created_at) year"),DB::raw('sum(pesanans.qty * menus.harga_menu) as sub_total'))
                    -> groupBy('year')
                    -> get();  
        

        if(!is_null($pesanan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pesanan
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
            'id_reservasi' => 'required|numeric',
            'id_menu' => 'required|numeric',
            'qty' => 'required|numeric',
            'harga' => 'nullable',
            'status_pesanan' => 'required|in:selesai,belum disajikan',
            
        ]); //membuat rule validasi input
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $pesanan = Pesanan::create($storeData); //menambah data pesanan baru
        
        $menu = Menu::find($pesanan->id_menu);
        $bahan = Bahan::find($menu->id_bahan);

        //jumlah stok jika kurang dari jumlah bahan, jumlah bahan tidak cukup
        //karena ada pesanan, jadi merubah stok bahan
        //jika memesan jumlah bahan tidak cukup
        if($pesanan->qty*$bahan->serving_size > $bahan->jumlah_bahan) {
            DB::table('pesanans')
            ->where('pesanans.id', $pesanan->id)
            ->delete();
            return response (['message' => 'jumlah bahan tidak mencukupi']);
        }
        else {

            $todayDate = Carbon::now('Asia/Jakarta');

            $historystok = HistoryStok::create(['id_bahan' => $menu->id_bahan,
                                                'sisa_stok' => $bahan->jumlah_bahan,
                                                'jumlah_stok_keluar' => $pesanan->qty*$bahan->serving_size, //qty kali serving size
                                                'tanggal_history' => $todayDate->toDateString(),]); 

            $bahan->jumlah_bahan = $bahan->jumlah_bahan - ($pesanan->qty*$bahan->serving_size); //jumlah bahan dikurang
            $bahan->save();

            return response([
                $pesanan->harga = $pesanan->qty*$menu->harga_menu, //hitung harga
                $pesanan->save(),
                'message' => 'Add pesanan Success',
                'data' => $pesanan,
            ],200); //return data pesanan baru dalam bentuk json
        }
    } 

    //(delete)

    public function destroy($id){
        $pesanan = Pesanan::find($id); //mencari data pesanan berdasarkan id
        
        if(is_null($pesanan)){
            return response([
                'message' => 'pesanan Not Found',
                'data' => null
            ],404);
        } //return message saat data pesanan tidak ditemukan

        if($pesanan->delete()){
            return response([
                'message' => 'Delete pesanan Success',
                'data' => $pesanan,
            ],200);
        } //return message saat berhasil menghapus data pesanan
        return response([
            'message' => 'Delete pesanan Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data pesanan
    }

    //(update)

    public function update(Request $request, $id){
        $pesanan = Pesanan::find($id); //mencari data pesanan berdasarkan id
        if(is_null($pesanan)){
            return response([
                'message' => 'pesanan Not Found',
                'data' => null
            ],404); 
        } //return message saat data pesanan tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_reservasi' => 'numeric',
            'id_menu' => 'numeric',
            'qty' => 'numeric',
            'harga' => 'nullable',
            'status_pesanan' => 'in:selesai,belum disajikan',
        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $pesanan->id_reservasi = $updateData['id_reservasi']; //edit id_reservasi
        $pesanan->id_menu = $updateData['id_menu'];
        $pesanan->qty = $updateData['qty'];
        $pesanan->harga = $updateData['harga'];  //edit id_menu
        $pesanan->status_pesanan = $updateData['status_pesanan']; //edit status_pesanan


        if($pesanan->save()){
            return response([
                'message' => 'Update pesanan Success',
                'data' => $pesanan,
            ],200);
        } //return data pesanan yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update pesanan Failed',
            'data' => null,
        ],400); //return message saat pesanan gagal di edit
    }
    
    // update status pesanan

    public function updateStatus(Request $request, $id){
        $pesanan = Pesanan::find($id); //mencari data pesanan berdasarkan id
        if(is_null($pesanan)){
            return response([
                'message' => 'pesanan Not Found',
                'data' => null
            ],404); 
        } //return message saat data pesanan tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            
            'status_pesanan' => 'in:selesai,belum disajikan',

        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $pesanan->status_pesanan = $updateData['status_pesanan']; //edit status_pesanan


        if($pesanan->save()){
            return response([
                'message' => 'Update Status Success',
                'data' => $pesanan,
            ],200);
        } //return data pesanan yang telah di edit dalam bentuk json

        return response([
            'message' => 'Update Status Failed',
            'data' => null,
        ],400); //return message saat pesanan gagal di edit
    } 
}

