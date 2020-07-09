<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzClockContinuityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_clock_continuity')) {
			Schema::create('yz_clock_continuity', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('uniacid')->nullable();
				$table->integer('member_id')->nullable();
				$table->integer('clock_num')->nullable();
				$table->integer('last_clock_at')->nullable();
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
		Schema::drop('ims_yz_clock_continuity');
	}

}
