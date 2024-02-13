<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
    Route::post('user', 'Api\AuthController@index');
    Route::get('user/{id}', 'Api\AuthController@show');
    Route::put('user/{id}', 'Api\AuthController@update');
    Route::delete('user/{id}', 'Api\AuthController@destroy');
    Route::post('user/{id}', 'Api\AuthController@restore');

    Route::get('bahan', 'Api\BahanController@index'); //tampil semua
    Route::get('bahan/{id}', 'Api\BahanController@show'); //tampil 1
    Route::post('bahan', 'Api\BahanController@store'); //tambah
    Route::put('bahan/{id}', 'Api\BahanController@update'); //edit
    Route::delete('bahan/{id}', 'Api\BahanController@destroy'); //delete
    Route::get('bahanhabis', 'Api\BahanController@habis');

    Route::get('customer', 'Api\CustomerController@index'); //tampil semua
    Route::get('customer/{id_customer}', 'Api\CustomerController@show'); //tampil 1
    Route::post('customer', 'Api\CustomerController@store'); //tambah
    Route::put('customer/{id_customer}', 'Api\CustomerController@update'); //edit
    Route::delete('customer/{id_customer}', 'Api\CustomerController@destroy'); //delete

    Route::get('meja', 'Api\MejaController@index'); //tampil semua
    Route::get('meja/{id_meja}', 'Api\MejaController@show'); //tampil 1
    Route::post('meja', 'Api\MejaController@store'); //tambah
    Route::put('meja/{id}', 'Api\MejaController@update'); //edit
    Route::delete('meja/{id}', 'Api\MejaController@destroy'); //delete

    Route::get('menu', 'Api\MenuController@index'); //tampil semua
    Route::get('menu/{id}', 'Api\MenuController@show'); //tampil 1
    Route::post('menu', 'Api\MenuController@store'); //tambah
    Route::put('menu/{id}', 'Api\MenuController@update'); //edit
    Route::delete('menu/{id}', 'Api\MenuController@destroy'); //delete
    Route::get('tampilmenu', 'Api\MenuController@tampilMenu'); //tampil semua

    Route::get('reservasi', 'Api\ReservasiController@index'); //tampil semua
    Route::get('reservasi/{id}', 'Api\ReservasiController@show'); //tampil 1
    Route::post('reservasi', 'Api\ReservasiController@store'); //tambah
    Route::put('reservasi/{id}', 'Api\ReservasiController@update'); //edit
    Route::delete('reservasi/{id}', 'Api\ReservasiController@destroy'); //delete

    Route::get('belum', 'Api\PesananController@join'); //tampil belum disajikan
    Route::get('pesanan', 'Api\PesananController@index'); //tampil semua
    Route::get('pesanan/{id}', 'Api\PesananController@show'); //tampil 1
    Route::post('pesanan', 'Api\PesananController@store'); //tambah
    Route::put('pesanan/{id}', 'Api\PesananController@update'); //edit
    Route::delete('pesanan/{id}', 'Api\PesananController@destroy'); //delete

    Route::put('updateStatus/{id}', 'Api\PesananController@updateStatus');//edit status
    Route::get('pesananReservasi/{id}', 'Api\PesananController@showPesananReservasi');
    Route::get('pesananJumlah/{id}', 'Api\PesananController@showJumlah');
    Route::get('pesananCount/{id}', 'Api\PesananController@showCount');
    Route::get('pesananDisajikan', 'Api\PesananController@pesananSudahDisajikan');

    Route::get('makanantahunan/{tahun}', 'Api\PesananController@laporanPenjualanMakananTahunan');
    Route::get('sidedishtahunan/{tahun}', 'Api\PesananController@laporanPenjualanSideDishTahunan');
    Route::get('minumantahunan/{tahun}', 'Api\PesananController@laporanPenjualanMinumanTahunan');

    Route::get('makananbulanan/{tahun},{bulan}', 'Api\PesananController@laporanPenjualanMakananBulanan');
    Route::get('sidedishbulanan/{tahun},{bulan}', 'Api\PesananController@laporanPenjualanSideDishBulanan');
    Route::get('minumanbulanan/{tahun},{bulan}', 'Api\PesananController@laporanPenjualanMinumanBulanan');

    Route::get('pendapatanmakananbulanan/{tahun}', 'Api\PesananController@laporanPendapatanMakananBulanan');
    Route::get('pendapatanminumanbulanan/{tahun}', 'Api\PesananController@laporanPendapatanMinumanBulanan');
    Route::get('pendapatansidebulanan/{tahun}', 'Api\PesananController@laporanPendapatanSideBulanan');
    Route::get('pendapatanmakanantahunan/{tahun1},{tahun2}', 'Api\PesananController@laporanPendapatanMakananTahunan');
    Route::get('pendapatanminumantahunan/{tahun1},{tahun2}', 'Api\PesananController@laporanPendapatanMinumanTahunan');
    Route::get('pendapatansidetahunan/{tahun1},{tahun2}', 'Api\PesananController@laporanPendapatanSideTahunan');

    Route::get('stok', 'Api\StokController@index'); //tampil semua
    Route::get('stok/{id}', 'Api\StokController@show'); //tampil 1
    Route::post('stok', 'Api\StokController@store'); //tambah
    Route::put('stok/{id}', 'Api\StokController@update'); //edit
    Route::delete('stok/{id}', 'Api\StokController@destroy'); //delete

    Route::get('stokPerMenu/{menu},{tahun},{bulan}', 'Api\StokController@laporanStokPerMenu');
    Route::get('stokMakanan/{tanggal},{tanggal2}', 'Api\StokController@laporanStokMenuMakanan');
    Route::get('stokSideDish/{tanggal},{tanggal2}', 'Api\StokController@laporanStokMenuSideDish');
    Route::get('stokMinuman/{tanggal},{tanggal2}', 'Api\StokController@laporanStokMenuMinuman');

    Route::get('pengeluaranMakananBulanan/{tahun}', 'Api\StokController@laporanPengeluaranMakananBulanan');
    Route::get('pengeluaranMinumanBulanan/{tahun}', 'Api\StokController@laporanPengeluaranMinumanBulanan');
    Route::get('pengeluaranSideBulanan/{tahun}', 'Api\StokController@laporanPengeluaranSideDishBulanan');
    Route::get('pengeluaranMakananTahunan/{tahun},{tahun2}', 'Api\StokController@laporanPengeluaranMakananTahunan');
    Route::get('pengeluaranMinumanTahunan/{tahun},{tahun2}', 'Api\StokController@laporanPengeluaranMinumanTahunan');
    Route::get('pengeluaranSideTahunan/{tahun},{tahun2}', 'Api\StokController@laporanPengeluaranSideDishTahunan');

    Route::get('historystok', 'Api\HistoryStokController@index'); //tampil semua
    Route::get('historystok/{id}', 'Api\HistoryStokController@show'); //tampil 1
    Route::post('historystok', 'Api\HistoryStokController@store'); //tambah
    Route::put('historystok/{id}', 'Api\HistoryStokController@update'); //edit
    Route::delete('historystok/{id}', 'Api\HistoryStokController@destroy'); //delete
    Route::get('stokterbuang', 'Api\HistoryStokController@terbuang'); //tampil stok terbuang

    Route::get('stokterbuang', 'Api\StokTerbuangController@index'); //tampil semua
    Route::get('stokterbuang/{id}', 'Api\StokTerbuangController@show'); //tampil 1
    Route::post('stokterbuang', 'Api\StokTerbuangController@store'); //tambah
    Route::put('stokterbuang/{id}', 'Api\StokTerbuangController@update'); //edit
    Route::delete('stokterbuang/{id}', 'Api\StokTerbuangController@destroy'); //delete

    Route::get('detail', 'Api\DetailController@index'); //tampil semua
    Route::get('detail/{id}', 'Api\DetailController@show'); //tampil 1
    Route::post('detail', 'Api\DetailController@store'); //tambah
    Route::put('detail/{id}', 'Api\DetailController@update'); //edit
    Route::delete('detail/{id}', 'Api\DetailController@destroy'); //delete

    Route::get('transaksi', 'Api\TransaksiController@index'); //tampil semua
    Route::get('transaksi/{id}', 'Api\TransaksiController@show'); //tampil 1
    Route::post('transaksi', 'Api\TransaksiController@store'); //tambah
    Route::put('transaksi/{id}', 'Api\TransaksiController@update'); //edit
    Route::delete('transaksi/{id}', 'Api\TransaksiController@destroy'); //delete

Route::group(['middleware' => 'auth:api'], function(){
    

    Route::post('logout', 'Api\AuthController@logout');

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
