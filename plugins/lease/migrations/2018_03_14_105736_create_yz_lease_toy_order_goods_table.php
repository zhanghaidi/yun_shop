<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_order_goods')) {
            Schema::create('yz_lease_toy_order_goods', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->decimal('deposit', 12, 2)->default(0)->comment('商品租赁押金');
                $table->decimal('free_deposit', 12, 2)->default(0)->comment('商品订单租赁押金');
                $table->integer('order_id')->nullable()->default(0)->comment('订单id');
                $table->integer('goods_id')->nullable()->default(0)->comment('商品id');
                $table->integer('order_goods_id')->nullable()->default(0)->comment('订单商品表id');
                $table->integer('return_days')->nullable()->comment('租赁天数');
                $table->decimal('lease_price', 12, 2)->nullable()->default(0)->comment('租金优惠后价格');
                $table->integer('lease_total')->default(0)->nullable()->comment('购买数量');
                $table->integer('lease_rent_free')->nullable()->comment('权益免租金件数');
                $table->integer('lease_deposit_free')->nullable()->comment('权益免押金件数');
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
