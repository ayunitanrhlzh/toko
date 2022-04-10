<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inputan['name'] = 'User Satu';
        $inputan['email'] = 'user@gmail.com';//ganti pake emailmu
        $inputan['password'] = Hash::make('123258');//passwordnya 123258
        $inputan['phone'] = '085852527575';
        $inputan['alamat'] = 'Bandung';
        $inputan['role'] = 'admin';//kita akan membuat akun atau users in dengan role admin
        User::create($inputan);

    }
}
