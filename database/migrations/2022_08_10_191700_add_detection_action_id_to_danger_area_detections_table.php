<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetectionActionIdToDangerAreaDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('danger_area_detections', function (Blueprint $table) {
            $table->integer('detection_action_id')->after('rule_id')->comment('検知アクションID');
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
            $table->dropColumn('detection_action_id');
        });
    }
}
