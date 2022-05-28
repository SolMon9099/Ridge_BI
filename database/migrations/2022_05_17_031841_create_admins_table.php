<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('email')->comment('メールアドレス');
            $table->string('name')->comment('名前');
            $table->string('password')->comment('パスワード');
            $table->string('department')->comment('部門');
            $table->tinyInteger('is_enabled')->default(1)->comment('有効設定');
            $table->foreignId('authority_id')->references('id')->on('authorities');
            $table->integer('created_by')->nullable()->comment('データ作成者ID');
            $table->integer('updated_by')->nullable()->comment('データ最終更新者ID');
            $table->integer('deleted_by')->nullable()->comment('データ論理削除者ID');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE admins COMMENT 'サイト管理者'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
