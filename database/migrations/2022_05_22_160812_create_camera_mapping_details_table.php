<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCameraMappingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camera_mapping_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drawing_id')->references('id')->on('location_drawings')->comment('図面ID');
            $table->foreignId('camera_id')->references('id')->on('cameras')->comment('カメラID');
            $table->double('x_coordinate')->comment('X座標');
            $table->double('y_coordinate')->comment('Y座標');
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
        Schema::dropIfExists('camera_mapping_details');
    }
}
