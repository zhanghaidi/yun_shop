<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoSlideTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_slide')) {
            Schema::create('yz_video_slide', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->string('slide_name')->nullable()->comment('名称');
                $table->string('link')->nullable()->comment('链接');
                $table->string('thumb')->nullable()->comment('图片');
                $table->integer('display_order')->nullable()->comment('排序');
                $table->boolean('status')->nullable()->comment('状态0：禁用1：启用');
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
		Schema::drop('ims_yz_video_slide');
	}

}
