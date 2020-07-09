<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzClockRewardLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_clock_reward_log')) {
			Schema::create('yz_clock_reward_log', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->nullable();
				$table->integer('member_id')->nullable();
				$table->decimal('amount', 10)->nullable();
				$table->boolean('status')->nullable();
				$table->integer('pay_id')->nullable();
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
		Schema::drop('ims_yz_clock_reward_log');
	}

}
