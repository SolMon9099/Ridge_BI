<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToHeatmapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('heatmaps', function (Blueprint $table) {
            $table->tinyInteger('status')->nullable()->after('time_diff')->comment('1:AIへ解析依頼完了, 2:AIから解析結果受付完了');
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
            $table->dropColumn('status');
        });
    }
}
