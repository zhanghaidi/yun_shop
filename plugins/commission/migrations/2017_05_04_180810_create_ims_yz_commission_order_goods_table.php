<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImsYzCommissionOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_commission_order_goods')) {
            Schema::create('yz_commission_order_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('commission_order_id')->nullable();
                $table->string('name')->nullable();
                $table->string('thumb')->nullable();
                $table->boolean('has_commission')->nullable();
                $table->integer('commission_rate')->nullable();
                $table->decimal('commission_pay', 14)->nullable();
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
		Schema::dropIfExists('yz_commission_order_goods');
	}

}
