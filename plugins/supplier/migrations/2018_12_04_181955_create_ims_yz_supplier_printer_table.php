<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierPrinterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier_printer')) {
            Schema::create('yz_supplier_printer', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('mp_id')->default(0)->comment('more_printer.id');;
                $table->integer('user_uid')->default(0)->comment('微擎会员id');;
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
		Schema::dropIfExists('yz_supplier_printer');
	}

}
