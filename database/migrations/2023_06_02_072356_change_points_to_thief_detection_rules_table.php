<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePointsToThiefDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thief_detection_rules', function (Blueprint $table) {
            $table->text('points')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thief_detection_rules', function (Blueprint $table) {
            // $table->integer('action_id')->comment('アクションID')->change();
        });
    }
}
