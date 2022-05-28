<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeterDetectionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meter_detection_rules', function (Blueprint $table) {
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
            $table->tinyInteger('meter_type')->comment('1-数値タイプ, 2-指針タイプ（config/const.phpにconstに設定）');
            $table->integer('range')->comment('1-次の値の間,　2-次の値の間以外　 3-次の値より大きい, 4-次の値より小さい, (meter_type = 1)
                1-範囲内, 2-範囲外(meter_type = 2)
                （config/const.phpにconstに設定）');
            $table->double('value1')->comment('if meter_type = 1, 有効 else 無効');
            $table->double('value2')->comment('if meter_type = 1, 有効 else 無効');
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
        Schema::dropIfExists('meter_detection_rules');
    }
}
