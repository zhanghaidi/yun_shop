<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier_goods')) {
            Schema::create('yz_supplier_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('goods_id')->default(0);
                $table->integer('supplier_id')->default(0);
                $table->integer('member_id')->default(0);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('deleted_at')->default(0);
                $table->integer('uniacid')->nullable();
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
		Schema::dropIfExists('yz_supplier_goods');
	}

}
