<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeterDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meter_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('meter_detection_rules')->comment('ルールID');
            $table->string('video_file_path')->comment('映像データパス');
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
        Schema::dropIfExists('meter_detections');
    }
}
