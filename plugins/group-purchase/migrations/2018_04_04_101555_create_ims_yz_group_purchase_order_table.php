<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImsYzGroupPurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_group_purchase_order')) {
            Schema::create('yz_group_purchase_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('order_id')->nullable();
                $table->integer('uid')->nullable();
                $table->string('buyer_name')->nullable();
                $table->integer('recommend_id')->nullable();
                $table->string('recommend_name')->nullable();
                $table->string('order_sn')->nullable();
                $table->decimal('price')->nullable();
                $table->decimal('goods_price')->nullable();
                $table->string('goods_total')->nullable();
                $table->string('status')->nullable();
                $table->string('order_type')->nullable();
                $table->string('shipping_address')->nullable();
                $table->string('store_address')->nullable();
                $table->integer('created_at')->nullable();
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
        Schema::dropIfExists('ims_yz_group_purchase_order');
    }
}
