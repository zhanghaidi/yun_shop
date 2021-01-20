<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzNominateUserTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_nominate_user_task')) {
            Schema::create('yz_nominate_user_task',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uid')
                        ->nullable();
                    $table->integer('level_id')
                        ->nullable();
                    $table->integer('task_level_id')
                        ->nullable();
                    $table->boolean('type')
                        ->nullable()->comment('1奖励现金2奖励时间');
                    $table->boolean('status')
                        ->nullable();
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
		Schema::drop('yz_nominate_user_task');
	}

}
