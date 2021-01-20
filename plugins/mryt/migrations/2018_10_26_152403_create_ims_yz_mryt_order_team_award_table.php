<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMrytOrderTeamAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_mryt_order_team_award')) {
            Schema::create('yz_mryt_order_team_award',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->default(0)->index('idx_uniacid');
                    $table->integer('uid')->default(0)->index('idx_uid')->comment('获得奖励的会员');
                    $table->integer('level_id')->default(0)->index('idx_level_id')->comment('等级id');
                    $table->integer('log_uid')->default(0)->index('idx_log_uid')->comment('获得销售佣金的会员');
                    $table->integer('log_id')->default(0)->comment('销售佣金记录id');
                    $table->decimal('log_amount',
                        10)->default(0.00)->comment('销售佣金 金额');
                    $table->decimal('award_ratio',
                        10)->default(0.00)->comment('奖励比例   (等级比例 减去 下级奖励比例 = 0 不获得)');
                    $table->decimal('lower_award_ratio',
                        10)->default(0.00)->comment('下级奖励比例');
                    $table->decimal('amount',
                        10)->default(0.00)->comment('奖励金额');
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
		Schema::drop('ims_yz_mryt_order_team_award');
	}

}
