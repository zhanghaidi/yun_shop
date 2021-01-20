<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzPluginLeaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_plugin_lease_order')) {
            Schema::create('yz_plugin_lease_order',
                function (Blueprint $table) {
                    $table->integer('id', true);
                    $table->integer('uniacid')->nullable()->default(0)->index('idx_uniacid');
                    $table->integer('order_id')->nullable()->default(0)->comment('订单id');
                    $table->integer('member_id')->nullable()->default(0)->comment('会员id');
                    $table->decimal('deposit_total', 12, 2)->default(0)->comment('押金总和');
                    $table->integer('return_days')->default(0)->comment('租期天数');
                    $table->integer('start_time')->nullable()->comment('开始租赁时间');
                    $table->integer('return_time')->nullable()->comment('归还时间');
                    $table->decimal('return_deposit', 12, 2)->default(0)->comment('退还押金总和');
                    $table->string('order_sn', 255)->nullable()->comment('订单号');
                    $table->integer('updated_at')->nullable();
                    $table->integer('created_at')->nullable();
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
        Schema::dropIfExists('yz_plugin_lease_order');
    }
}
