<?php

return [
    'enable_status' => [
        1 => '有効',
        0 => '無効',
    ],

    'enable_status_code' => [
        'enable' => 1,
        'disable' => 0,
    ],

    'camera_status' => [
        1 => '稼働中',
        0 => '停止中',
    ],

    //カメラ表示マークのサイズ
    'camera_mark_radius' => 5,

    //アクション
    'action' => [
        1 => '横たわる',
        2 => '屈む',
        3 => '寄りかかる',
        4 => '侵入する',
    ],

    'action_code' => [
        'lie' => 1,
        'bend' => 2,
        'lean' => 3,
        'invade' => 4,
    ],

    //メーター種別
    'meter_type' => [
        1 => '数値タイプ',
        2 => '指針タイプ',
    ],
    'meter_type_code' => [
        'number' => 1,
        'pointer' => 2,
    ],

    //メーター判定範囲
    'number_type_range' => [
        1 => '次の値の間',
        2 => '次の値の間以外',
        3 => '次の値より大きい',
        4 => '次の値より小さい',
    ],
    'number_type_range_code' => [
        'inside' => 1,
        'outside' => 2,
        'large' => 3,
        'small' => 4,
    ],
    'pointer_type_range' => [
        1 => '範囲内',
        2 => '範囲外',
    ],
    'pointer_type_range_code' => [
        'inside' => 1,
        'outside' => 2,
    ],

    'authorities' => [
        1 => '管理者',
        2 => '現場責任者',
        3 => '現場担当者',
    ],
    'authorities_codes' => [
        'super_admin' => 1,
        'owner' => 2,
        'manager' => 3,
    ],

    'pages' => [
        '全般' => [
            ['id' => 1, 'name' => '権限グループ設定'],
            ['id' => 2, 'name' => 'アカウント管理'],
            ['id' => 3, 'name' => '通知設定'],
        ],
        '現場設定' => [
            ['id' => 4, 'name' => '現場名一覧'],
        ],
        'カメラ設定' => [
            ['id' => 5, 'name' => 'カメラ一覧'],
            ['id' => 6, 'name' => 'カメラマッピング一覧'],
        ],
        'ピット入退場検知' => [
            ['id' => 7, 'name' => 'ルール一覧'],
            ['id' => 8, 'name' => '検知リスト'],
            ['id' => 9, 'name' => '詳細分析'],
        ],
        '危険エリア侵入検知' => [
            ['id' => 10, 'name' => 'ルール一覧'],
            ['id' => 11, 'name' => '検知リスト'],
            ['id' => 12, 'name' => '詳細分析'],
        ],
        '棚乱れ検知' => [
            ['id' => 13, 'name' => 'ルール一覧'],
            ['id' => 14, 'name' => '検知リスト'],
            ['id' => 15, 'name' => '詳細分析'],
        ],
        '過去分析' => [
            ['id' => 16, 'name' => '新規分析依頼'],
            ['id' => 17, 'name' => '新規分析依頼中リスト'],
            ['id' => 18, 'name' => '分析済みリスト'],
        ],
    ],
];
