<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateS3VideoHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s3_video_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('request_id')->comment('リクエストID');
            $table->string('device_id')->comment('カメラID');
            $table->dateTime('start_time')->comment('開始時刻');
            $table->dateTime('end_time')->comment('終了時刻');
            $table->string('file_name')->nullable()->comment('ファイル名');
            $table->string('file_path')->nullable()->comment('s3のファイルパス');
            $table->tinyInteger('status')->default(0)->comment('0:メディアファイル作成リクエスト実行中(PROCESSING), 1:リクエスト済み(AVAILABLE)、2:s3に保存済み');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s3_video_histories');
    }
}
