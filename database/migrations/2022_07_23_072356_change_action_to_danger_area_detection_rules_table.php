<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeActionToDangerAreaDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('danger_area_detection_rules', function (Blueprint $table) {
            $table->string('action_id')->comment('アクションIDの配列')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('danger_area_detection_rules', function (Blueprint $table) {
            $table->integer('action_id')->comment('アクションID')->change();
        });
    }
}
