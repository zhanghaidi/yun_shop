<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzLeaseToyGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_lease_toy_goods')) {
            Schema::create('yz_lease_toy_goods', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                $table->integer('goods_id')->nullable()->default(0)->comment('租赁商品id');
                $table->integer('immed_goods_id')->nullable()->default(0)->comment('立即商品id');
                $table->tinyInteger('is_lease')->nullable()->default(0)->comment('租赁商品 0：否 1：是');
                $table->tinyInteger('is_rights')->nullable()->default(1)->comment('支持等级权益 0：否 1：是');
                $table->decimal('goods_deposit', 12, 2)->default(0)->comment('商品押金');
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->integer('updated_at')->nullable();
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
        Schema::dropIfExists('yz_lease_toy_goods');
    }
}
