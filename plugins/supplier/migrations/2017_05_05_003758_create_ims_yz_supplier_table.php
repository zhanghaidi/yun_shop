<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzSupplierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_supplier')) {
            Schema::create('yz_supplier', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('member_id')->default(0);
                $table->string('username', 50)->default('0');
                $table->string('password', 50)->default('0');
                $table->string('realname', 50)->default('0');
                $table->string('mobile', 50)->default('0');
                $table->boolean('status')->default(0);
                $table->integer('apply_time')->default(0);
                $table->integer('created_at')->default(0);
                $table->integer('updated_at')->default(0);
                $table->integer('deleted_at')->default(0);
                $table->integer('uniacid')->default(0);
                $table->string('salt')->default('0');
                $table->string('product', 100)->default('0');
                $table->string('remark', 255)->default('0');
                $table->string('store_name', 255)->default('null');

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
		Schema::dropIfExists('yz_supplier');
	}

}
