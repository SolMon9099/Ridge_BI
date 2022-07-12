<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxPermissionMembersToPitDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pit_detection_rules', function (Blueprint $table) {
            $table->tinyInteger('max_permission_members')->nullable()->after('blue_points')->comment('許容最大人数');
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
            $table->dropColumn('max_permission_members');
        });
    }
}
