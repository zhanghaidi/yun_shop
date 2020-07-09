<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImsYzPostByWechatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('yz_post_by_wechat')) {
            Schema::create('yz_post_by_wechat', function (Blueprint $table) {
                $table->increments('id');
                $table->text('file_path', 65535)->comment('海报完整路径');
                $table->string('media_id')->default('')->comment('微信返回mediaId');
                $table->integer('compare_at')->comment('比较更新时间');
                $table->integer('created_at');
                $table->integer('updated_at');
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
		Schema::dropIfExists('ims_yz_post_by_wechat');
	}

}
