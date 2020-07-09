<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzDiyformTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('yz_diyform_type')) {
			Schema::create('yz_diyform_type', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('uniacid')->default(0)->index('idx_uniacid');
				$table->string('title')->default('')->comment('表单标题');
				$table->text('fields', 65535)->comment('表单内容');
				$table->boolean('status')->nullable()->default(1)->comment('状态');
				$table->string('success', 500)->nullable()->comment('提交成功提示文本');
				$table->integer('submit_number')->nullable()->comment('允许提交次数');
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
		Schema::drop('ims_yz_diyform_type');
	}

}
