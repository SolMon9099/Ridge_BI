<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVcDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vc_detections', function (Blueprint $table) {
            $table->id();
            $table->string('camera_id')->comment('カメラID');
            $table->foreignId('rule_id')->references('id')->on('danger_area_detection_rules')->comment('ルールID');
            $table->string('vc_category')->comment('検知された車両の種類。可能な値：｛"truck", "forklift", "wheel loader"｝');
            $table->string('video_file_path')->comment('映像データパス');
            $table->dateTime('starttime')->comment('検知開始日時');
            $table->dateTime('endtime')->nullable()->comment('');
            $table->string('thumb_img_path')->nullable()->comment('サムネイル');
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
        Schema::dropIfExists('vc_detections');
    }
}
