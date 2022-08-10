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

    'super_admin' => [99999 => 'スーパー管理者'],

    'super_admin_code' => 99999,

    'authorities' => [
        1 => '管理者',
        // 2 => '現場責任者',
        3 => '現場担当者',
    ],
    'authorities_codes' => [
        'admin' => 1,
        // 'owner' => 2,
        'manager' => 3,
    ],

    'header_menus_routes' => [
        1 => 'admin.pit',
        2 => 'admin.danger',
        3 => 'admin.shelf',
        4 => 'admin.thief',
        // 5 => 'admin.analyze',
    ],

    'header_menus' => [
        1 => 'ピット入退場検知',
        2 => '危険エリア侵入検知',
        3 => '棚乱れ検知',
        4 => '大量盗難検知',
        // 5 => '過去分析',
    ],

    'header_menu_codes' => [
        'pit' => 1,
        'danger_area' => 2,
        'shelf' => 3,
        'thief' => 4,
        // 'past_analysis' => 5,
    ],

    'admin_pages' => ['全般', '現場設定', 'カメラ設定'],

    'pages' => [
        '全般' => [
            ['id' => 1, 'name' => '権限グループ設定'],
            ['id' => 2, 'name' => 'アカウント管理'],
            ['id' => 3, 'name' => '通知設定'],
        ],
        '現場設定' => [
            ['id' => 4, 'name' => '設置エリア一覧'],
        ],
        'カメラ設定' => [
            ['id' => 5, 'name' => 'カメラ一覧'],
            ['id' => 6, 'name' => 'カメラマッピング一覧'],
        ],
        'ピット入退場検知' => [
            ['id' => 7, 'name' => 'ルール新規作成・編集'],
            ['id' => 9, 'name' => 'ダッシュボード'],
            ['id' => 8, 'name' => '検知リスト'],
            ['id' => 22, 'name' => '過去データ'],
        ],
        '危険エリア侵入検知' => [
            ['id' => 10, 'name' => 'ルール新規作成・編集'],
            ['id' => 12, 'name' => 'ダッシュボード'],
            ['id' => 11, 'name' => '検知リスト'],
            ['id' => 23, 'name' => '過去データ'],
        ],
        '棚乱れ検知' => [
            ['id' => 13, 'name' => 'ルール一覧'],
            ['id' => 15, 'name' => 'ダッシュボード'],
            ['id' => 14, 'name' => '検知リスト'],
            ['id' => 24, 'name' => '詳細分析'],
        ],
        '大量盗難検知' => [
            ['id' => 19, 'name' => 'ルール一覧'],
            ['id' => 25, 'name' => 'ダッシュボード'],
            ['id' => 20, 'name' => '検知リスト'],
            ['id' => 21, 'name' => '詳細分析'],
        ],
        // '過去分析' => [
        //     ['id' => 16, 'name' => '新規分析依頼'],
        //     ['id' => 17, 'name' => '新規分析依頼中リスト'],
        //     ['id' => 18, 'name' => '分析済みリスト'],
        // ],
    ],

    'page_route_names' => [
        1 => 'admin.top.permission_group',
        2 => 'admin.account',
        3 => 'admin.notification',
        4 => 'admin.location',
        5 => 'admin.camera',
        6 => 'admin.camera.mapping',
        7 => 'admin.pit',
        8 => 'admin.pit.list',
        9 => 'admin.pit.detail',
        22 => 'admin.pit.past_analysis',
        10 => 'admin.danger',
        11 => 'admin.danger.list',
        12 => 'admin.danger.detail',
        23 => 'admin.danger.past_analysis',
        13 => 'admin.shelf',
        14 => 'admin.shelf.list',
        15 => 'admin.shelf.detail',
        24 => 'admin.shelf.past_analysis',
        // 16 => 'admin.analyze',
        // 17 => 'admin.analyze.now_list',
        // 18 => 'admin.analyze.finish_list',
        19 => 'admin.thief',
        20 => 'admin.thief.list',
        21 => 'admin.thief.detail',
        25 => 'admin.thief.past_analysis',
    ],

    'camera_start_time' => '08:00:00',
    'camera_end_time' => '21:00:00',
    'request_interval' => 5,        //分
    'pit_time_options' => [15, 30, 45, 60, 75, 90, 105, 120],
    'shelf_max_rect_numbers' => 3,
    'thief_max_rect_numbers' => 3,
    'danger_max_figure_numbers' => 3,
    'drawing_width_criteria' => 1024,    //px
    'drawing_height_criteria' => 960,    //px
];
