<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Rule;
use Validator;
use App\Customer;

class CustomerController extends Controller
{
    //method tampil data

    public function index(){
        $customers = Customer::all(); //mengambil semua data

        if(count($customers) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $customers
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    //method search

    public function show($id_customer){
        $customers = Customer::find($id_customer); //mencari data

        if(!is_null($customers)){
            return response([
                'message' => 'Retrieve customer Success',
                'data' => $customers
            ],200);
        }//return data customer yg ditemukan dlmbentuk json

        return response([
            'message'=>'Customer Not Found',
            'data' => null
        ],404);//return message customer tidak ditemukan
    }

    //create

    public function store(Request $request){
        $storeData = $request->all(); //mengambil input dr klien
        $validate = Validator::make($storeData, [
            'nama_cust' => 'required|max:60|',
            'email_cust' => 'nullable',
            'no_telp_cust' => 'nullable',
            
        ]);//membuat rule validasi
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        
            $customers = Customer::create($storeData);
        return response([
            'message'=>'Add customer success',
            'data' => $customers,
        ],200);//return message customer kosong
    }

    //hapus

    public function destroy($id_customer){
        $customers = Customer::find($id_customer);  

        if(is_null($customers)){
            return response([
                'message' => 'customer Not Found',
                'data' => null
            ],404);
        }

        if($customers->delete()){
            return response([
                'message' => 'Delete customer Success',
                'data' => $customers,
            ],200);
        }

        return response([
            'message'=>'Delete customer Failed',
            'data' => null,
        ],400);
    }

    
    //update

    public function update(Request $request, $id){
        $customers = Customer::find($id);//cari data
        if(is_null($customers)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all(); //mengambil input dr klien
        $validate = Validator::make($updateData, [
            'nama_cust' => 'max:60',
            'email_cust' => 'nullable',
            'no_telp_cust' => 'nullable'
        ]);//membuat rule validasi
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        
            $customers->nama_cust = $updateData['nama_cust'];
            $customers->email_cust = $updateData['email_cust'];
            $customers->no_telp_cust = $updateData['no_telp_cust'];
            
        
        if($customers->save()){
            return response([
                'message' => 'Update customer Success',
                'data' => $customers,
            ],200);
        }

        
        return response([
            'message' => 'Update customer failed',
            'data' => null,
        ],400);
    }


}
