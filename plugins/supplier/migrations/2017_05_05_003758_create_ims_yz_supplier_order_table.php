<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier_order')) {
            Schema::create('yz_supplier_order', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->default(0);
                $table->integer('supplier_id')->default(0);
                $table->integer('member_id')->default(0);
                $table->boolean('apply_status')->default(0);
                $table->decimal('supplier_profit', 14)->default(0.00);
                $table->string('order_goods_information')->default('0');
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
		Schema::dropIfExists('yz_supplier_order');
	}

}
