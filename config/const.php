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

    'action_cond_statement' => [
        1 => '横たわる',
        2 => '屈む',
        3 => '寄りかかる',
        4 => 'エリア侵入',
    ],
    'action_statement' => [
        1 => '横たわる',
        2 => '屈む',
        3 => '寄りかかる',
        4 => 'エリア内侵入検知',
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

    'header_menu_classes' => [
        1 => 'pit-menu',
        2 => 'danger-menu',
        3 => 'shelf-menu',
        4 => 'thief-menu',
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
        'ダッシュボード' => [
            ['id' => 30, 'name' => 'ダッシュボード'],
        ],
        'ピット入退場検知' => [
            ['id' => 9, 'name' => 'TOP'],
            ['id' => 7, 'name' => 'ルール新規作成'],
            ['id' => 26, 'name' => 'ルール一覧・編集'],
            ['id' => 8, 'name' => '検知リスト'],
            ['id' => 22, 'name' => '過去グラフ'],
        ],
        '危険エリア侵入検知' => [
            ['id' => 12, 'name' => 'TOP'],
            ['id' => 10, 'name' => 'ルール新規作成'],
            ['id' => 27, 'name' => 'ルール一覧・編集'],
            ['id' => 11, 'name' => '検知リスト'],
            ['id' => 23, 'name' => '過去グラフ'],
        ],
        '棚乱れ検知' => [
            ['id' => 15, 'name' => 'TOP'],
            ['id' => 13, 'name' => 'ルール新規作成'],
            ['id' => 28, 'name' => 'ルール一覧・編集'],
            ['id' => 14, 'name' => '検知リスト'],
            ['id' => 24, 'name' => '過去グラフ'],
        ],
        '大量盗難検知' => [
            ['id' => 25, 'name' => 'TOP'],
            ['id' => 19, 'name' => 'ルール新規作成'],
            ['id' => 29, 'name' => 'ルール一覧・編集'],
            ['id' => 20, 'name' => '検知リスト'],
            ['id' => 21, 'name' => '過去グラフ'],
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
        30 => 'admin.top',
        7 => 'admin.pit.cameras_for_rule',
        26 => 'admin.pit',
        8 => 'admin.pit.list',
        9 => 'admin.pit.detail',
        22 => 'admin.pit.past_analysis',
        10 => 'admin.danger.cameras_for_rule',
        27 => 'admin.danger',
        11 => 'admin.danger.list',
        12 => 'admin.danger.detail',
        23 => 'admin.danger.past_analysis',
        13 => 'admin.shelf.cameras_for_rule',
        28 => 'admin.shelf',
        14 => 'admin.shelf.list',
        15 => 'admin.shelf.detail',
        24 => 'admin.shelf.past_analysis',
        // 16 => 'admin.analyze',
        // 17 => 'admin.analyze.now_list',
        // 18 => 'admin.analyze.finish_list',
        19 => 'admin.thief.cameras_for_rule',
        29 => 'admin.thief',
        20 => 'admin.thief.list',
        21 => 'admin.thief.detail',
        25 => 'admin.thief.past_analysis',
    ],

    'super_admin_not_allowed_pages' => [
        7 => 'admin.pit.cameras_for_rule',
        10 => 'admin.danger.cameras_for_rule',
        13 => 'admin.shelf.cameras_for_rule',
        19 => 'admin.thief.cameras_for_rule',
    ],

    'camera_start_time' => '08:00:00',
    'camera_end_time' => '21:00:00',
    'request_interval' => 5,        //分
    'detection_video_length' => 10,    //秒
    'pit_time_options' => [15, 30, 45, 60, 75, 90, 105, 120],
    'shelf_max_rect_numbers' => 3,
    'thief_max_rect_numbers' => 3,
    'danger_max_figure_numbers' => 3,
    'drawing_width_criteria' => 1024,    //px
    'drawing_height_criteria' => 960,    //px

    'top_block_types' => [
        1 => 'live_video_pit',
        2 => 'live_video_danger',
        3 => 'live_video_shelf',
        4 => 'live_video_thief',
        5 => 'live_graph_pit',
        6 => 'live_graph_danger',
        7 => 'live_graph_shelf',
        8 => 'live_graph_thief',
        9 => 'detect_list_pit',
        10 => 'detect_list_danger',
        11 => 'detect_list_shelf',
        12 => 'detect_list_thief',
        13 => 'past_graph_pit',
        14 => 'past_graph_danger',
        15 => 'past_graph_shelf',
        16 => 'past_graph_thief',
        17 => 'pit_history',
        18 => 'recent_detect_pit',
        19 => 'recent_detect_danger',
        20 => 'recent_detect_shelf',
        21 => 'recent_detect_thief',
    ],

    'top_block_type_codes' => [
        'live_video_pit' => 1,
        'live_video_danger' => 2,
        'live_video_shelf' => 3,
        'live_video_thief' => 4,
        'live_graph_pit' => 5,
        'live_graph_danger' => 6,
        'live_graph_shelf' => 7,
        'live_graph_thief' => 8,
        'detect_list_pit' => 9,
        'detect_list_danger' => 10,
        'detect_list_shelf' => 11,
        'detect_list_thief' => 12,
        'past_graph_pit' => 13,
        'past_graph_danger' => 14,
        'past_graph_shelf' => 15,
        'past_graph_thief' => 16,
        'pit_history' => 17,
        'recent_detect_pit' => 18,
        'recent_detect_danger' => 19,
        'recent_detect_shelf' => 20,
        'recent_detect_thief' => 21,
    ],

    'top_block_titles' => [
        1 => '■リアルタイム映像(ピット入退場)',
        2 => '■リアルタイム映像(危険エリア侵入)',
        3 => '■リアルタイム映像(棚乱れ)',
        4 => '■リアルタイム映像(大量盗難)',
        5 => '■リアルタイムグラフ(ピット入退場)',
        6 => '■リアルタイムグラフ(危険エリア侵入)',
        7 => '■リアルタイムグラフ(棚乱れ)',
        8 => '■リアルタイムグラフ(大量盗難)',
        9 => '■検知リスト(ピット入退場)',
        10 => '■検知リスト(危険エリア侵入)',
        11 => '■検知リスト(棚乱れ)',
        12 => '■検知リスト(大量盗難)',
        13 => '■過去グラフ(ピット入退場)',
        14 => '■過去グラフ(危険エリア侵入)',
        15 => '■過去グラフ(棚乱れ)',
        16 => '■過去グラフ(大量盗難)',
        17 => '■ピット入退場履歴',
        18 => '■最新の検知(ピット入退場)',
        19 => '■最新の検知(危険エリア侵入)',
        20 => '■最新の検知(棚乱れ)',
        21 => '■最新の検知(大量盗難)',
    ],

    'top_block_gears' => [
        1 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        2 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        3 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        4 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        5 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        6 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        7 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        8 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        9 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
        ],
        10 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
        ],
        11 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
        ],
        12 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        13 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        14 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        15 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        16 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
            'change_period' => '期間変更',
            'change_x_axis' => 'グラフの横軸変更',
        ],
        17 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        18 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        19 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        20 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
        21 => [
            'delete' => '削除',
            'change_camera' => '指定カメラ変更',
        ],
    ],
    'rule_default_color' => ['#FFFF00', '#FF0000', '#00FF00'],
];
