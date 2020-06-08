<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzWechatProfitSharingLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_wechat_profit_sharing_log')) {
            Schema::create('yz_wechat_profit_sharing_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid')->nullable();
                $table->integer('order_id')->nullable();
                $table->string('mch_id')->nullable();
                $table->string('sub_mch_id')->nullable();
                $table->string('appid')->nullable();
                $table->string('sub_appid')->nullable();
                $table->integer('type')->nullable();
                $table->integer('account')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('out_order_no')->nullable();
                $table->string('description')->nullable();
                $table->integer('amount')->nullable();
                $table->integer('status')->nullable();
                $table->string('message')->nullable();
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
		Schema::drop('ims_yz_excel_recharge_detail');
	}

}
