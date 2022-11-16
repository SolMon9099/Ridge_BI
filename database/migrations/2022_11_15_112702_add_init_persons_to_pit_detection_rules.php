<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitPersonsToPitDetectionRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pit_detection_rules', function (Blueprint $table) {
            $table->integer('init_persons')->after('min_members')->comment('現在のピット内人数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pit_detection_rules', function (Blueprint $table) {
            $table->dropColumn('init_persons');
        });
    }
}
