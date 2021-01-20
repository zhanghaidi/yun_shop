<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzVideoCourseGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_video_course_goods')) {
            Schema::create('yz_video_course_goods', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid')->nullable();
                $table->integer('goods_id')->nullable();
                $table->string('goods_title')->nullable()->comment('商品名称');
                $table->boolean('is_course')->nullable()->comment('是否开启课程');
                $table->integer('lecturer_id')->nullable();
                $table->string('lecturer_name')->nullable()->comment('讲师姓名');
                $table->boolean('is_reward')->nullable()->comment('是否打赏');
                $table->text('see_levels')->nullable()->comment('会员等级权限 （会员等级ID） 0全部等级 ');
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
		Schema::drop('ims_yz_video_course_goods');
	}

}
