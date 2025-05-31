<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'username' => 'superadmin',
            'password' => Hash::make('superadmin'),
            'nama_lengkap' => 'Super Administrator',
            'email' => 'superadmin@superadmin.com',
        ]);

    }
}
