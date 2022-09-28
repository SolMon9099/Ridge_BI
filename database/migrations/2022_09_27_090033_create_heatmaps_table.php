<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeatmapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('heatmaps', function (Blueprint $table) {
            $table->id();
            $table->string('camera_id')->comment('カメラID');
            $table->integer('grid_x')->default(128)->comment('グリッドサイズ');
            $table->integer('grid_y')->default(72)->comment('グリッドサイズ');
            $table->text('heatmap_data')->comment('ヒートマップ:２次元配列');
            $table->float('quality_score')->comment('品質スコア');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('heatmaps');
    }
}
