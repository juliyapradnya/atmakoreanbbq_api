<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;  
use App\Transaksi; 
use App\Pesanan;

class TransaksiController extends Controller
{
    //read

    public function index(){
        $transaksi = DB::table('transaksis')
                    -> join('pesanans','pesanans.id','=','transaksis.id_pesanan')
                    -> join('reservasis','reservasis.id','=','pesanans.id_reservasi')
                    -> join('users','users.id','=','reservasis.id_karyawan')
                    -> join('customers','customers.id','=','reservasis.id_customer')
                    -> join('mejas','mejas.id','=','reservasis.id_meja')
                    -> select('transaksis.*', 'users.nama_karyawan', 'customers.nama_cust', 'mejas.no_meja', 'pesanans.id_reservasi')
                    -> get();

        if(count($transaksi) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ],200);
        } //return data semua  transaksi dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ],404); //return message data  transaksi kosong
    } 

    //search

    public function show($id){
        $transaksi = Transaksi::find($id); //mencari data  transaksi berdasarkan id

        if(!is_null($transaksi)){
            return response([
                'message' => 'Retrieve  transaksi Success',
                'data' => $transaksi
            ],200);
        } //return data  transaksi yang ditemukan dalam bentuk json

        return response([
            'message' => ' transaksi Not Found',
            'data' => null
        ],404); //return message saat data  transaksi tidak ditemukan
    }

    //(create)

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'id_pesanan' => 'numeric',
            'id_karyawan' => 'numeric',
            'kode_transaksi' => 'max:20',
            'jenis_pembayaran' => 'in:cash,kredit,debit',
            'no_kartu' => 'nullable',
            'nama_pemilik_kartu' => 'nullable',
            'kode_verifikasi' => 'nullable',
            'tgl_transaksi' => 'date-format:Y-m-d',
            'total_sub_total' => 'numeric',
            'service' => 'numeric',
            'tax' => 'numeric',
            'total_harga' => 'numeric',

        ]); //membuat rule validasi inpu

        //hitung untuk di struk
        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $transaksi = Transaksi::create($storeData); //menambah data transaksi baru
            $pesanan = Pesanan::find($transaksi->id_pesanan);

            $transaksi->total_sub_total = DB::table('pesanans')
                                          ->where('id_reservasi',$pesanan->id_reservasi)
                                          ->sum('harga');
            
            $transaksi->service = $transaksi->total_sub_total * (5/100);
            $transaksi->tax = $transaksi->total_sub_total * (10/100);
            $transaksi->total_harga = $transaksi->total_sub_total+$transaksi->service+$transaksi->tax;
            $transaksi->save();

            $todayDate = Carbon::now('Asia/Jakarta')->format('dmy');//untuk di no transaksi
            $hari_ini = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $temp = Transaksi::where('tgl_transaksi',$hari_ini)->get();

            $nomorTransaksi = $temp->count() + 1;

            $transaksi->tgl_transaksi = $hari_ini;
            $transaksi->kode_transaksi = "AKB-".$todayDate."-".$nomorTransaksi;
            $transaksi->save();


        return response([
            'message' => 'Add transaksi Success',
            'data' => $transaksi,
        ],200); //return data transaksi baru dalam bentuk json
    } 

    //(delete)

    public function destroy($id){
        $transaksi = Transaksi::find($id); //mencari data transaksi berdasarkan id
        
        if(is_null($transaksi)){
            return response([
                'message' => 'transaksi Not Found',
                'data' => null
            ],404);
        } //return message saat data transaksi tidak ditemukan

        if($transaksi->delete()){
            return response([
                'message' => 'Delete transaksi Success',
                'data' => $transaksi,
            ],200);
        } //return message saat berhasil menghapus data transaksi
        return response([
            'message' => 'Delete  transaksi Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data  transaksi
    }

    //(update)

    public function update(Request $request, $id){
        $transaksi = Transaksi::find($id); //mencari data  transaksi berdasarkan id
        if(is_null($transaksi)){
            return response([
                'message' => ' transaksi Not Found',
                'data' => null
            ],404); 
        } //return message saat data  transaksi tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_pesanan' => 'numeric',
            'id_karyawan' => 'numeric',
            'kode_transaksi' => 'max:20',
            'jenis_pembayaran' => 'in:cash,kredit,debit',
            'no_kartu' => 'nullable',
            'nama_pemilik_kartu' => 'nullable',
            'kode_verifikasi' => 'nullable',
            'tgl_transaksi' => 'date-format:Y-m-d',
            'total_sub_total' => 'numeric',
            'service' => 'numeric',
            'tax' => 'numeric',
            'total_harga' => 'numeric',

        ]); //membuat rule validasi input


        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
            $transaksi->id_pesanan = $updateData['id_pesanan']; //edit id transaksi
            $transaksi->id_karyawan = $updateData['id_karyawan']; //edit id menu
            $transaksi->kode_transaksi = $updateData['kode_transaksi'];
            $transaksi->jenis_pembayaran = $updateData['jenis_pembayaran']; //id pesanan
            $transaksi->no_kartu = $updateData['no_kartu']; //edit qty
            $transaksi->nama_pemilik_kartu = $updateData['nama_pemilik_kartu']; //edit jharga
            $transaksi->kode_verifikasi = $updateData['kode_verifikasi']; //edit status
            $transaksi->tgl_transaksi = $updateData['tgl_transaksi'];
            
            $transaksis = Transaksi::find($id);
            $pesanan = Pesanan::find($transaksi->id_pesanan);
            if($transaksi->id_pesanan != $transaksis->id_pesanan)
            {
                $transaksi->total = DB::table('pesanans')
                                        ->where('id_reservasi',$pesanan->id_reservasi)
                                        ->sum('harga');

                $transaksi->service = $transaksi->total_sub_total * (5/100);
                $transaksi->tax = $transaksi->total_sub_total * (10/100);
                $transaksi->total_harga = $transaksi->total_sub_total+$transaksi->service+$transaksi->tax;

                $transaksi->save();
            }


        if($transaksi->save()){
            return response([
                'message' => 'Update transaksi Success',
                'data' => $transaksi,
            ],200);
        } //return data transaksi yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update transaksi Failed',
            'data' => null,
        ],400); //return message saat  transaksi gagal di edit
    } 
}