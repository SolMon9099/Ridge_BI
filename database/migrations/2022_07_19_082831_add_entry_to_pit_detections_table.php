<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntryToPitDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pit_detections', function (Blueprint $table) {
            $table->integer('nb_entry')->default(0)->after('thumb_img_path')->comment('入場数');
            $table->integer('nb_exit')->default(0)->after('nb_entry')->comment('退場数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pit_detections', function (Blueprint $table) {
            $table->dropColumn('nb_entry');
            $table->dropColumn('nb_exit');
        });
    }
}
