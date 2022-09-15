<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsToTopBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('top_blocks', function (Blueprint $table) {
            $table->text('options')->after('block_type')->nullable()->comment('オプション');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_blocks', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
