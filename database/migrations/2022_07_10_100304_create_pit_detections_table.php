<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePitDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pit_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->references('id')->on('cameras')->comment('カメラID');
            $table->foreignId('rule_id')->references('id')->on('pit_detection_rules')->comment('ルールID');
            $table->dateTime('starttime')->comment('検知開始日時');
            $table->dateTime('endtime')->nullable()->comment('検知開始日時');
            $table->string('video_file_path')->comment('映像データパス');
            $table->string('thumb_img_path')->comment('サムネイル');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pit_detections');
    }
}
