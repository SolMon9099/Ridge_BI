<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStarttimeToHeatmapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('heatmaps', function (Blueprint $table) {
            $table->dateTime('starttime')->after('quality_score')->comment('解析動画開始時刻');
            $table->dateTime('endtime')->after('starttime')->comment('解析動画終了時刻');
            $table->integer('time_diff')->after('endtime')->comment('解析動画時間');
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
        Schema::table('heatmaps', function (Blueprint $table) {
            $table->dropColumn('starttime');
            $table->dropColumn('endtime');
            $table->dropColumn('time_diff');
        });
    }
}
