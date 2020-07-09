<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDiyformDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_diyform_data')) {
			Schema::create('yz_diyform_data', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('uniacid')->default(0)->index('idx_uniacid');
				$table->integer('member_id')->default(0)->index('idx_typeid');
				$table->integer('form_id')->nullable();
				$table->text('data', 65535)->nullable();
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
		Schema::drop('ims_yz_diyform_data');
	}

}
