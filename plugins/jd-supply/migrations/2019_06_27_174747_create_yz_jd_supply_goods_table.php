<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzJdSupplyGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('yz_jd_supply_goods')) {
            Schema::create('yz_jd_supply_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->default(0)->nullable()->index('goods_idx')->comment('商品ID');
                $table->integer('jd_goods_id')->default(0)->nullable()->index('jd_goods_idx')->comment('第三方商品ID');
                $table->integer('shop_id')->default(0)->nullable()->comment('第三方店铺ID');
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
        Schema::dropIfExists('yz_jd_supply_goods');
    }
}
