<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShelfDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shelf_detection_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camera_id')->references('id')->on('cameras')->comment('カメラID');
            $table->string('color')->comment('カラー');
            $table->double('first_x')->comment('四角形の初点のX桁表');
            $table->double('first_y')->comment('四角形の初点のY桁表');
            $table->double('second_x')->comment('四角形の2番目の点のX桁表');
            $table->double('second_y')->comment('四角形の2番目の点のY桁表');
            $table->double('third_x')->comment('四角形の3番目の点のX桁表');
            $table->double('third_y')->comment('四角形の3番目の点のY桁表');
            $table->double('fourth_x')->comment('四角形の4番目の点のX桁表');
            $table->double('fourth_y')->comment('四角形の4番目の点のY桁表');
            $table->integer('action_id')->comment('アクション');
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
        Schema::dropIfExists('shelf_detection_rules');
    }
}
