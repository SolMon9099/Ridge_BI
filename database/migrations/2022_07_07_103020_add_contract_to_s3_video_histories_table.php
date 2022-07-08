<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractToS3VideoHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('s3_video_histories', function (Blueprint $table) {
            $table->string('contract_no')->after('id')->comment('契約番号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('s3_video_histories', function (Blueprint $table) {
            $table->dropColumn('contract_no');
        });
    }
}
