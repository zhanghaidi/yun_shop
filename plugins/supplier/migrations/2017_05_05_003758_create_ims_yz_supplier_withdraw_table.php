<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierWithdrawTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier_withdraw')) {
            Schema::create('yz_supplier_withdraw', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('member_id')->default(0);
                $table->integer('supplier_id')->default(0);
                $table->boolean('status')->default(0);
                $table->decimal('money', 14)->default(0.00);
                $table->text('order_ids')->nullable();
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('deleted_at')->default(0);
                $table->integer('uniacid')->nullable()->default(0);
                $table->string('apply_sn', 50)->nullable()->default('0');
                $table->boolean('type')->nullable()->default(0);
                $table->integer('pay_time')->nullable()->default(0);
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
		Schema::dropIfExists('yz_supplier_withdraw');
	}

}
