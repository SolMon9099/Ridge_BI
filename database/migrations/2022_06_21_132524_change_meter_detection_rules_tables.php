<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMeterDetectionRulesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meter_detection_rules', function (Blueprint $table) {
            $table->string('points')->after('color');
            $table->dropColumn('first_x');
            $table->dropColumn('first_y');
            $table->dropColumn('second_x');
            $table->dropColumn('second_y');
            $table->dropColumn('third_x');
            $table->dropColumn('third_y');
            $table->dropColumn('fourth_x');
            $table->dropColumn('fourth_y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meter_detection_rules', function (Blueprint $table) {
            $table->dropColumn('points');
            $table->double('first_x')->comment('四角形の初点のX桁表');
            $table->double('first_y')->comment('四角形の初点のY桁表');
            $table->double('second_x')->comment('四角形の2番目の点のX桁表');
            $table->double('second_y')->comment('四角形の2番目の点のY桁表');
            $table->double('third_x')->comment('四角形の3番目の点のX桁表');
            $table->double('third_y')->comment('四角形の3番目の点のY桁表');
            $table->double('fourth_x')->comment('四角形の4番目の点のX桁表');
            $table->double('fourth_y')->comment('四角形の4番目の点のY桁表');
        });
    }
}
