<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoLecturerRewardLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_lecturer_reward_log')) {
            Schema::create('yz_video_lecturer_reward_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('lecturer_id')->nullable()->comment('讲师ID');
                $table->integer('course_id')->nullable()->comment('课程ID');
                $table->string('order_sn')->nullable()->comment('单号');
                $table->decimal('order_price', 10)->nullable()->comment('订单金额');
                $table->boolean('reward_type')->nullable()->comment('奖励类型0：分红1：打赏');
                $table->decimal('amount', 10)->nullable()->comment('奖励金额');
                $table->boolean('status')->nullable()->comment('状态0：未结算 1：已结算');
                $table->integer('settle_days')->nullable()->comment('结算天数');
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
		Schema::drop('ims_yz_video_lecturer_reward_log');
	}

}
