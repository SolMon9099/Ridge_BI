<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorityGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authority_groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('authority_id')->references('id')->on('authorities');
            $table->foreignId('group_id')->references('id')->on('page_groups');
            $table->tinyInteger('access_flag')->default(0)->comment('許可状況');
            $table->integer('created_by')->nullable()->comment('データ作成者ID');
            $table->integer('updated_by')->nullable()->comment('データ最終更新者ID');
            $table->integer('deleted_by')->nullable()->comment('データ論理削除者ID');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authority_groups');
    }
}
