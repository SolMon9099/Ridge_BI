<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHourMinsToShelfDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shelf_detection_rules', function (Blueprint $table) {
            $table->integer('hour')->after('points')->comment('定時撮影時刻の時');
            $table->integer('mins')->after('hour')->comment('定時撮影時刻の分');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shelf_detection_rules', function (Blueprint $table) {
            $table->dropColumn('hour');
            $table->dropColumn('mins');
        });
    }
}
