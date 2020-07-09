<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCommissionManageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_commission_manage')) {
			Schema::create('yz_commission_manage', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->nullable();
				$table->integer('member_id')->nullable()->comment('获奖者');
				$table->integer('subordinate_id')->nullable()->comment('下级分销商');
				$table->decimal('subordinate_commission', 12)->nullable()->comment('下级佣金');
				$table->integer('hierarchy')->nullable()->comment('下级层级');
				$table->integer('manage_rage')->nullable()->comment('管理奖比例');
				$table->decimal('manage_amount', 12)->nullable()->comment('管理奖金额');
				$table->boolean('status')->nullable()->default(0)->comment('管理奖状态 0未提现 1已提现');
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
		Schema::drop('ims_yz_commission_manage');
	}

}
