<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMrytMemberReferralAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_mryt_member_referral_award')) {
            Schema::create('yz_mryt_member_referral_award',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->default(0)->index('idx_uniacid');
                    $table->integer('uid')->default(0)->index('idx_uid')->comment('获得奖励的会员');
                    $table->integer('source_uid')->default(0)->index('idx_source_uid')->comment('来源会员');
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
		Schema::drop('ims_yz_mryt_member_referral_award');
	}

}
