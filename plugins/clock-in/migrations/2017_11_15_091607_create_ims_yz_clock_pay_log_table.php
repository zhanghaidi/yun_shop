<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzClockPayLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_clock_pay_log')) {
			Schema::create('yz_clock_pay_log', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->nullable();
				$table->string('order_sn')->nullable();
				$table->integer('member_id')->nullable()->comment('会员ID');
				$table->decimal('amount', 10)->nullable()->comment('支付金额');
				$table->boolean('pay_method')->nullable()->comment('支付方式');
				$table->boolean('pay_status')->nullable()->comment('0未支付1支付成功');
				$table->boolean('clock_in_status')->nullable()->comment('打卡状态0未打卡1已打卡');
				$table->integer('clock_in_at')->nullable();
				$table->integer('queue_id')->nullable();
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
		Schema::drop('ims_yz_clock_pay_log');
	}

}
