<?php

return [
    // メールアドレスの確認
    'Verify' => [
        'Title' => 'メールアドレスの確認',
        'NewUrl' => '新しく確認用のURLを送信しました。',
        'SendActionUrl' => '送られたメールを確認してURLをクリックして下さい。',
        'NotEmail' => "メールが届いていない場合は、<a href=':url'>こちら</a>をクリックして下さい。",
    ],

    // メール
    'Mail' => [
        'Opning' => 'ご利用ありがとうございます。',
        'Whoops' => 'ご迷惑をおかけいたします。',
        'Regards' => '引き続きご利用の程、よろしくお願いいたします。',
        'NotClick' => "「:actionText」がクリックできない場合は以下のURLをブラウザにコピーして下さい。\n[:actionURL](:actionURL)",

        // メールアドレスの確認
        'Verify' => [
            'Title' => '【'.config('app.name').'】メールアドレスの確認',
            'Line' => 'メールアドレスを確認するには、下のボタンをクリックしてください。',
            'Action' => 'メールアドレスを確認',
            'PasswordLine' => 'あなたのパスワードは「:password」です。',
            'OutLine' => 'このメールにお心当たりがない場合、本メールは破棄して頂きますようお願いいたします。',
        ],

        //Reset Password Email To Staff
        'ResetPassword' => [
            'Title' => '【'.config('app.name').'】パスワードリセット',
            'Line1' => 'お客様のアカウントのパスワードリセットリクエストが届いているため,このメールが届いています。',
            'Action' => 'パスワードリセット',
            'Line2' => 'このパスワードのリセットリンクは:count分以内に期限切れになります。',
            'Line3' => 'パスワードのリセットを要求しなかった場合は,これ以上の処置は不要です。',
        ],

        //Send Login Email To Staff
        'LoginEmail' => [
            'Title' => '【'.config('app.name').'】スタッフページの発行',
            'NewLine' => '★★'.config('app.name').'★★に【:name】さんが登録しました。',
            'ClickLine' => 'パスワードを設定するには、下のボタンをクリックしてください。',
            'Action' => 'パスワードを設定',
        ],

        //Send Job Request To Staff
        'JobRequestEmail' => [
            'Title' => '【'.config('app.name').'】お仕事依頼',
            'NewLine' => '新しい仕事登録しました。',
            'ClickLine' => 'お仕事依頼詳細を確認するには、下のボタンをクリックしてください。',
            'Action' => 'お仕事依頼詳細へ',
        ],

        //Send Job Confirm To Staff
        'JobConfirmEmail' => [
            'Title' => '【'.config('app.name').'】お仕事確定しました。',
            'NewLine' => 'お仕事がシフト確定しました。',
            'ClickLine' => '確定したお仕事を確認するには、下のボタンをクリックしてください。',
            'Action' => '確定した仕事詳細へ',
        ],

        //Send Job Charge To Staff
        'JobChargeEmail' => [
            'Title' => '【'.config('app.name').'】お仕事担当依頼',
            'NewLine' => '新しい仕事登録しました。',
            'ClickLine' => 'お仕事依頼詳細を確認するには、下のボタンをクリックしてください。',
            'Action' => 'お仕事依頼詳細へ',
        ],

        //Send Job Charge To Admin
        'NotifyChangeStaffInfo' => [
            'Title' => '【'.config('app.name').'】スタッフ基本情報更新通知',
            'Name' => ':nameの基本情報が更新しました。',
        ],

        //Send Job Charge To Account
        'NotifyChangeSalesStaff' => [
            'Title' => '【'.config('app.name').'】担当情報更新通知',
            'Name' => '顧客名: :name',
        ],

        //Send Job Cancel To Staff
        'JobCancelEmail' => [
            'Title' => '【'.config('app.name').'】お仕事依頼キャンセル通知',
            'NewLine' => ':work_day :client :shift_type番',
        ],

        //Send Contact Email To Admin
        'ContactToAdmin' => [
            'Title' => '【'.config('app.name').'】お問い合わせ',
            'About' => '件名：:about<br/>',
            'Name' => 'お名前：:name<br/>',
            'KanaName' => 'フリガナ：:kana<br/>',
            'Email' => 'メールアドレス：:email<br/>',
            'Tel' => '電話番号：:tel<br/>',
            'ContentTitle' => 'お問い合わせ内容<br/>',
            'Content' => '<div style="white-space: pre-wrap !important;">:content</div>',
            'NewLine' => '<br/>',
        ],
        'ContactToCustomer' => [
            'Title' => '【'.config('app.name').'】お問い合わせありがとうございます',
        ],
    ],
];
