<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('camera_id')->comment('カメラID');
            $table->string('installation_floor')->comment('設置フロア');
            $table->string('installation_position')->comment('設置場所');
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->string('remarks')->nullable()->comment('備考');
            $table->integer('is_enabled')->default(1)->comment('稼働状況 (1:稼働中, 0:停止中)');
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
        Schema::dropIfExists('cameras');
    }
}
