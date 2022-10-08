<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToPitDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pit_detection_rules', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id')->comment('ルール名');
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
            $table->dropColumn('name');
        });
    }
}
