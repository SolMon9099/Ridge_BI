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
];
