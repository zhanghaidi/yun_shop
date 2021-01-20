<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzNominateLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_nominate_level')) {
            Schema::create('yz_nominate_level',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('level_id')
                        ->nullable()->comment('member_level.id');
                    $table->integer('nominate_prize')
                        ->nullable()->comment('推荐奖-元');
                    $table->integer('team_prize')
                        ->nullable()->comment('团队奖-元');
                    $table->decimal('team_manage_prize',
                        10)
                        ->nullable()->comment('团队管理奖-%');
                    $table->text('task', 65535)
                        ->nullable()->comment('任务');
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
		Schema::drop('yz_nominate_level');
	}

}
