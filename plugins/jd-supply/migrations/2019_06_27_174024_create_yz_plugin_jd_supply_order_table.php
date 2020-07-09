<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzPluginJdSupplyOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_plugin_jd_supply_order')) {
            Schema::create('yz_plugin_jd_supply_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id')->default(0)->index('order_idx')->comment('订单id');
                $table->string('order_sn')->nullable()->default('')->comment('订单号');
                $table->integer('member_id')->default(0)->comment('会员ID');
                $table->decimal('order_price', 14,2)->default(0)->comment('订单金额');
                $table->tinyInteger('status')->nullable()->default(0)->comment('订单状态');
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
        Schema::dropIfExists('yz_plugin_jd_supply_order');
    }
}
