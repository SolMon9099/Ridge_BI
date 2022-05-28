<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PageGroup;

class PageGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PageGroup::create([
            'id' => 1,
            'group_name' => '全般',
            'detail_name' => '権限グループ設定',
            'order_no' => 1
        ]);
        PageGroup::create([
            'id' => 2,
            'group_name' => '全般',
            'detail_name' => 'アカウント管理',
            'order_no' => 2
        ]);
        PageGroup::create([
            'id' => 3,
            'group_name' => '全般',
            'detail_name' => '通知設定',
            'order_no' => 3
        ]);
        PageGroup::create([
            'id' => 4,
            'group_name' => '現場設定',
            'detail_name' => '現場名一覧',
            'order_no' => 4
        ]);
        PageGroup::create([
            'id' => 5,
            'group_name' => 'カメラ設定',
            'detail_name' => 'カメラ一覧',
            'order_no' => 5
        ]);
        PageGroup::create([
            'id' => 6,
            'group_name' => '危険エリア侵入検知',
            'detail_name' => 'ルール一覧',
            'order_no' => 6
        ]);
        PageGroup::create([
            'id' => 7,
            'group_name' => '危険エリア侵入検知',
            'detail_name' => '検知リスト',
            'order_no' => 7
        ]);
        PageGroup::create([
            'id' => 8,
            'group_name' => '危険エリア侵入検知',
            'detail_name' => '詳細分析',
            'order_no' => 8
        ]);
        PageGroup::create([
            'id' => 9,
            'group_name' => '棚乱れ検知',
            'detail_name' => 'ルール一覧',
            'order_no' => 9
        ]);
        PageGroup::create([
            'id' => 10,
            'group_name' => '棚乱れ検知',
            'detail_name' => '検知リスト',
            'order_no' => 10
        ]);
        PageGroup::create([
            'id' => 11,
            'group_name' => '棚乱れ検知',
            'detail_name' => '詳細分析',
            'order_no' => 11
        ]);
        PageGroup::create([
            'id' => 12,
            'group_name' => '検針メーター検知',
            'detail_name' => 'ルール一覧',
            'order_no' => 12
        ]);
        PageGroup::create([
            'id' => 13,
            'group_name' => '検針メーター検知',
            'detail_name' => '検知リスト',
            'order_no' => 13
        ]);
        PageGroup::create([
            'id' => 14,
            'group_name' => '検針メーター検知',
            'detail_name' => '詳細分析',
            'order_no' => 14
        ]);
        PageGroup::create([
            'id' => 15,
            'group_name' => '過去分析',
            'detail_name' => '新規分析依頼',
            'order_no' => 15
        ]);
        PageGroup::create([
            'id' => 16,
            'group_name' => '過去分析',
            'detail_name' => '分析依頼中リスト',
            'order_no' => 16
        ]);
        PageGroup::create([
            'id' => 17,
            'group_name' => '過去分析',
            'detail_name' => '分析済みリスト',
            'order_no' => 17
        ]);
    }
}
