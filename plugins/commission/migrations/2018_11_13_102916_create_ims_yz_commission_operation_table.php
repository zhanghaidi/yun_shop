<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCommissionOperationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_commission_operation')) {
            Schema::create('yz_commission_operation',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->nullable();
                    $table->integer('order_id')->nullable();
                    $table->integer('uid')->nullable();
                    $table->integer('buy_uid')->nullable();
                    $table->integer('level_id')->nullable();
                    $table->decimal('ratio', 10)->nullable();
                    $table->text('content', 65535)->nullable();
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
		Schema::drop('ims_yz_commission_operation');
	}

}
