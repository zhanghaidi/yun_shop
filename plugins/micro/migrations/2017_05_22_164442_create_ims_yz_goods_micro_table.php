<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzGoodsMicroTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_goods_micro')) {
            Schema::create('yz_goods_micro', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->default(0);
                $table->boolean('is_open_bonus')->default(0);
                $table->boolean('independent_bonus')->default(0);
                $table->text('set', 65535);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('deleted_at')->default(0);
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
		Schema::drop('ims_yz_goods_micro');
	}

}
