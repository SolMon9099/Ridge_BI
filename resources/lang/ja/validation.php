<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeにはアルファベッドのみ使用できます。',
    'alpha_dash' => ":attributeには英数字('A-Z','a-z','0-9')とハイフンと下線('-','_')が使用できます。",
    'alpha_num' => ":attributeには英数字('A-Z','a-z','0-9')が使用できます。",
    'array' => ':attributeには配列を指定してください。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',
    'between' => [
        'numeric' => ':attributeには:minから:maxまでの数字を指定してください。',
        'file' => ':attributeには:min KBから:max KBまでのサイズのファイルを指定してください。',
        'string' => ':attributeは:min文字から:max文字にしてください。',
        'array' => ':attributeの項目は:min個から:max個にしてください。',
    ],
    'boolean' => ":attributeには'true'か'false'を指定してください。",
    'confirmed' => ':attributeと:attribute確認が一致しません。',
    'date' => ':attributeは正しい日付ではありません。',
    'date_equals' => ':attributeは:dateに等しい日付でなければなりません。',
    'date_format' => ":attributeの形式は':format'と合いません。",
    'different' => ':attributeと:otherには異なるものを指定してください。',
    'digits' => ':attributeは:digits桁にしてください。',
    'digits_between' => ':attributeは:min桁から:max桁にしてください。',
    'dimensions' => ':attributeの画像サイズが無効です',
    'distinct' => ':attributeの値が重複しています。',
    'email' => ':attributeは有効なメールアドレス形式で指定してください。',
    'exists' => '選択された:attributeは有効ではありません。',
    'file' => ':attributeはファイルでなければいけません。',
    'filled' => ':attributeは必須です。',
    'gt' => [
        'numeric' => ':attributeは:valueより大きくなければなりません。',
        'file' => ':attributeは:value KBより大きくなければなりません。',
        'string' => ':attributeは:value文字より大きくなければなりません。',
        'array' => ':attributeの項目数は:value個より大きくなければなりません。',
    ],
    'gte' => [
        'numeric' => ':attributeは:value以上でなければなりません。',
        'file' => ':attributeは:value KB以上でなければなりません。',
        'string' => ':attributeは:value文字以上でなければなりません。',
        'array' => ':attributeの項目数は:value個以上でなければなりません。',
    ],
    'image' => ':attributeには画像を指定してください。',
    'in' => '選択された:attributeは有効ではありません。',
    'in_array' => ':attributeが:otherに存在しません。',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeはIPv4アドレスを指定してください。',
    'ipv6' => ':attributeはIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'lt' => [
        'numeric' => ':attributeは:valueより小さくなければなりません。',
        'file' => ':attributeは:value KBより小さくなければなりません。',
        'string' => ':attributeは:value文字より小さくなければなりません。',
        'array' => ':attributeの項目数は:value個より小さくなければなりません。',
    ],
    'lte' => [
        'numeric' => ':attributeは:value以下でなければなりません。',
        'file' => ':attributeは:value KB以下でなければなりません。',
        'string' => ':attributeは:value文字以下でなければなりません。',
        'array' => ':attributeの項目数は:value個以下でなければなりません。',
    ],
    'max' => [
        'numeric' => ':attributeには:max以下の数字を指定してください。',
        'file' => ':attributeには:max KB以下のファイルを指定してください。',
        'string' => ':attributeは:max文字以下にして入力ください。',
        'array' => ':attributeの項目は:max個以下にしてください。',
    ],
    'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':attributeには:min以上の数字を指定してください。',
        'file' => ':attributeには:min KB以上のファイルを指定してください。',
        'string' => ':attributeは:min文字以上に入力してください。',
        'array' => ':attributeの項目は:min個以上にしてください。',
    ],
    'not_in' => '選択された:attributeは有効ではありません。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeには数字を指定してください。',
    'present' => ':attributeが存在している必要があります。',
    'regex' => ':attributeには有効な正規表現を指定してください。',
    'required' => ':attributeは必ず指定してください。',
    'required_if' => ':otherが:valueの場合:attributeを指定してください。',
    'required_unless' => ':otherが:values以外の場合:attributeを指定してください。',
    'required_with' => ':valuesが指定されている場合:attributeも指定してください。',
    'required_with_all' => ':valuesが全て指定されている場合:attributeも指定してください。',
    'required_without' => ':valuesが指定されていない場合:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合:attributeを指定してください。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'numeric' => ':attributeには:sizeを指定してください。',
        'file' => ':attributeには:size KBのファイルを指定してください。',
        'string' => ':attributeは:size文字にしてください。',
        'array' => ':attributeの項目は:size個にしてください。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる必要があります。:values',
    'string' => ':attributeには文字を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => '指定の:attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeは有効なURL形式で指定してください。',
    'uuid' => ':attributeは有効なUUIDでなければなりません。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'ID' => 'ID',
        'login_id' => 'ログインＩＤ',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード確認',
        'name' => '氏名',
        'email' => 'ログインID',
        'picture' => '顔写真',
        'work_place' => '勤務先',
        'department' => '所属科',
    ],
];
