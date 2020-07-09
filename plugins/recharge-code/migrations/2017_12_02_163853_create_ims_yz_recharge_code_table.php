<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzRechargeCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_recharge_code')) {
            Schema::create('yz_recharge_code', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->default(0);
                $table->integer('uid')->default(0);
                $table->boolean('type')->default(0);
                $table->decimal('price', 10)->default(0.00);
                $table->integer('end_time')->default(0)->index('end_time');
                $table->string('code_key', 100)->default('')->index('code_key');
                $table->boolean('is_bind')->default(0);
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
		Schema::drop('ims_yz_recharge_code');
	}

}
