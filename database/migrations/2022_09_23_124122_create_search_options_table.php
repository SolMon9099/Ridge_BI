<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->comment('ユーザーのID');
            $table->string('page_name')->comment('ページ：const.phpに定義');
            $table->text('options')->nullable()->comment('検索オプション');
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
        Schema::dropIfExists('search_options');
    }
}
