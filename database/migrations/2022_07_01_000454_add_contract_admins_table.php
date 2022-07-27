<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('authority_id')->comment('99999:スーパー管理者, 1:管理者, 2:現場責任者, 3:現場担当者')->change();
            $table->string('contract_no')->nullable()->after('authority_id')->comment('契約番号');
            $table->string('header_menu_ids')->nullable()->after('contract_no')->comment('1:ピット入退場検知, 2:危険エリア侵入検知, 3 :棚乱れ検知,4:大量盗難検知, 5 :過去分析');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('authority_id')->comment('権限ID')->change();
            $table->dropColumn('contract_no');
            $table->dropColumn('header_menu_ids');
        });
    }
}
