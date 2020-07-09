<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCommissionLoseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_commission_lose')) {
            Schema::create('yz_commission_lose',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('award_id')->nullable()->comment('分销奖励id');
                    $table->decimal('amount_seal', 10)->nullable()->comment('封顶金额');
                    $table->decimal('today_commission', 10)->nullable()->comment('今日分销佣金');
                    $table->decimal('today_team_dividend', 10)->nullable()->comment('今日经销商奖励');
                    $table->decimal('today_share', 10)->nullable()->comment('今日共享奖');
                    $table->decimal('should_amount', 10)->nullable()->comment('应该获得奖励金额');
                    $table->decimal('reality_amount', 10)->nullable()->comment('实际奖励金额');
                    $table->integer('created_at')
                        ->nullable();
                    $table->integer('updated_at')
                        ->nullable();
                    $table->integer('deleted_at')
                        ->nullable();
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
		Schema::drop('yz_commission_lose');
	}

}
