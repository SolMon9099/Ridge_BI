<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Authority;

class AuthorityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Authority::create([
            'id' => 1,
            'name' => '管理者',
        ]);
        Authority::create([
            'id' => 2,
            'name' => '現場責任者',
        ]);
        Authority::create([
            'id' => 3,
            'name' => '現場担当者',
        ]);
    }
}
