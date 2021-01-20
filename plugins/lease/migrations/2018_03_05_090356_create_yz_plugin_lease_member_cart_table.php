<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzPluginLeaseMemberCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_plugin_lease_member_cart')) {
            Schema::create('yz_plugin_lease_member_cart', function (Blueprint $table) {
                $table->integer('id', true)->comment('主键');
                $table->integer('member_id')->comment('会员id');
                $table->integer('uniacid')->comment('所属公众号id');
                $table->integer('goods_id')->comment('商品id');
                $table->integer('total')->comment('加入购物车数量');
                $table->integer('option_id')->comment('商品规格id');
                $table->integer('created_at')->comment('加入购物车时间');
                $table->integer('updated_at')->comment('最后一次修改时间');
                $table->integer('deleted_at')->nullable()->comment('移除购物车时间');
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
        Schema::dropIfExists('yz_plugin_lease_member_cart');
    }
}
