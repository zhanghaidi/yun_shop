<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_record')) {
            Schema::create('yz_lease_toy_record', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('lease_member_id')->nullable()->default(0)->comment('租赁过的会员');
                $table->decimal('deposit', 12, 2)->default(0)->comment('会员租赁押金');
                $table->integer('order_id')->nullable()->default(0)->comment('订单id');
                $table->integer('goods_id')->nullable()->default(0)->comment('商品id');
                $table->integer('order_goods_id')->nullable()->default(0)->comment('订单商品表id');
                $table->string('order_sn', 255)->nullable()->comment('单号');
                $table->tinyInteger('status')->default(0)->comment('0：冻结 1：免押金 2：已退还');
                $table->integer('return_days')->nullable()->comment('租赁天数');
                $table->decimal('retreat_deposit', 12, 2)->default(0)->comment('退还押金');
                $table->decimal('lease_price', 12, 2)->nullable()->default(0)->comment('租金优惠后价格');
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
        Schema::dropIfExists('yz_lease_toy_record');
    }
}
