<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPrinterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_printer')) {
            Schema::create('yz_printer', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->string('title')->default('');
                $table->string('user')->default('');
                $table->string('ukey')->default('');
                $table->string('printer_sn')->default('');
                $table->integer('times')->default(0);
                $table->integer('owner')->default(1);
                $table->integer('owner_id')->nullable()->default(0);
                $table->boolean('status')->default(0);
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
		Schema::drop('ims_yz_printer');
	}

}
