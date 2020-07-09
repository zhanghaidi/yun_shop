<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzWithdrawRelationOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_withdraw_relation_order')) {
            Schema::create('yz_withdraw_relation_order', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('withdraw_id')->nullable()->default(0);
                $table->integer('order_id')->nullable()->default(0);
                $table->integer('created_at')->nullable()->default(0);
                $table->integer('updated_at')->nullable()->default(0);
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
		Schema::dropIfExists('yz_withdraw_relation_order');
	}

}
