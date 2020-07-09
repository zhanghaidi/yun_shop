<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzHelpCenterContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_help_center_content')) {
			Schema::create('yz_help_center_content', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('uniacid')->default(0);
				$table->integer('sort')->default(0)->comment('排序');
				$table->string('title')->nullable();
				$table->text('content', 65535)->nullable();
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
		Schema::drop('ims_yz_help_center_content');
	}

}
