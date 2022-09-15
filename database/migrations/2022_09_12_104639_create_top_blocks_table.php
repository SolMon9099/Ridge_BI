<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->comment('ユーザーのID');
            $table->integer('block_type')->comment('グリッド内容のタイプ : const.phpに定義');
            $table->integer('gs_x')->comment('グリッドのX座標');
            $table->integer('gs_y')->comment('グリッドのY座標');
            $table->integer('gs_w')->comment('グリッドの幅');
            $table->integer('gs_h')->comment('グリッドの高さ');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_blocks');
    }
}
