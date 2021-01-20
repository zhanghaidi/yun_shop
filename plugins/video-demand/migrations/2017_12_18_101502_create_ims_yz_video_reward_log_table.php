<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoRewardLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_reward_log')) {
            Schema::create('yz_video_reward_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('member_id')->nullable()->comment('打赏人ID');
                $table->integer('goods_id')->nullable()->comment('商品ID');
                $table->integer('lecturer_id')->nullable()->comment('讲师ID');
                $table->decimal('amount', 10)->nullable()->comment('打赏金额');
                $table->string('order_sn')->nullable()->comment('单号');
                $table->boolean('pay_method')->nullable()->comment('支付方式');
                $table->boolean('pay_status')->nullable()->comment('支付状态0：未支付1：已支付');
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
		Schema::drop('ims_yz_video_reward_log');
	}

}
