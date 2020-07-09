<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzLoveTradingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_love_trading')) {
			Schema::create('yz_love_trading', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->nullable();
				$table->integer('member_id')->nullable()->comment('出卖人ID');
				$table->integer('buy_id')->nullable()->comment('购买人ID');
				$table->boolean('status')->nullable()->comment('状态：0：出售中 1：已完成');
				$table->boolean('type')->nullable()->comment('类型：0：交易 1：公司回购');
				$table->decimal('amount', 12)->nullable()->comment('数量');
				$table->integer('poundage')->nullable();
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
		Schema::drop('ims_yz_love_trading');
	}

}
