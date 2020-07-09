<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzJdSupplyOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_jd_supply_order_goods')) {
            Schema::create('yz_jd_supply_order_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id')->default(0)->index('order_idx')->comment('订单id');
                $table->integer('goods_id')->default(0)->comment('商品id');
                $table->integer('jd_goods_id')->default(0)->comment('第三方商品id');
                $table->integer('jd_option_id')->default(0)->comment('第三方规格id');
                $table->integer('total')->default(0)->comment('商品数量');
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
        Schema::dropIfExists('yz_jd_supply_order_goods');
    }
}
