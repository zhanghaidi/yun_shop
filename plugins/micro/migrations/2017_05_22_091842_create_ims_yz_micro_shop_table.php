<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMicroShopTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_micro_shop')) {
            Schema::create('yz_micro_shop', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('member_id')->default(0);
                $table->integer('level_id')->default(0);
                $table->string('shop_name', 100)->nullable()->default('0');
                $table->string('shop_avatar')->nullable()->default('0');
                $table->string('signature', 500)->nullable()->default('0');
                $table->string('shop_background')->nullable()->default('0');
                $table->boolean('status')->default(0);
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
		Schema::drop('ims_yz_micro_shop');
	}

}
