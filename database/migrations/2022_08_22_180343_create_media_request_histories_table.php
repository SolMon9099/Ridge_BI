<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaRequestHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_request_histories', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->comment('カメラID');
            $table->dateTime('start_time')->comment('開始時刻');
            $table->dateTime('end_time')->comment('終了時刻');
            $table->integer('time_diff')->comment('録画時間(分)');
            $table->string('request_resource')->nullable()->comment('呼出源');
            $table->string('request_id')->nullable()->comment('リクエストID');
            $table->integer('http_code')->comment('レスポンスコード');
            $table->boolean('status')->default(0)->comment('作成状態　1:残りの状態, 0:未作成または削除');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_request_histories');
    }
}
