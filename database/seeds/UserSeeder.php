<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
            ->insert([
                'nama_karyawan' => 'Ni Putu Juliya Pradnyawati',
                'jenis_kelamin_karyawan' => 'female',
                'no_telp_karyawan' => '081234567890',
                'posisi_karyawan'=> 'owner',
                'tgl_gabung' => '2020-01-03',
                'email' => 'akbresto@admin.com',
                'password' => '$2b$10$4YNWezZ8M4Ob4cfXf/EK5OxVCBx2v1mmTwdrXl0YN6O2UBNFEDb/e',
                'created_at' => Carbon\Carbon::now(),
                'updated_at' => Carbon\Carbon::now()
            ]);
    }
}