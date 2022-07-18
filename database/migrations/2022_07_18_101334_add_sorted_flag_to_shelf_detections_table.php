<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortedFlagToShelfDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shelf_detections', function (Blueprint $table) {
            $table->boolean('sorted_flag')->after('thumb_img_path')->default(false)->comment('整理済みフラグ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shelf_detections', function (Blueprint $table) {
            $table->dropColumn('sorted_flag');
        });
    }
}
