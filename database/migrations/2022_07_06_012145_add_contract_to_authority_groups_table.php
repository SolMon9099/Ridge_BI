<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractToAuthorityGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authority_groups', function (Blueprint $table) {
            $table->string('contract_no')->after('updated_at')->comment('契約番号');
            $table->string('authority_id')->comment('1:管理者,2:現場責任者 3:現場担当者')->change();
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
            $table->dropColumn('contract_no');
        });
    }
}
