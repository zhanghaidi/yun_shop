<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzNominateGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_nominate_goods')) {
            Schema::create('yz_nominate_goods',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('goods_id')
                        ->nullable();
                    $table->boolean('is_open')
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
		Schema::drop('yz_nominate_goods');
	}

}
