<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAuthorityGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authority_groups', function (Blueprint $table) {
            $table->dropForeign('authority_groups_authority_id_foreign');
            $table->dropForeign('authority_groups_group_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authority_groups', function (Blueprint $table) {
            $table->foreignId('authority_id')->references('id')->on('authorities');
            $table->foreignId('group_id')->references('id')->on('page_groups');
        });
    }
}
