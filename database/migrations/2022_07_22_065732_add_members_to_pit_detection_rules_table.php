<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembersToPitDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pit_detection_rules', function (Blueprint $table) {
            $table->integer('min_members')->nullable()->after('max_permission_time')->comment('ピット内人数');
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
            $table->dropColumn('min_members');
        });
    }
}
