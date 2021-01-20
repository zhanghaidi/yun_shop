<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzNominateTeamPrizeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_nominate_team_prize')) {
            Schema::create('yz_nominate_team_prize',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')
                        ->nullable();
                    $table->integer('uid')
                        ->nullable();
                    $table->integer('level_id')
                        ->nullable();
                    $table->integer('order_id')
                        ->nullable();
                    $table->integer('goods_id')
                        ->nullable();
                    $table->decimal('ratio',
                        10)
                        ->nullable();
                    $table->decimal('amount',
                        10)
                        ->nullable();
                    $table->boolean('status')
                        ->nullable()->comment('0待发放1已发放');
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
		Schema::drop('yz_nominate_team_prize');
	}

}
