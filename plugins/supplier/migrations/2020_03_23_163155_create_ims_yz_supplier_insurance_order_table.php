<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierInsuranceOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier_insurance_order')) {
            Schema::create('yz_supplier_insurance_order',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('order_id');
                    $table->integer('ins_id')->comment('保单id');
                    $table->integer('supplier_id')->nullable();
                    $table->boolean('has_withdraw')->default(0)->comment('已提现');
                    $table->boolean('has_settlement')->default(0);
                    $table->decimal('settlement_days', 10)->default(0.00);
                    $table->decimal('amount', 10)->default(0.00);
                    $table->decimal('fee', 10)->default(0.00);
                    $table->decimal('fee_percentage', 10)->default(0.00);
                    $table->integer('updated_at')->nullable();
                    $table->integer('created_at')->nullable();
                    $table->integer('deleted_at')->nullable();
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
		Schema::drop('yz_plugin_cashier_order');
	}

}
