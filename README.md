## ソースコード修正後デプロイ方法

変更したソースをgitにプッシュし、ssh接続して以下のコマンドを実行します

- cd ridgeBI/
- git pull origin main

username, passwordは[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]() です。

キャッシュクリア、db migrationなどのためには以下のコマンドを実行する。

- sh deploy.sh

## ソースコードのディレクトリ構成

### BI-->AIリクエスト送信

BI-->AIリクエストはcronで自動送信しています。
[app\Console](https://github.com/polsjapan/ridgeBI/tree/main/app/Console)ディレクトリにcronによって自動実行されるモジュールがあります。

- **[app\Console\Kernel.php](https://github.com/polsjapan/ridgeBI/blob/main/app/Console/Kernel.php)**<br>
cronモジュールを総合的に定義したファイルです。
- **[app\Console\Commands\S3Command.php](https://github.com/polsjapan/ridgeBI/blob/main/app/Console/Commands/S3Command.php)**<br>
BI-->AIリクエスト送信モジュールです。 S3にカメラの動画を定期的に保存しながらBI-->AIリクエストを送信します。

### AI-->BIリクエスト受信

[app\Http\Controllers\Api](https://github.com/polsjapan/ridgeBI/tree/main/app/Http/Controllers/Api)ディレクトリには外部と連動するapiモジュールがあります。

- **[app\Http\Controllers\Api\DetectionController.php](https://github.com/polsjapan/ridgeBI/blob/main/app/Http/Controllers/Api/DetectionController.php)**<br>
危険エリア侵入、ピット入退場などの検知結果を受信するモジュールです。検知結果受信後sendAlertMail関数を使ってメール通信をする。<br/>[app\Http\Controllers\Api\MailController.php](https://github.com/polsjapan/ridgeBI/blob/main/app/Http/Controllers/Api/MailController.php)メール通信モジュールです。sendAlertMail関数がMailControllerのsendInavasionMail関数と連動する。

### 定数

- **[config\const.php](https://github.com/polsjapan/ridgeBI/blob/main/config/const.php)**<br>
Rinocaシステムに利用する全て定数が定義されています。

### Route & Controller & View
- **[routes\admin.php](https://github.com/polsjapan/ridgeBI/blob/main/routes/admin.php)**<br>
Rinocaシステムのルーチンファイルです。URLからそのControllerを見つけることができます。<br>

- **[app\Http\Controllers\Admin](https://github.com/polsjapan/ridgeBI/tree/main/app/Http/Controllers/Admin)**<br>
Rinocaシステムに利用するControllerが定義されています。<br>

- **[resources\views\admin](https://github.com/polsjapan/ridgeBI/tree/main/resources/views/admin)**<br>
Rinocaシステムのフロントです。<br>

> 例：危険エリア侵入の検知リストページについて<br>
>> URL：/admin/danger/list<br>
ルーチン：Route::get('/list', 'DangerController@list')->name('admin.danger.list');<br>
Controller：[app\Http\Controllers\Admin\DangerController.php](https://github.com/polsjapan/ridgeBI/blob/main/app/Http/Controllers/Admin/DangerController.php)のlist関数<br>
View：[resources\views\admin\danger\list.blade.php](https://github.com/polsjapan/ridgeBI/blob/main/resources/views/admin/danger/list.blade.php)<br>


### エリア設定
危険エリア侵入エリア設定 -> [resources\views\admin\danger\_form.blade.php](#)<br>
ピット入退場エリア設定 -> [resources\views\admin\pit\_form.blade.php](#)
