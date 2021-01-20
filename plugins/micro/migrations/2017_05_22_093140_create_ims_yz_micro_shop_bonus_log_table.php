<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMicroShopBonusLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_micro_shop_bonus_log')) {
            Schema::create('yz_micro_shop_bonus_log',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->default(0);
                    $table->integer('shop_id')->default(0);
                    $table->integer('member_id')->default(0);
                    $table->decimal('bonus_money', 10)->default(0.00);
                    $table->decimal('bonus_ratio', 10)->default(0.00);
                    $table->integer('level_id')->default(0);
                    $table->integer('order_id')->default(0);
                    $table->string('order_sn', 50)->default('0');
                    $table->integer('order_buyer')->default(0);
                    $table->integer('goods_id')->default(0);
                    $table->string('goods_title')->default('0');
                    $table->decimal('goods_price',
                        10)->nullable()->default(0.00);
                    $table->decimal('goods_cost_price', 10)->default(0.00);
                    $table->string('goods_thumb')->default('0');
                    $table->integer('goods_total')->default(0);
                    $table->string('pay_type', 20)->default(0);
                    $table->string('pay_sn', 50)->default('0');
                    $table->integer('pay_time')->default(0);
                    $table->integer('complete_time')->default(0);
                    $table->boolean('order_status')->default(0);
                    $table->boolean('apply_status')->default(0);
                    $table->integer('apply_time')->default(0);
                    $table->integer('refund_time')->default(0);
                    $table->integer('is_lower')->default(0);
                    $table->integer('lower_level_shop_id')->default(0);
                    $table->integer('lower_level_member_id')->default(0);
                    $table->string('lower_level_nickname')->default('0');
                    $table->decimal('lower_level_bonus_money',
                        10)->default(0.00);
                    $table->decimal('agent_bonus_ratio', 10)->default(0.00);
                    $table->integer('created_at')->default(0);
                    $table->integer('updated_at')->default(0);
                    $table->integer('deleted_at')->default(0);
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
		Schema::drop('ims_yz_micro_shop_bonus_log');
	}

}
