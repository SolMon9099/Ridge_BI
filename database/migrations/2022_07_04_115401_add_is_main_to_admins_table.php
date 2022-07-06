<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsMainToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('is_main_admin')->nullable()->after('header_menu_ids')->comment('最初の管理者フラグ');
            $table->string('safie_user_name')->nullable()->after('is_main_admin')->comment('SafieのID');
            $table->string('safie_password')->nullable()->after('safie_user_name')->comment('Safieのパス');
            $table->string('safie_client_id')->nullable()->after('safie_password')->comment('SafieのクライアントID');
            $table->string('safie_client_secret')->nullable()->after('safie_client_id')->comment('Safieのsecret');
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
            $table->dropColumn('is_main_admin');
            $table->dropColumn('safie_user_name');
            $table->dropColumn('safie_password');
            $table->dropColumn('safie_client_id');
            $table->dropColumn('safie_client_secret');
        });
    }
}
