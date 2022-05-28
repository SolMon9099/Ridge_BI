<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDrawingFileNameToLocationDrawingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_drawings', function (Blueprint $table) {
            $table->string('drawing_file_name')->nullable()->after('drawing_file_path')->comment('図面ファイル名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_drawings', function (Blueprint $table) {
            $table->dropColumn('drawing_file_name');
        });
    }
}
