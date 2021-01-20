<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzTbkCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_tbk_category')) {
            Schema::create('yz_tbk_category', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('sid')->unsigned()->default(0)->comment('子分类ID');
                $table->integer('parent_id')->unsigned()->default(0)->comment('上级主分类ID');
                $table->integer('parent_sid')->unsigned()->default(0)->comment('上级子分类ID');
                $table->integer('level')->unsigned()->default(0)->comment('级别');
                $table->string('name')->default('')->comment('名称');
                $table->string('spell')->default('')->comment('简拼');
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
		Schema::drop('ims_yz_tbk_category');
	}

}
