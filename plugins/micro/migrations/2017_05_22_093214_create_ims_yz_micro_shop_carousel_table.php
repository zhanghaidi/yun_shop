<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzMicroShopCarouselTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_micro_shop_carousel')) {
            Schema::create('yz_micro_shop_carousel',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('uniacid')->unsigned()->default(0);
                    $table->string('slide_name', 100)->default('0');
                    $table->string('link')->default('0');
                    $table->string('thumb')->default('0');
                    $table->integer('display_order')->unsigned()->default(0);
                    $table->boolean('enabled')->default(0);
                    $table->boolean('is_carousel')->default(0);
                    $table->integer('created_at')->unsigned()->default(0);
                    $table->integer('updated_at')->unsigned()->default(0);
                    $table->integer('deleted_at')->unsigned()->default(0);
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
		Schema::drop('ims_yz_micro_shop_carousel');
	}

}
