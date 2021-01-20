<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTbkGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_tbk_goods')) {
            Schema::create('yz_tbk_goods', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->string('num_iid', 20)->nullable();
                $table->integer('goods_id')->nullable();
                $table->string('title', 400)->nullable();
                $table->integer('created_at')->nullable();
                $table->integer('deleted_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::drop('ims_yz_tbk_goods');
	}

}
