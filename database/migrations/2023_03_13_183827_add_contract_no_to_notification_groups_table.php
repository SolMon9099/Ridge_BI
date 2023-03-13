<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractNoToNotificationGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_groups', function (Blueprint $table) {
            $table->string('contract_no')->nullable()->after('emails')->comment('契約番号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_groups', function (Blueprint $table) {
            $table->dropColumn('contract_no');
        });
    }
}
