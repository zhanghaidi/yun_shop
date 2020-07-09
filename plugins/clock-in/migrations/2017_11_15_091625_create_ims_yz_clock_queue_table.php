<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzClockQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_clock_queue')) {
			Schema::create('yz_clock_queue', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->nullable();
				$table->decimal('day_before_amount', 10)->nullable()->comment('前一天奖金池总额');
				$table->decimal('rate', 10)->nullable()->comment('奖金发放比例');
				$table->decimal('amount', 10)->nullable()->comment('总发放金额');
				$table->integer('pay_num')->nullable()->comment('前一天支付人数');
				$table->integer('clock_in_num')->nullable()->comment('打卡人数');
				$table->integer('not_clock_in_num')->nullable()->comment('未打卡人数');
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
		Schema::drop('ims_yz_clock_queue');
	}

}
