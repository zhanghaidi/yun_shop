<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMrytOrderParentingAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_mryt_order_parenting_award')) {
            Schema::create('yz_mryt_order_parenting_award',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->default(0)->index('idx_uniacid');
                    $table->integer('uid')->default(0)->index('idx_uid')->comment('获得奖励的会员');
                    $table->integer('level_id')->default(0)->index('idx_level_id')->comment('等级id');
                    $table->integer('team_uid')->default(0)->index('idx_team_uid')->comment('获得团队管理奖的会员(order_team_award)');
                    $table->integer('team_log_id')->default(0)->comment('团队管理奖记录id');
                    $table->decimal('team_amount',
                        10)->default(0.00)->comment('团队管理奖 金额');
                    $table->decimal('parenting_ratio',
                        10)->default(0.00)->comment('育人奖励比例');
                    $table->decimal('amount',
                        10)->default(0.00)->comment('奖励金额(团队管理奖 金额 *育人奖励比例 )');
                    $table->boolean('status')->default(0)->comment('奖励状态');
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
		Schema::drop('ims_yz_mryt_order_parenting_award');
	}

}
