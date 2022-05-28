<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'id' => 1,
            'name' => 'BI管理者',
            'email' => 'admin@bi.com',
            'password' => Hash::make('12345678'),
            'department' => '',
            'is_enabled' => 1,
            'authority_id' => 1,
        ]);
    }
}
