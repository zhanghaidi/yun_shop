<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzLoveTransferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_love_transfer')) {
            Schema::create('yz_love_transfer', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('uniacid');
                $table->integer('transfer');
                $table->decimal('change_value', 14);
                $table->integer('recipient');
                $table->integer('created_at');
                $table->integer('updated_at');
                $table->boolean('status');
                $table->string('order_sn', 45)->nullable();
                $table->decimal('poundage', 14)->nullable();
                $table->string('proportion', 11)->nullable();
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
		Schema::drop('ims_yz_love_transfer');
	}

}
