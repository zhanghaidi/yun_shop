<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMrytTierAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_mryt_tier_award')) {
            Schema::create('yz_mryt_tier_award', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('uid')->default(0);
                $table->integer('source_uid')->default(0)->comment('获得团队奖的UID');
                $table->decimal('amount', 10)->default(0.00)->comment('奖励金额');
                $table->integer('tier')->default(0)->comment('平级奖层级');
                $table->integer('level_id')->default(0)->comment('等级ID');
                $table->integer('level_tier')->default(0)->comment('等级设置平级奖层级');
                $table->integer('status')->default(0)->comment('0未提现1已提现');
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
		Schema::drop('ims_yz_mryt_tier_award');
	}

}
