<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierDispatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier_dispatch')) {
            Schema::create('yz_supplier_dispatch', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('dispatch_id')->default(0);
                $table->integer('supplier_id')->default(0);
                $table->integer('member_id')->default(0);
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
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
		Schema::dropIfExists('yz_supplier_dispatch');
	}

}
