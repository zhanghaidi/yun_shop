<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMicroShopLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_micro_shop_level')) {
            Schema::create('yz_micro_shop_level', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('level_weight')->default(0)->comment('等级权重');
                $table->string('level_name')->default('0')->comment('等级名称');
                $table->decimal('bonus_ratio',
                    10)->default(0.00)->comment('分红比例');
                $table->integer('goods_id')->default(0)->comment('商品ID');
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
		Schema::drop('ims_yz_micro_shop_level');
	}

}
