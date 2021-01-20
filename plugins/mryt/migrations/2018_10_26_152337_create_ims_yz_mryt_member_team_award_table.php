<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMrytMemberTeamAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_mryt_member_team_award')) {
            Schema::create('yz_mryt_member_team_award',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->default(0)->index('idx_uniacid');
                    $table->integer('uid')->default(0)->index('idx_uid')->comment('获得奖励的会员');
                    $table->integer('level_id')->default(0)->comment('等级id');
                    $table->integer('source_uid')->default(0)->index('idx_source_uid')->comment('来源会员');
                    $table->boolean('award_type')->default(0)->comment('1团队奖2感恩奖');
                    $table->decimal('level_team_award_amount',
                        10)->default(0.00)->comment('等级团队奖励金额');
                    $table->decimal('lower_level_team_award_amount',
                        10)->default(0.00)->comment('下级等级团队奖励金额');
                    $table->decimal('level_gratitude_amount',
                        10)->default(0.00)->comment('等级感恩奖金额');
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
		Schema::drop('ims_yz_mryt_member_team_award');
	}

}
