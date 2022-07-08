<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDangerAreaDetectionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('danger_area_detections', function (Blueprint $table) {
            $table->string('camera_id')->after('id')->comment('カメラID');
            $table->dateTime('starttime')->after('video_file_path')->comment('検知開始日時');
            $table->dateTime('endtime')->nullable()->after('starttime');
            $table->string('thumb_img_path')->nullable()->after('endtime')->comment('サムネイル');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('danger_area_detections', function (Blueprint $table) {
            $table->dropColumn('camera_id');
            $table->dropColumn('starttime');
            $table->dropColumn('endtime');
            $table->dropColumn('thumb_img_path');
        });
    }
}
