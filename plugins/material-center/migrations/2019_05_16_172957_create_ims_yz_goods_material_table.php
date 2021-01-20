<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzGoodsMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_goods_material')) {
            Schema::create('yz_goods_material', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('title')->comment('标题');
                $table->integer('goods_id')->comment('商品id');
                $table->string('content')->comment('推荐文案');
                $table->text('images')->comment('图片');
                $table->boolean('is_show')->default(0)->comment('是否显示 1显示 0不显示');
                $table->integer('share')->default(0)->comment('分享人数');
                $table->integer('download')->default(0)->comment('下载次数');
                $table->integer('collect')->default(0)->comment('收藏次数');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
                $table->integer('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
