<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\User;       //import model User
use Validator;      //import library untuk validasi

class AuthController extends Controller
{
    //method search

    public function show($id){
        $user = User::find($id); //mencari data

        if(!is_null($user)){
            return response([
                'message' => 'Retrieve user Success',
                'data' => $user
            ],200);
        }//return data customer yg ditemukan dlmbentuk json

        return response([
            'message'=>'User Not Found',
            'data' => null
        ],404);//return message customer tidak ditemukan
    }

    public function index(){
        $user = DB::table('users')
                ->get();

        if(count($user) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $user
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }

    

    public function destroy($id){
        $user = User::find($id);  

        if(is_null($user)){
            return response([
                'message' => 'Karyawan Not Found',
                'data' => null
            ],404);
        }

        if($user->delete()){
            return response([
                'message' => 'Nonaktif Karyawan Success',
                'data' => $user,
            ],200);
        }

        return response([
            'message'=>'Nonaktif Karyawan Failed',
            'data' => null,
        ],400);
    }

    //update

    public function update(Request $request, $id){
        $user = User::find($id);//cari data
        if(is_null($user)){
            return response([
                'message' => 'Karyawan Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all(); //mengambil input dr klien
        $validate = Validator::make($updateData, [
            'nama_karyawan' => 'required|max:60', 
            'jenis_kelamin_karyawan' => 'required|in:male,female', 
            'no_telp_karyawan' => 'required|regex:/(08)[0-9]{8}/|max:13', 
            'posisi_karyawan' => 'required|max:60', 
            'tgl_gabung' => 'required|date-format:Y-m-d',
            'email' => 'email:rfc,dns',
            

        ]);//membuat rule validasi
        
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        
            
            $user->nama_karyawan = $updateData['nama_karyawan'];
            $user->jenis_kelamin_karyawan = $updateData['jenis_kelamin_karyawan'];
            $user->no_telp_karyawan = $updateData['no_telp_karyawan'];
            $user->posisi_karyawan = $updateData['posisi_karyawan'];
            $user->tgl_gabung = $updateData['tgl_gabung'];
            $user->email = $updateData['email'];
            
        
        if($user->save()){
            return response([
                'message' => 'Update Karyawan Success',
                'data' => $user,
            ],200);
        }

        return response([
            'message' => 'Update Karyawan failed',
            'data' => null,
        ],400);
    }

    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [
            'nama_karyawan' => 'required|max:60', 
            'jenis_kelamin_karyawan' => 'required|in:male,female', 
            'no_telp_karyawan' => 'required|regex:/(08)[0-9]{8}/|max:13', 
            'posisi_karyawan' => 'required|max:60', 
            'tgl_gabung' => 'required|date-format:Y-m-d',
            'email' => 'required|email:rfc,dns', 
            'password' => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);

        $registrationData['password'] = bcrypt($request->password);
        $user = User::create($registrationData);
        return response([
            'message' => 'Add Karyawan Success',
            'user' => $user,
        ],200);
    }

    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);
        
        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'],401);
        
        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function logout(Request $request)
    { 
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
    
    public function restore($id)
    {

        /**
         * Find content only among those deleted.
         */

        $user = user::withTrashed()->find($id);

        $user->restore();

        return response([
            'message'=>'Karyawan Aktif Kembali',
            'data' => $user,
        ],200);

    }
}
