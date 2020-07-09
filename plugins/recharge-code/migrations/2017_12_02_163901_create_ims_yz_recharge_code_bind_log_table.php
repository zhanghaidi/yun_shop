<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzRechargeCodeBindLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_recharge_code_bind_log')) {
            Schema::create('yz_recharge_code_bind_log',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->default(0);
                    $table->integer('code_id')->default(0);
                    $table->integer('uid')->default(0);
                    $table->integer('bind_time')->default(0);
                    $table->text('code_information', 65535);
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
		Schema::drop('ims_yz_recharge_code_bind_log');
	}

}
