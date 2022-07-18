<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThiefDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thief_detection_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->references('id')->on('cameras')->comment('カメラID');
            $table->string('hanger')->comment('ハンガーの色');
            $table->string('color')->comment('カラー');
            $table->string('points')->comment('矩形データ');
            $table->timestamps();
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
        Schema::dropIfExists('thief_detection_rules');
    }
}
